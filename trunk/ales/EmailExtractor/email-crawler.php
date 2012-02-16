

<html><title>Crawler</title>
<body>
<div class="form" style="width:600px; margin-right: auto;margin-left: auto; border:1 solid silver;padding:10px;">
<br /><br /><form method=post action="#">
	<center><input type="text" name="urlname" style="margin-right: auto;margin-left: auto; width:500px;font-size: 1.2em;font-color: blue;">
	<input type="submit"></center>
</form>
</div>
</body></html>

<?
if ($_REQUEST['urlname'] && strlen(trim($_REQUEST['urlname']))>0)
    {
        $starturl = trim($_REQUEST['urlname']);
    }
    else
    {
        die('<center>Please enter valid start url</center>');
    }
?>
<?php

set_time_limit (600);
//$starturl = "http://vechorka.ru/contacts";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$starturl);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    $result=curl_exec ($ch);
    curl_close ($ch);
    // Search The Results From The Starting Site
    if( $result )
    {
        $arrurl = split("/",$starturl);
        $rooturl = ($arrurl[0]."//".$arrurl[2]);
        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        preg_match_all("/$regexp/siU", $result, $output, PREG_SET_ORDER );
  
        // array with all links on the page
        $url_array = array();
        
        $i=1;
        foreach( $output as $item )
        {
            if (!strstr($item[2],"http"))
            {
                $item[2] = $rooturl.$item[2];
            }
        echo "<font color='#3399CC'>".$i.".</font> ".$item[2]."; ";
        array_push($url_array,$item[2]);
        $i++;
        } 
        //echo print_r($url_array);
        

echo "<br><br>Emails found:<br><br>";

		// vizit all links
        foreach( $url_array as $item )
        {
                $ch1 = curl_init();
                curl_setopt($ch1, CURLOPT_URL,$item);
                curl_setopt($ch1, CURLOPT_TIMEOUT, 10); //timeout after 30 seconds
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER,1);
                $result=curl_exec ($ch1);
                curl_close ($ch1);
            
            $email = get_email_from_text ($result);
            fw($email);

            print($email);
        }
echo "<br>";
    }
    function get_email_from_text ($text){
        
        // parse emails
        $emails_string = '';
        if (!empty($text)) {
          $res = preg_match_all(
            "/[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}/i",
            $text,
            $matches
          );

          if ($res) {
            foreach(array_unique($matches[0]) as $email) 
			{
				if (!empty($email)) {
				  $emails_string .= $email.', ';
				  echo $email . ", ";
				}
            }
          }
          else {
            //echo "No emails found.";
          }
        }
        return $emails_string;
    }

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
		if (strlen(trim($text)>0))
		{
			$fp = fopen('_email_list.txt', 'a');
			fwrite($fp, $text);
			fclose($fp);
		}
    }
    
    ?>
