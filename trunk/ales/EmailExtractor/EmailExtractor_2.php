<?php
include_once('simple_html_dom.php');

set_time_limit (360);

###############################################################
    # Email Extractor 1.0
    ###############################################################
    # Visit http://www.zubrag.com/scripts/ for updates
    ############################################################### 

$html_urls = read_file("Z:\ales\EmailExtractor\press_sites.html");

preg_match_all ("/a[\s]+[^>]*?href[\s]?=[\s\"\']+".
                "(.*?)[\"\']+.*?>"."([^<]+|.*?)?<\/a>/",
                $html_urls, $matches);

/*
preg_match_all ("/a[\s]+[^>]*?href[\s]?=[\s\"\']+".
                "(.*?)[\"\']+.*?>"."([^<]+|.*?)?<\/a>/",
                $html_urls, &$matches);
*/
/*
preg_match_all ("\b(([\w-]+://?|www[.])[^\s()<>]+".
                "(?:\([\w\d]+\)|([^[:punct:]\s]|/)))",
                $html_urls, &$matches);
*/
                
// Create DOM from URL or file
    //$html = file_get_html('http://www.btk-online.ru/fullindex/?subr=2042');
    //echo file_get_html('http://www.btk-online.ru/fullindex/?subr=2042')->plaintext;

    // Find all links containing title as part of their HREF 
    //$links = $html->find('a[href*=title]');


    //$ret = $html->find('doc');
    /*
        // loop through links and do stuff
        foreach($links as $link) { 
               echo $element->href . '<br>';
        }
*/

$matches = $matches[1];
$list = array();
/*
foreach ($matches as $var)
{
    $html_urls = read_file($var);
    preg_match_all ("/a[\s]+[^>]*?href[\s]?=[\s\"\']+".
                "(.*?)[\"\']+.*?>"."([^<]+|.*?)?<\/a>/",
                $html_urls, $matches);
    $matches = $matches[1];
    $list = array();
}
*/

foreach($matches as $var)
{    
    $email = get_email_from_url ($var);
    fw($email);
    print($var."<br>");
}

?>
<!--
<form method="post">
  Please enter full URL of the page to parse (including http://):<br />
  <input type="text" name="url" size="65" value="<?php echo $the_url;  ?>"/><br />
  or enter text directly into textarea below:<br />
  <textarea name="text" cols="50" rows="15"></textarea>
  <br />
  <input type="submit" value="Parse Emails" />
</form>
-->
<?php

function get_email_from_url ($url){
    if (isset($url)) 
        {
            if (strstr($url,'http://')){
              // fetch data from specified url
              $context = stream_context_create(array('http' => array('header'=>'Connection: close'))); 
              $text = file_get_contents($url);
                           
              //$text = file_get_html($url);
              //echo file_get_html('http://www.btk-online.ru/fullindex/?subr=2042')->plaintext;
            }
        }
    
    // parse emails
    $emails_string = '';
    if (!empty($text)) {
      $res = preg_match_all(
        "/[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}/i",
        $text,
        $matches
      );

      if ($res) {
        foreach(array_unique($matches[0]) as $email) {
            $emails_string .= $email.', ';
          echo $email . "<br />";
        }
      }
      else {
        echo "No emails found.";
      }
    }
    return $emails_string;
}

function read_file($filename){
    if(file_exists($filename))
    {
        $html = implode('href=\"', file($filename));
    }
    return $html;
}

function fw($text)
{
    $fp = fopen('_email_list.txt', 'a');
    fwrite($fp, $text);
    fclose($fp);
}

?>