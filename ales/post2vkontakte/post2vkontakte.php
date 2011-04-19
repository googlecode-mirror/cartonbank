<?php    

$path = "/home/user"; // ����, ��� ������ ����� ������� ���� � �������, � ����� ����

//� ����� ���� �������� ��� ������ ����: 1) ID ���������� ����� 2) Activity Hash, ����������� ��� ��������� ������� ���������
$data = file_exists("$path/data.inc") ? unserialize(implode('',file("$path/data.inc"))) : array();

//��� ���������� ������, ����� ������� ����� �������������

$users = array(
  array('VK E-Mail','VK Password','Twitter Login','Twitter Password'),
);

//------------------------------------------------------------------------------

$ch_vk = new cURL(false);
$ch_twi = new cURL(false); 
$xml = xml_parser_create();

foreach ($users as $i => $v) {
  
  $ch_vk->cookie("$path/$v[0].txt"); //������������� ������ ���� ��� ��������.
  
  debug("$v[0]:");
  
  if (!filesize("$path/$v[0].txt") || !$data[$v[0]]['ahash']) { //�������������� ���������, ���� ���� ������.
    debug(" �������������� ���������...");
    $r = $ch_vk->post('http://login.vk.com/',"act=login&try_to_login=1&email=$v[0]&pass=$v[1]");
    if (preg_match("/<input type='hidden' name='s' id='s' value='(.*?)' \/>/si",$r,$m)) {
      $ch_vk->post('http://vkontakte.ru/login.php',"op=slogin&redirect=0&s=$m[1]");
      sleep(1);
      $r = $ch_vk->get('http://vkontakte.ru/'); //������� Activity Hash
      if (preg_match("/<a href='\/login.php'>/si",$r,$m)) { debug("����������� �� ������."); continue; }
      if (preg_match("/<input type='hidden' id='activityhash' value='(.*?)'>/si",$r,$m)) { $data[$v[0]]['ahash'] = $m[1]; debug("  Activity Hash: $m[1]"); } else { debug("  �� ������� �������� Activity Hash!"); continue; }
    }
  } else {
    if (!$data[$v[0]]['ahash']) continue;
  }
  
  $ch_twi->auth = "$v[2]:$v[3]"; //������ ����� � ������ ��� ��������
  
  //���� ��� �� ���� ���� �� �������������
  if (!$data[$v[0]]['lastid']) {
    debug(" ����������� ������ ����...");
    $r = $ch_twi->get("http://twitter.com/statuses/user_timeline/$v[2].xml?count=1"); //�������� �� ���������� �����, ����� � ����
    if (!$r) { debug("�� ������� �������� ������ ������!"); continue; }
    $d = new SimpleXMLElement($r); 
    if ($d->status) {
      $data[$v[0]]['lastid'] = (string)($d->status->id); //���������� �� ���������� �����
      vk_status($d->status->text,$d->status->source,$data[$v[0]]['ahash']);
      debug(" ok.");
    }
  } else {
    debug(" ���� ����� �����...");
    $r = $ch_twi->get("http://twitter.com/statuses/user_timeline/$v[2].xml?since_id=".$data[$v[0]]['lastid']); //�������� ��� ����� �����
    if (!$r) { debug("�� ������� �������� ������ ������!"); continue; }
    $d = new SimpleXMLElement($r);
    if (!$d->status) { debug("  ����� ���."); continue; }//��� ������
    if (count($d->status) > 1) { //���� ������ ���������
      debug(" ������ ����� �����..."); $i = 0;
      foreach ($d->status as $j => $s) {
      	if ($i == 0) { $data[$v[0]]['lastid'] = (string)$s->id; } else { sleep(65); } //��������. ��, ����� ������, ����� ������� ������ ������� ���������� ������ �� �����.
      	vk_status($s->text,$s->source,$data[$v[0]]['ahash']);
      	debug("  ".$s->id); $i++;
      }
    } else { //1 ����
      debug(" 1 ����� ����, ������...");
      $data[$v[0]]['lastid'] = (string)($d->status->id);
      vk_status($d->status->text,$d->status->source,$data[$v[0]]['ahash']);
    }
  }
  sleep(1);
  debug("OK.");
}

//��������� �������� � �������

$f = fopen("$path/data.inc",'w+');
fwrite($f,serialize($data));
fclose($f);

//------------------------------------------------------------------------------

function vk_status($text,$src,$hash) { //������� ��� ��������� �������
  global $ch_vk;
  $src = strip_tags($src);
  $text = (string)$text;
  if ($text[0] == '@') return false; //����������� ������. ������ ��� ����� ��������� ����� �� ��������
  $src = $src == 'web' ? 'via Twitter' : 'via '.$src; //������� �������� �������, ������ ������ ��� ����
  $ch_vk->post("http://vkontakte.ru/profile.php","setactivity=$text $src&activityhash=$hash");
}

//------------------------------------------------------------------------------

function debug($msg) {
  echo $msg."\n";
} 

//------------------------------------------------------------------------------
	
// Fast cURL Class

class cURL { 
  var $headers; 
  var $user_agent; 
  var $compression; 
  var $cookie_file; 
  var $proxy; 
  var $auth;
  
  function cURL($cookies=TRUE,$cookie='cookies.txt',$compression='gzip',$proxy='') { 
    $this->headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg'; 
    $this->headers[] = 'Connection: Keep-Alive'; 
    $this->headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8'; 
    $this->user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/530.5 (KHTML, like Gecko) Chrome/2.0.172.30 Safari/530.5'; 
    $this->compression=$compression; 
    $this->proxy=$proxy; 
    $this->cookies=$cookies; 
    if ($this->cookies == TRUE) $this->cookie($cookie); 
  } 
  
  function cookie($cookie_file) { 
    $this->cookies = true;
    if (file_exists($cookie_file)) { 
      $this->cookie_file=$cookie_file; 
    } else { 
      $f = fopen($cookie_file,'w');
      fclose($f);
      $this->cookie_file=$cookie_file; 
    } 
  } 
  
  function get($url) { 
    $process = curl_init($url); 
    curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers); 
    curl_setopt($process, CURLOPT_HEADER, 0); 
    curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent); 
    if ($this->cookies == TRUE) {
      curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file); 
      curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
     }
    if ($this->auth) curl_setopt($process, CURLOPT_USERPWD, $this->auth); 
    curl_setopt($process,CURLOPT_ENCODING , $this->compression); 
    curl_setopt($process, CURLOPT_TIMEOUT, 30); 
    if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy); 
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1); 
    $return = curl_exec($process); 
    curl_close($process); 
    return $return; 
  } 
  
  function post($url,$data) { 
    $process = curl_init($url); 
    curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers); 
    curl_setopt($process, CURLOPT_HEADER, 1); 
    curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent); 
    if ($this->cookies == TRUE) {
      curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file); 
      curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
    }
    if ($this->auth) curl_setopt($process, CURLOPT_USERPWD, $this->auth);  
    curl_setopt($process, CURLOPT_ENCODING , $this->compression); 
    curl_setopt($process, CURLOPT_TIMEOUT, 30); 
    if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy); 
    curl_setopt($process, CURLOPT_POSTFIELDS, $data); 
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1); 
    curl_setopt($process, CURLOPT_POST, 1); 
    $return = curl_exec($process); 
    curl_close($process); 
    return $return; 
  } 
  
}

?>