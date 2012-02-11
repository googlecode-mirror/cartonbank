<?php
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"http://www.advmarket.ru/catalog/outdoor_adv/?page=&num_page=1");
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    $result=curl_exec ($ch);
    curl_close ($ch);
    // Search The Results From The Starting Site
    if( $result )
    {
        // I LOOK ONLY FROM TOP domains change this for your usage 
        preg_match_all( '/<a href="(http:\/\/www.[^0-9].+?)"/', $result, $output, PREG_SET_ORDER );

        foreach( $output as $item )

        {
            // ALL LINKS DISPLAY HERE 
            //print_r($item);

            //$html_urls = read_file($item[1]);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$item[1]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout after 30 seconds
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                $result=curl_exec ($ch);
                curl_close ($ch);
                
            /*
            preg_match_all ("/a[\s]+[^>]*?href[\s]?=[\s\"\']+".
                "(.*?)[\"\']+.*?>"."([^<]+|.*?)?<\/a>/",
                $html_urls, $matches);
              */  
            //$matches = $matches[1];
            //$list = array();
            
            $email = get_email_from_text ($result);
            fw($email);
            print($email.", ");
            
            /*
            foreach($matches as $var)
            {    
                $email = get_email_from_url ($var);
                fw($email);
                print($var."<br>");
            }
            */

        }

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
