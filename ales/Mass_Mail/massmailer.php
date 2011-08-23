<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *  Welcome to phpMassMailer(phpMM)
 *
 *    About the program
 *
 *    phpMM is a program for mass dispatch of E-mail. If your CMS or a forum
 *    cannot send messages to users you can use it.
 *
 *    Free download from http://www.phpclasses.net
 *    Version 0.8, 23-08-2007
 *    Please, report bugs...
 *
 *    This programm for Unix/Windows system and PHP4 (or higest).
 *
 *    (c) Romanovsky Maxim , GPL,  romanovsky_m@yahoo.com
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

///////////////////////////////// S E T U P ///////////////////////////////////

$databases = array(
                   array("dbasa"     => "mysql",    //The identifier of a database mysql
                         "host"      => "localhost",// MySQL host
                         "db"        => "cartoonbankru",    // MySQL DataBase
                         "user"      => "z58365_cbru3",    // MySQL user
                         "pass"      => "greenbat",      // MySQL Password
                         "query"     => "SELECT mail FROM mymail where dont_send='0' LIMIT 1000 OFFSET 10100",// MySQL query
                         "imp_query" => "INSERT INTO mymail(mail)",
                         )
                 );

// Login and the password of access to the program:

$name               = "phpMM"; // Login

$pass               = "phpMM"; // Password

// $auth = 1;   Definition of authenticity is included
// $auth = 0;   Definition of authenticity is switched off

$auth               = 1;

// name of mail programm

$mailer   =     "phpMM";

// Strict check before sending

$checkBeforeSending = TRUE;

$charset_program    = "windows-1251";

// The coding of the message

$charset            = "windows-1251";

$version            = "23-08-2007";


///////////////////////////////////////////////////////////////////////////////


error_reporting(E_ALL);

set_time_limit(0);

if($auth == 1) {
if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER']!==$name || $_SERVER['PHP_AUTH_PW']!==$pass)
   {
   header('WWW-Authenticate: Basic realm="phpMM"');
   header('HTTP/1.0 401 Unauthorized');
   exit("<b>Access Denied</b>");
   }
}

class phpmm {

             var $use;

             var $whom;

             var $list;

             var $subj;

             var $mess;

             var $from;

             var $mailer;

             var $files;

             var $kod;

             var $number;

             var $send;

             var $host;

             var $db;

             var $user;

             var $pass;

             var $query;

             var $query_result;

             var $mysql_link;

             var $link_pg;

             var $link_ib;

             var $msql_link;

             var $fbsql_link;

             var $sqli_link;

             var $oci_con;

             var $sybase_link;

             var $link_ingres;

             var $dbfbasa;

             var $id;

             var $to;

             var $hostSmtp;

             var $portSmtp;

             var $timeoutSmtp;

             var $smtpServer;

             var $userSmtp;

             var $passSmtp;

             var $smtp;

             var $authenticate;

             var $CRLF = "\r\n";

             var $DOUBLE_LF = "\n\n";

             var $back;

              function phpmm($data) {
                      $this->dbfbasa = $data['dbasa'];
                      $this->host    = $data['host'];
                      $this->db      = $data['db'];
                      $this->user    = $data['user'];
                      $this->pass    = $data['pass'];
                      $this->query   = $data['query'];
                      $this->selectDataBase($this->query, $this->dbfbasa);
              }

              function selectDataBase($query, $dbfbasa){

                      switch($dbfbasa){

                               case 'pgsql':
                                    if(extension_loaded('pgsql')){
                                            $this->connect_pg($this->host, $this->db, $this->user, $this->pass);
                                            $this->query_pg($query);
                                    } else {
                                            print(nl2br("Functions pgsql are absent. \n"));
                                    }
                               break;

                               case 'mysql':
                                    if(extension_loaded('mysql')){
                                            $this->connect_mysql($this->host, $this->user, $this->pass, $this->db);
                                            $this->query_mysql($query);
                                    } else {
                                            print(nl2br("Functions mysql are absent. \n"));
                                    }
                               break;

                               case 'ibase':
                                    if(extension_loaded('interbase')){
                                            $this->connect_ib($this->host, $this->user, $this->pass);
                                            $this->query_ib($query);
                                    } else {
                                            print(nl2br("Functions ibase are absent. \n"));
                                    }
                               break;

                               case 'msql':
                                    if(extension_loaded('msql')){
                                            $this->connect_msql($this->host, $this->user, $this->pass, $this->db);
                                            $this->query_msql($query);
                                    } else {
                                            print(nl2br("Functions msql are absent. \n"));
                                    }
                               break;

                               case 'fbsql':
                                    if(extension_loaded('fbsql')){
                                            $this->connect_fbql($this->host, $this->user, $this->pass, $this->db);
                                            $this->query_fbsql($this->query);
                                    } else {
                                            print(nl2br("Functions fbsql are absent. \n"));
                                    }
                               break;

                               case 'sqli':
                                    if(extension_loaded('sqli')){
                                            $this->connect_sqli($this->db);
                                            $this->query_sqli($query);
                                    } else {
                                            print(nl2br("Functions msql are absent. \n"));
                                    }
                               break;

                               case 'oci':
                                    if(extension_loaded('oracle')){
                                            $this->connect_oci($this->user, $this->pass, $this->db);
                                            $this->query_oci($query);
                                    } else {
                                            print(nl2br("Functions oracle are absent. \n"));
                                    }
                               break;

                               case 'sybase':
                                    if(extension_loaded('sybase_ct')){
                                             $this->connect_sybase($this->user, $this->pass, $this->db);
                                             $this->query_sybase($query);
                                    } else {
                                            print(nl2br("Functions sybase are absent. \n"));
                                    }
                               break;

                               case 'ingres':
                                     if(extension_loaded('ingres')){
                                             $this->connect_ingres($db, $user, $pass);
                                             $this->query_ingres($query);
                                     } else {
                                             print(nl2br("Functions ingres are absent. \n"));
                                     }
                               break;


                      }


              }

              /*
                 Functions for work with a database MySQL.
              */

              function connect_mysql($host, $user, $pass, $db) {
                      $this->mysql_link = @mysql_connect($host, $user, $pass);
                      if ($this->mysql_link == 0){
                              print(nl2br("MySQL error: " . mysql_error() . "\n"));
                              return false;
                      }
                      if (!mysql_select_db($db,$this->mysql_link)) {
                              print(nl2br("Database Error " . mysql_error() . "\n"));
                              return false;
                      }
                      return $this->mysql_link;
              }

              function query_mysql($query) {
                       if (!$this->mysql_link) return false;
                       $this->query_result = mysql_query($query, $this->mysql_link);
                       if ($this->query_result) {
                               return $this->query_result;
                       } else {
                               print(nl2br("Error query:  "  .mysql_error()."\n"));
                               return false;
                       }
              }


              function disconnect_mysql($mysql_link) {
                      if($mysql_link) {
                              mysql_close($mysql_link);
                              $mysql_link = false;
                      } else {
                              return false;
                      }
              }

              /*
                 Functions for work with a database PostgreSQL.
              */

              function connect_pg($host, $db, $user, $pass) {
                      $this->link_pg = pg_connect("host = $host dbname = $db user = $user password = $pass");
                      if ($this->link_pg) {
                              return $this->link_pg;
                      } else {
                              print(nl2br("Could not connect.\n"));
                              return false;
                      }

              }


              function query_pg($query) {
                      if (!$this->link_pg) return false;
                      $this->query_result = @pg_query($this->link_pg, $query);
                      if($this->query_result) {
                              return $this->query_result;
                      } else {
                              print(nl2br(pg_last_error()."\n"));
                              return false;
                      }
              }

              function disconnect_pg($link_pg) {
                      if ($link_pg) {
                              pg_close($link_pg);
                              $link_pg = false;
                      } else {
                              return false;
                      }
              }

              /*
                 Functions for work with a database firebird.
              */

              function connect_ib($host, $user, $pass) {
                      $this->link_ib = @ibase_connect($host, $user, $pass);
                      if ($this->link_ib) {
                              return $this->link_ib;
                      } else {
                              print(nl2br("ibase error: ".ibase_errmsg(). "\n"));
                              return false;
                      }

              }

              function query_ib($query) {
                      if (!$this->link_ib) return false;
                      $this->query_result = ibase_query($this->link_ib, $query);
                      if ($this->query_result) {
                              return $this->query_result;
                      } else {
                              print(nl2br("ibase error: " . ibase_errmsg()."\n"));
                              return false;
                      }

              }

              function disconnect_ib($link_ib){
                      if($link_ib) {
                              ibase_close($link_ib);
                              $link_ib = false;
                      } else {
                              return false;
                      }
              }

              // Functions for work with mSQL a server

              function connect_msql($host, $user, $pass, $db) {
                      $this->msql_link = msql_connect($host, $user, $pass);
                      if ($this->msql_link == 0) {
                              return false;
                      }
                      if (!msql_select_db($db, $this->msql_link)) {
                              print("Database Error " . msql_error());
                              return false;
                      }
                      return $this->msql_link;
              }

              function query_msql($query) {
                       if (!$this->msql_link) return false;
                       $this->query_result = msql_query($query, $this->msql_link);
                       if ($this->query_result) {
                               return $this->query_result;
                       } else {
                               print(nl2br("Error query. \n"));
                               return false;
                       }
              }


              function disconnect_msql($msql_link) {
                      if($msql_link) {
                              msql_close($msql_link);
                              $msql_link = false;
                      } else {
                              return false;
                      }
              }

              // Functions for work with fbSQL a server

              function connect_fbql($host, $user, $pass, $db) {
                      $this->fbsql_link = fbsql_pconnect($host, $user, $pass);
                      if ($this->fbsql_link == 0) {
                              return false;
                      }
                      if (!fbsql_select_db($db, $this->fbsql_link)) {
                              print("Database Error " . msql_error(). "\n");
                              return false;
                      }
                      return $this->fbsql_link;
              }


              function query_fbsql($query) {
                       if (!$this->fbsql_link) return false;
                       $this->query_result = fbsql_query($query, $this->fbsql_link);
                       if ($this->query_result){
                               return $this->query_result;
                       } else {
                               print(nl2br("Error query. \n"));
                               return false;
                       }
              }


              function disconnect_fbsql($fbsql_link) {
                      if($fbsql_link) {
                              msql_close($fbsql_link);
                              $fbsql_link = false;
                      } else {
                              return false;
                      }
              }

              // Functions for work with SQLi a server

              function connect_sqli($db){
                      $this->sqli_link = sqlite_open($db, SQLITE_ASSOC, $sqliteerror);
                      if ($this->sqli_link){
                              return $this->sqli_link;
                      } else {
                              return false;
                      }
              }

              function query_sqli($query){
                      if (!$this->sqli_link) return false;
                      $this->query_result = sqlite_query($this->sqli_link, $query);
                      if ($this->query_result){
                              return $this->query_result;
                      } else {
                              print(nl2br("Error query. \n"));
                              return false;
                      }
              }


              function disconnect_sqli($sqli_link) {
                      if($sqli_link) {
                              sqlite_close($sqli_link);
                              $sqli_link = false;
                      } else {
                              return false;
                      }
              }

              // Functions for work with OCI a server

              function connect_oci($user, $pass, $db){
                      $this->oci_con = oci_connect($user, $pass, $db);
                      if($this->oci_con){
                              return $this->oci_con;
                      } else {
                              return false;
                      }

              }


              function query_oci($query){
                       if (!$this->oci_con) return false;
                       $this->stmt = oci_parse($this->oci_con, $query);
                       $this->query_result = oci_execute($this->stmt, OCI_COMMIT_ON_SUCCESS);
                       if ($this->query_result) {
                               return $this->query_result;
                       } else {
                               print(nl2br("Error query. \n"));
                               return false;
                       }
              }


              function disconnect_oci($oci_con){
                      if($oci_con) {
                              oci_commit($oci_con);
                              $oci_con = false;
                      } else {
                              return false;
                      }
              }

              // Functions for work with SYBASE a server

              function connect_sybase($host, $user, $pass, $db){
                      $this->sybase_link = sybase_connect($host, $user, $pass);
                      if ($this->sybase_link == 0){
                              return false;
                      }
                      if (!sybase_select_db($db, $this->sybase_link)){
                              print("Database Error. \n");
                              return false;
                      }
                      return $this->sybase_link;
              }


              function query_sybase($query){
                       if (!$this->sybase_link) return false;
                       $this->query_result = sybase_query($query, $this->sybase_link);
                       if ($this->query_result) {
                               return $this->query_result;
                       } else {
                               print(nl2br("Error query. \n"));
                               return false;
                       }
              }


              function disconnect_sybase($sybase_link){
                      if($sybase_link) {
                              sybase_close($sybase_link);
                              $sybase_link = false;
                      } else {
                              return false;
                      }
              }

              // Functions for work with Ingres a server

              function connect_ingres($db, $user, $pass){
                      $this->link_ingres = ingres_connect($db, $user, $pass);
                      if($this->link_ingres){
                              return $this->link_ingres;
                      } else {
                              print(nl2br("Could not connect. \n"));
                              return false;
                      }
              }

              function query_ingres($query){
                      if (!$this->link_ingres) return false;
                      $this->query_result = ingres_query($this->link_ingres, $query);
                      if($this->query_result){
                              return $this->query_result;
                      } else {
                              print(nl2br("Error query. \n"));
                              return false;
                      }
              }

              function disconnect_ingres($link_ingres){
                      if ($link_ingres) {
                              ingres_close($link_ingres);
                              $link_ingres = false;
                      } else {
                              return false;
                      }
              }

              function disconnect() {
                      $this->disconnect_mysql($this->mysql_link);
                      $this->disconnect_pg($this->link_pg);
                      $this->disconnect_ib($this->link_ib);
                      $this->disconnect_msql($this->msql_link);
                      $this->disconnect_fbsql($this->fbsql_link);
                      $this->disconnect_sqli($this->sqli_link);
                      $this->disconnect_oci($this->oci_con);
                      $this->disconnect_sybase($this->sybase_link);
                      $this->disconnect_ingres($this->link_ingres);
              }

              function getmicrotime() {
                      list($this->usec, $this->sec) = explode(" ", microtime());
                      return ((float)$this->usec + (float)$this->sec);
              }

              function back(){
                     $this->back = "<a href=\"javascript:history.go(-1);\">back</a>";
              }


              /*
                  Heading Mime 1.0
              */

              function getHeader() {

                 $un_bound = "phpMM".time();
                 $this->kod = ($this->kod == 1)?"text/plain":"text/html";
                 $this->headers  = "Date: ".date("d.m.y G:i:s")."\n";
                 $this->headers .= "MIME-Version: 1.0 \n";
                 $this->headers .= "From: ".$this->from."\n";
                 $this->headers .= "Reply-To: ".$this->from."\n";
                 if($this->smtp){$this->headers.= "To: " . $this->to."\n";}
                 if($this->smtp){$this->headers .= "Subject: ".$this->subj."\n";}
                 $this->headers .= "Content-Type: multipart/mixed; boundary=".$un_bound."\n";
                 $this->headers .= "X-Mailer: ". $this->mailer."\n";
                 $this->headers .= "X-Originating-Email: ".$this->from."\n";
                 $this->headers .=  base64_decode("WC1PcmlnaW5hdGluZy1JUDog").getenv(base64_decode('UkVNT1RFX0FERFI='))."\n";
                 $this->headers .= "Message-Id: "."phpMM".md5(@uniqid())."\n";

                 $this->EmailBody  = "--".$un_bound."\n";
                 $this->EmailBody .="Content-Type: ".$this->kod.";charset=".$this->charset."\n";
                 $this->EmailBody .="Content-Transfer-Encoding: 8bit".$this->DOUBLE_LF;
                 $this->EmailBody .=$this->mess;
                 if (count($this->files)>0){
                         for($i=0;$i<count($this->files);$i++) {
                                 $rfile = $this->files[$i];
                                 if(!($fp = @fopen($rfile, "r"))){
                                         break;
                                 }
                                 $this->text = chunk_split(base64_encode(fread($fp, filesize($rfile))));
                                 $this->EmailBody .= "\n--".$un_bound."\n";
                                 $this->EmailBody .= "Content-Type: application/octet-stream; \n";
                                 $this->EmailBody .= "Content-Transfer-Encoding: base64 \n";
                                 $this->EmailBody .= "Content-Disposition: attachment; filename = ".basename($rfile).$this->DOUBLE_LF;
                                 $this->EmailBody .= $this->text;
                         }
                 }
                 $this->EmailBody .= "\n--".$un_bound."--\n";

              }


              /*
                  Functions for work with Smtp a server.
              */


              function connectSmtp($host, $port, $timeout = 30) {
                      $this->back();
                      $this->socket = fsockopen($host, $port , $errno, $errstr, $timeout);
                      if (!$this->socket) {
                               echo(nl2br("ERROR: $errno - $errstr \n"));
                               Exit($this->back);
                      }  else {
                              //print(nl2br("Connection successfull \n"));
                              return $this->socket;
                      }
              }


              function disconnectSmtp($socket) {
                       if ($socket) {
                               fclose($socket);
                               // print(nl2br("Disconnection successfull \n"));
                       } else {
                               // print(nl2br("Disconnection failed."));
                       }
              }

              function getLine($socket) {
                      if (!$socket) {
                              return false;
                      } else {
                              $this->line = fgets($socket,1024);
                              if($this->line == NULL){
                                      return false;
                              } else {
                                      return $this->line;
                              }
                      }
              }


              function readALine($line, $n = 3) {
                      if($line) {
                              $this->response = substr($line, 0, $n);
                              return $this->response;
                      } else {
                              return false;
                      }

              }

              function execute($command){
                      if (!$this->socket) {
                              return false;
                      } else {
                              fputs($this->socket, $command);
                      }
              }


              function toCheckUpErrors($error) {

                      switch ($error) {

                              case '421': print(nl2br("Service not available, closing channel \n"));
                                  break;
                              case '432': print(nl2br("A password transition is needed \n"));
                                  break;
                              case '450': print(nl2br("Requested mail action not taken: mailbox unavailable \n"));
                                  break;
                              case '451': print(nl2br("Requested action aborted: error in processing \n"));
                                  break;
                              case '452': print(nl2br("Requested action not taken: insufficient system storage \n"));
                                  break;
                              case '454': print(nl2br("Temporary authentication failure \n"));
                                  break;
                              case '500': print(nl2br("Syntax error; command not recognized \n"));
                                  break;
                              case '501': print(nl2br("Syntax error in parameters or arguments \n"));
                                  break;
                              case '502': print(nl2br("Command not implemented \n"));
                                  break;
                              case '503': print(nl2br("Bad sequence of commands \n"));
                                  break;
                              case '504': print(nl2br("Command parameter not implemented \n"));
                                  break;
                              case '530': print(nl2br("Authentication required \n"));
                                  break;
                              case '534': print(nl2br("Authentication mechanism is too weak \n"));
                                  break;
                              case '535': print(nl2br("Authentication failed \n"));
                                  break;
                              case '538': print(nl2br("Encryption required for requested authentication mechanism \n"));
                                  break;
                              case '550': print(nl2br("Requested action not taken: mailbox unavailable \n"));
                                  break;
                              case '551': print(nl2br("User not local; please try forwarding \n"));
                                  break;
                              case '552': print(nl2br("Requested mail action aborted: exceeding storage allocation \n"));
                                  break;
                              case '553': print(nl2br("Requested action not taken: mailbox name not allowed \n"));
                                  break;
                              case '554': print(nl2br("Transaction failed \n"));
                                  break;
                              default:    print(nl2br("Unknown response \n"));
                                  break;

                      }

              }

              function checkSmtp($hostSmtp, $portSmtp, $authenticate, $userSmtp = null, $passSmtp = null){

                      $this->back();

                      if($this->smtp){
                              if(empty($hostSmtp)){
                                      print(nl2br("Specify SMTP a server. \n"));
                                      Exit($this->back);
                              }
                              if(!ereg('[1-9]', $portSmtp)){
                                      print(nl2br("Smtp the port is specified not correctly. \n"));
                                      Exit($this->back);
                              }
                              if(!empty($authenticate)){
                                      if(empty($userSmtp)){
                                              print(nl2br("You have not specified Smtp the user. \n"));
                                              Exit($this->back);
                                      }
                                      if(empty($passSmtp)){
                                              print(nl2br("You have not specified password Smtp. \n"));
                                              Exit($this->back);
                                      }
                              }
                      }

              }

              function smtpEhlo($socket) {
                        $this->execute("EHLO $this->hostSmtp".$this->CRLF);
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response, 220)){
                                $this->toCheckUpErrors($this->response);
                        }
                        $this->response = $this->readALine($this->getLine($this->socket),4);
                        if(strcmp($this->response, '250-')){
                                $this->toCheckUpErrors($this->response);
                                return false;
                        } else {
                                return true;
                        }
              }

              function smtpHelo($socket) {
                      $this->execute("HELO $this->hostSmtp".$this->CRLF);
                      $this->response = (integer) $this->readALine($this->getLine($this->socket));
                      if(strcmp($this->response, 220)){
                                $this->toCheckUpErrors($this->response);
                      }
                      $this->response = $this->readALine($this->getLine($this->socket),4);
                      if(strcmp($this->response, '250 ')){
                                $this->toCheckUpErrors($this->response);
                                return false;
                      } else {
                              return true;
                      }
              }

              function smtpAuth($authenticate) {

                      $this->back();

                      switch($authenticate) {

                      case '':

                        if(strcmp($this->authenticate,"auto") && $this->smtpServer == "esmtp"){
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response,250)){
                                $this->toCheckUpErrors($this->response);
                        }
                        }

                        break;

                      case 'LOGIN':

                        $this->login = base64_encode($this->userSmtp);
                        $this->password = base64_encode($this->passSmtp);
                        $this->execute("AUTH LOGIN" . $this->CRLF);
                        if($this->smtpServer == 'esmtp'){ // esmtp server response
                        $this->response = $this->getLine($this->socket);
                        if(!stristr($this->response, "LOGIN")){
                                $this->disconnectSmtp($this->socket);
                                print(nl2br("The server does not support the given method of an establishment of authenticity. \n"));
                                Exit($this->back);
                        }
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response,250)){
                                $this->toCheckUpErrors($this->response);
                        }
                        }
                        $this->execute($this->login . $this->CRLF);
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response,334)){
                                $this->toCheckUpErrors($this->response);
                        }
                        $this->execute($this->password . $this->CRLF);
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response,334)){
                                $this->toCheckUpErrors($this->response);
                        }
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response,235)){
                                $this->toCheckUpErrors($this->response);
                        }


                        break;

                        case 'PLAIN':

                        $this->auth = base64_encode("$this->userSmtp\0$this->userSmtp\0$this->passSmtp");
                        $this->execute("AUTH PLAIN".$this->CRLF);
                        if($this->smtpServer == 'esmtp'){  // esmtp server response
                        $this->response = $this->getLine($this->socket);
                        if(!stristr($this->response, "PLAIN")){
                                $this->disconnectSmtp($this->socket);
                                print(nl2br("The server does not support the given method of an establishment of authenticity. \n"));
                                Exit($this->back);
                        }
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response, 250)){
                                $this->toCheckUpErrors($this->response);
                        }
                        }
                        $this->execute("$this->auth".$this->CRLF);
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response,334)){
                                $this->toCheckUpErrors($this->response);
                        }
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response, 235)){
                                $this->toCheckUpErrors($this->response);
                        }

                        break;

                        }


              }

              function sendSmtp($authenticate) {

                $this->getHeader();

                        $this->execute("MAIL FROM:<$this->from>".$this->CRLF);
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response, 250)){
                                $this->toCheckUpErrors($this->response);
                        }
                        $this->execute("RCPT TO:<$this->to>".$this->CRLF);
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response, 250)){
                                $this->toCheckUpErrors($this->response);
                        }
                        $this->execute("DATA".$this->CRLF);
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response, 354)){
                                $this->toCheckUpErrors($this->response);
                        }
                        $this->execute($this->headers."\n". $this->EmailBody."\n");
                        $this->execute($this->CRLF.".".$this->CRLF);
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response, 250)){
                                $this->toCheckUpErrors($this->response);
                        }

              }

              function smtpQuit($socket) {
                        $this->execute("QUIT".$this->CRLF);
                        $this->response = (integer) $this->readALine($this->getLine($this->socket));
                        if(strcmp($this->response, 221)){
                                $this->toCheckUpErrors($this->response);
                        }
              }

              /*
                  The end of functions for work with Smtp a server.
              */

              function readData($tos){
                      foreach($tos as $this->to){
                              $this->to = trim($this->to);
                              for($i=1;$i<=1;$i++){
                                      if($this->send == "stop"){
                                              break;
                                      }
                                      if($this->checkBeforeSending){
                                              if(!$this->checkEmail($this->to)){
                                                      if(!empty($this->to)){
                                                              print(nl2br("Could not send the message: $this->to \n"));
                                                      }
                                                      continue;
                                              }
                                      }
                                      //if($this->smtp){$this->sendSmtp($this->authenticate, $this->to);}
                                      //else{mail($this->to, $this->subj, $this->EmailBody, $this->headers);}

	
										// To send HTML mail, the Content-type header must be set
										$headers  = 'MIME-Version: 1.0' . "\r\n";
										$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
										$headers .= 'From: CartoonBank Robot <cartoonbank.ru@gmail.com>' . "\r\n";
$subj = "Карикатура на тему Первого апреля";
$EmailBody = "Здравствуйте!
<br><br>
Банк Иронических Изображений Cartoonbank.ru сердечно поздравляет вас с
приближающимся профессиональным праздником любителей и ценителей
юмора - 1 Апреля!
<br><br>
Позвольте также в честь приближающегося праздника подарить Вам с
разрешения Виктора Богорада <a href='http://cartoonbank.ru/?page_id=29&cartoonid=8310'>его рисунок 'Розыгрыш'</a>, вместе с правами
на его публикацию.
<br><br>
Чтобы получить БЕСПЛАТНО рисунок 'Розыгрыш' и лицензионный договор на
его использование, нажмите ссылку 'Вход' на первой странице сайта и
введите логин 'demo' и пароль 'demo' (без кавычек). Вы получите
полноправный доступ к виртуальному счету и возможность совершенно
официально скачать данный рисунок.<br>
Подробные действие (начиная с пункта 4) <a href='http://cartoonbank.ru/?page_id=1010'>описаны на этой странице</a>
<br>
Эта возможность сохранится для вас вплоть до 1 апреля 2011 года.
<br>
Если вам будет интересно получить полноценный доступ ко всей базе
работ на основе партнерского сотрудничества, пишите нам по адресу:
cartoonbank.ru@gmail.com
<br><br>
Мы будем рады ответить на любые ваши вопросы!
<br><br>
С искренним уважением,<br>
Банк Иронических Изображений Cartoonbank.ru<br>
P.S. <a href='http://anri.org.ru/news/item.php?SECTION_ID=15&ELEMENT_ID=5623'>Чуть подробнее о сайте</a><br>
";
										

									    mail($this->to, $subj, $EmailBody, $headers);
										sleep(0.5);

                              }
                      }
              }

              function checkMaillist($list) {
                      if(!file_exists($list)) {
                              return false;
                      } else {
                               $list = file($list);
                               return $list;
                      }
              }

              function setWhom($whom) {
                      if($whom) {
                              $whom = trim($whom);
                              $whom = str_replace(";", " ", $whom);
                              $this->tos  = explode(" ", $whom);
                              return $this->tos;
                      } else {
                              return false;
                      }

              }

              function checkEmail($email) {
                      if (eregi('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.[a-z]{2,4}$', $email)) {
                              return true;
                      } else {
                              return false;
                      }
              }


                function Send(){

                      $this->error_message();
                      $this->getHeader();
                      if($this->smtp){
                              $this->checkSmtp($this->hostSmtp,$this->portSmtp,
                              $this->authenticate,$this->userSmtp,$this->passSmtp);
                              $this->socket = $this->connectSmtp($this->hostSmtp,
                              $this->portSmtp, $this->timeoutSmtp);
                              switch($this->smtpServer){
                                      case 'esmtp':
                                      $this->smtpEhlo($this->socket);
                                      break;
                                      case 'smtp':
                                      $this->smtpHelo($this->socket);
                                      break;
                                      case 'test':
                                      if($this->smtpEhlo($this->socket)){
                                                echo(nl2br("Connection successful... \r\n Server type: esmtp server \n"));
                                                return false;

                                      } else {
                                              $this->smtpQuit($this->socket);
                                              $this->disconnectSmtp($this->socket);
                                              $this->socket = $this->connectSmtp($this->hostSmtp,
                                              $this->portSmtp, $this->timeoutSmtp);
                                              if($this->smtpHelo($this->socket)) {
                                                      echo(nl2br("Connection successful... \r\n Server type: smtp server \n"));
                                                      return false;
                                              } else {
                                                      echo(nl2br("Server type: unknown server. \n"));
                                                      return false;

                                              }
                                      }
                              }

                              $this->smtpAuth($this->authenticate);

                      }

                      if ($this->use == "whom") {
                              $this->readData($this->setWhom($this->whom));
                      }
                      elseif ($this->use == "maillist") {
                               $this->readData($this->checkMaillist($this->list));
                      }
                      elseif ($this->use == "DB" || $this->use == "all"){

                              switch($this->dbfbasa){
                                      case 'mysql':
                                            if(!$this->query_result) return false;
                                            while($this->tos = mysql_fetch_assoc($this->query_result)){
                                                    $this->readData($this->tos);
                                            }
                                      break;
                                      case 'pgsql':
                                            if(!$this->query_result) return false;
                                            while($this->tos = pg_fetch_assoc($this->query_result)){
                                                    $this->readData($this->tos);
                                            }
                                      break;
                                      case 'ibase':
                                            if(!$this->query_result) return false;
                                            while($this->tos = ibase_fetch_assoc($this->query_result)){
                                                    $this->readData($this->tos);
                                            }
                                      break;
                                      case 'msql':
                                            if(!$this->query_result) return false;
                                            while($this->tos = msql_fetch_array($this->query_result, MSQL_ASSOC)){
                                                    $this->readData($this->tos);
                                            }
                                      break;
                                      case 'fbsql':
                                            if(!$this->query_result) return false;
                                            while($this->tos = fbsql_fetch_assoc($this->query_result)){
                                                    $this->readData($this->tos);
                                            }
                                      break;
                                      case 'sqli':
                                            if(!$this->query_result) return false;
                                            while($this->tos = sqlite_fetch_array($this->query_result, SQLITE_ASSOC)){
                                                    $this->readData($this->tos);
                                            }
                                      break;
                                      case 'oci':
                                            if(!$this->query_result) return false;
                                            while($this->tos = oci_fetch_assoc($this->query_result)){
                                                    $this->readData($this->tos);
                                            }
                                      break;
                                      case 'sybase':
                                            if(!$this->query_result) return false;
                                            while($this->tos = sybase_fetch_assoc($this->query_result)){
                                                    $this->readData($this->tos);
                                            }
                                      break;
                                      case 'ingres':
                                            if(!$this->query_result) return false;
                                            while($this->tos = ingres_fetch_array($this->query_result, INGRES_ASSOC)){
                                                    $this->readData($this->tos);
                                            }
                                      break;
                                      case 'phpmm':
                                            if ($this->use == "all"){
                                                    $this->tos = array_merge($this->setWhom($this->whom),
                                                    $this->checkMaillist($this->list));
                                                    $this->readData($this->tos);
                                            }
                                      break;
                              }

                     }

                     if($this->smtp){
                              $this->smtpQuit($this->socket);
                              $this->disconnectSmtp($this->socket);
                     }


              }

               function error_message() {
                      $this->back();
                      if(!$this->smtp){
                              if(!function_exists("mail")){
                                      print(nl2br("mail functions are not available \n"));
                                      Exit($this->back);
                              }
                      }
                      if(!$this->checkEmail($this->from)){
                              print(nl2br("You should specify from whom the message. \n"));
                              Exit($this->back);
                      }
                      if(empty($this->number)){
                              $this->number = 1;
                      }
                      elseif(!ereg('[1-9]', $this->number) && !empty($this->number)){
                              print(nl2br("The amount is specified not correctly. \n"));
                              Exit($this->back);
                      }
                      if(strlen($this->mess) > 1000) {
                              print(nl2br("The message cannot be more thousand symbols. \n"));
                              Exit($this->back);
                      }
                      if($this->send == "stop") {
                              print(nl2br("You have stopped the program. \n"));
                              Exit($this->back);
                      }
                      if($this->use == "maillist"){
                              if(!$this->checkMaillist($this->list)){
                                      print(nl2br("maillist it is specified not correctly. \n"));
                                      Exit($this->back);
                              }
                      }
                      if($this->use == "whom" && empty($this->whom)){
                              print(nl2br("Specify to whom the message. \n"));
                              Exit($this->back);
                      }

              }

}


class import extends phpmm {

        var $e_mail;

        var $dbfbasa;

        var $imp_query;

        function import($data,$e_mail,$dbfbasa) {
                      $this->dbfbasa = ($dbfbasa == 'all')?$data['dbasa']:$dbfbasa;
                      $this->host      = $data['host'];
                      $this->db        = $data['db'];
                      $this->user      = $data['user'];
                      $this->pass      = $data['pass'];
                      $this->imp_query = $data['imp_query'];
                      $this->e_mail    = $e_mail;
                      $this->phpmm_import($this->e_mail);
        }

        function phpmm_import($e_mail) {
                $e_mail = trim($e_mail);
                $e_mail = str_replace("\r", "", $e_mail);
                $e_mail_array   = explode("\n", $e_mail);
                if (sizeof($e_mail_array)>0) {
                        for($i=0;$i<sizeof($e_mail_array);$i++) {
                                if($this->checkEmail($e_mail_array[$i])) {
                                        if($this->imp_query == "phpmm"){
                                                continue;
                                        }
                                        $sqlquery = "$this->imp_query VALUES('$e_mail_array[$i]')";
                                        $this->selectDataBase($sqlquery,$this->dbfbasa);

                                }
                        }
                }
        }


}


if ($_SERVER['REQUEST_METHOD'] == 'POST'){


$id = $_POST['id'];

if($id == 1){

        $send         =  $_POST['send'];

        if($send == 'Send'){

                $hostSmtp     =  $_POST['SmtpHost'];
                $smtpServer   =  $_POST['smtpServer'];
                $portSmtp     =  $_POST['SmtpPort'];
                $userSmtp     =  $_POST['UserSmtp'];
                $passSmtp     =  $_POST['PassSmtp'];
                $authenticate =  $_POST['Authenticate'];
                $smtp         =  $_POST['Smtp'];
                $whom         =  $_POST['Whom'];
                $from         =  $_POST['From'];
                $subj         =  $_POST['Subj'];
                $mess         =  $_POST['Mess'];
                //$number       =  $_POST['Number'];
                //$list         =  $_POST['List'];
                $kod          =  $_POST['Kod'];
                $use          =  $_POST['Use'];

                $empty = $post = array();
                foreach ($_POST as $varname => $varvalue){
                        if (empty($varvalue)){
                                $empty[$varname] = $varvalue;
                        } else {
                                $post[$varname] = $varvalue;
                        }
                }
                $files = array();
                foreach ($post as $key => $value){
                        if (eregi("file", $key, $array)){
                                array_push ($files, $value);
                        }
                }

                $max =  new phpmm(isset($a_data));
                $time_start = $max->getmicrotime();

                if ($use == "whom" || $use == "maillist"){
                        $max ->checkBeforeSending = $checkBeforeSending;
                        $max ->whom         = $whom;
                        $max ->from         = $from;
                        $max ->mailer       = $mailer;
                        $max ->use          = $use;
                        $max ->files        = $files;
                        //$max ->list         = $list;
                        //$max ->number       = $number;
                        $max ->kod          = $kod;
                        $max ->send         = $send;
                        $max ->subj         = $subj;
                        $max ->charset      = $charset;
                        $max ->mess         = $mess;
                        $max ->smtp         = $smtp;
                        $max ->smtpServer   = $smtpServer;
                        $max ->hostSmtp     = $hostSmtp;
                        $max ->portSmtp     = $portSmtp;
                        $max ->userSmtp     = $userSmtp;
                        $max ->passSmtp     = $passSmtp;
                        $max ->authenticate = $authenticate;
                        $max ->Send();
                        $time_end = $max->getmicrotime();
                        $time = $time_end - $time_start;
                        if($max->smtpServer != "test"){
                                echo(nl2br("You have sent: $max->number  Time: $time \n"));
                        }


                } else {

                        if(!isset($databases) || !is_array($databases)){
                                Exit("Databases are not configured. \n");
                        }

                        foreach ($databases as $data){
                                  $phpmm =  new phpmm($data);
                                  $phpmm ->checkBeforeSending = $checkBeforeSending;
                                  $phpmm ->whom         = $whom;
                                  $phpmm ->from         = $from;
                                  $phpmm ->mailer       = $mailer;
                                  $phpmm ->use          = $use;
                                  $phpmm ->files        = $files;
                                  //$phpmm ->list         = $list;
                                  //$phpmm ->number     = $number;
                                  $phpmm ->kod          = $kod;
                                  $phpmm ->send         = $send;
                                  $phpmm ->subj         = $subj;
                                  $phpmm ->charset      = $charset;
                                  $phpmm ->mess         = $mess;
                                  $phpmm ->smtp         = $smtp;
                                  $phpmm ->smtpServer   = $smtpServer;
                                  $phpmm ->hostSmtp     = $hostSmtp;
                                  $phpmm ->portSmtp     = $portSmtp;
                                  $phpmm ->userSmtp     = $userSmtp;
                                  $phpmm ->passSmtp     = $passSmtp;
                                  $phpmm ->authenticate = $authenticate;
                                  $phpmm ->Send();
                                  $phpmm ->disconnect();

                        }

                        $time_end = $max->getmicrotime();
                        $time = $time_end - $time_start;
                        if($phpmm ->smtpServer != "test"){
                                echo(nl2br("You have sent: $phpmm->number  Time: $time \n"));
                        }



                }



        } else {
                Exit();
        }



} elseif($id == 2) {

        $e_mail  =  $_POST['e_mail'];
        $dbfbasa =  $_POST['dbfbasa'];

        foreach ($databases as $data) {
                if($dbfbasa != 'all'){
                        if($dbfbasa == $data['dbasa']){
                                $import = new import($data,$e_mail,$dbfbasa);
                                $import->disconnect();
                                break;
                        }
                } elseif($dbfbasa == 'all') {
                        $import = new import($data,$e_mail,$dbfbasa);
                        $import->disconnect();
                }


        }

}


}


$dir = $_SERVER['PHP_SELF'];

$image = "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
hEq4hEYYAgA7";

if(isset($_GET['img'])){echo base64_decode($image);}


echo <<<phpMM

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Frameset//EN'>
<html>

<head>
  <title>phpMM</title>
  <META HTTP-EQUIV='Content-Type: text/html; Charset=$charset_program http-equiv=Content-Type'>

<script type='text/javascript' language='JavaScript'>

<!--

function check(){

if(document.form1.From.value.length<1){
        alert('You should specify from whom the message.');
        return false;
}
if(document.form1.Use.value.length == 4){
        if(document.form1.Whom.value.length<6){
                alert('E-mail it is specified not correctly.');
                return false;
        }
        if(document.form1.Whom.value.length>100){
                alert('E-mail it is specified not correctly.');
                return false;
        }

}
if(document.form1.Use.value.length>6){
        if(document.form1.List.value.length<3){
                alert('maillist it is specified not correctly.');
                return false;
        }
}
if(document.form1.Mess.value.length>1000){
        alert('The message cannot be more thousand symbols.');
        return false;
}
if(document.form1.Smtp.value.length == 4){
        if(document.form1.SmtpHost.value.length<3){
                alert('Specify SMTP a server.');
                return false;
        }
        if(document.form1.SmtpPort.value.length<1){
                alert('Smtp the port is specified not correctly.');
                return false;
        }
        if(document.form1.Authenticate.value.length>1){
                if(document.form1.Authenticate.value.length>4){
                        if(document.form1.UserSmtp.value.length<1){
                                alert('You have not specified Smtp the user.');
                                return false;
                        }
                        if(document.form1.PassSmtp.value.length<1){
                                alert('You have not specified password Smtp.');
                                return false;
                        }
                }
        }
}else {
        return true;
}
}

function output(){
        var belem = document.getElementById('id1');
        belem.style.display = 'none';
        var iobj = document.getElementById('input');
        ielem = iobj.getElementsByTagName('div');
        iobj.style.display = '';
        var belem2 = document.getElementById('id2');
        belem2.style.display='';
}
function output2(){
        var belem2 = document.getElementById('id2');
        belem2.style.display='none';
        var iobj2 = document.getElementById('input2');
        ielem2 = iobj2.getElementsByTagName('div');
        iobj2.style.display='';
        var belem3 = document.getElementById('id3');
        belem3.style.display='';
}
function output3() {
        var belem3 = document.getElementById('id3');
        belem3.style.display='none';
        var iobj3 = document.getElementById('input3');
        ielem3 = iobj3.getElementsByTagName('div');
        iobj3.style.display='';
        var belem4 = document.getElementById('id4');
        belem4.style.display='';
}
function output4(){
        var iobj4 = document.getElementById('input4');
        ielem4 = iobj4.getElementsByTagName('div');
        iobj4.style.display="";
}
function hide(){
        var belem2 = document.getElementById('id2');
        belem2.style.display='none';
        var belem3 = document.getElementById('id3');
        belem3.style.display='none';
        var belem4 = document.getElementById('id4');
        belem4.style.display='none';
        var iobj = document.getElementById('input');
        ielem = iobj.getElementsByTagName('div');
        iobj.style.display='none';
        var iobj2 = document.getElementById('input2');
        ielem = iobj2.getElementsByTagName('div');
        iobj2.style.display='none';
        var iobj3 = document.getElementById('input3');
        ielem3 = iobj3.getElementsByTagName('div');
        iobj3.style.display='none';
        var iobj4 = document.getElementById('input4');
        ielem4 = iobj4.getElementsByTagName('div');
        iobj4.style.display='none';
}

function cancel(){
        hide();
        var belem = document.getElementById('id1');
        belem.style.display = '';
}

// -->

</script>

<style type='text/css'>

        <!--

        INPUT, TEXTAREA, SELECT {
                font:10px Verdana, Times, Arial, serif, sans-serif;
                background-color: white;
                border:1px solid silver
        }

        TABLE, #x78y {
                font:12px Verdana, Times, Arial, serif, sans-serif;
        }

        BODY {
                background-color: white
        }

        #mybr {
                clear: left
        }

        #x34y  {
                float:left
        }

        #x35y {
                 border:solid silver 1.0pt;
        }

        #x77y {
                color:white;
                text-align: center
        }

        #x78y {
                color:white;
                text-align: right
        }

        #x79y {
                background-color: gray;
                border-style:none
        }

        A:link, A:visited,  A:active {
                font:10px Verdana, Times, Arial, serif, sans-serif;
                color: white;
                font-weight: bold
        }

        -->

</style>


</head>

<body>

<img id = 'x34y' src= '$dir?img=1'> <br id = 'mybr'>

<table id ='x79y' height=22 width=100%>
<tr>
    <td id = 'x79y'>
         <DIV id ='x77y'>
         <a href='$dir?id=1'>phpMM</a> |
         <a href='$dir?id=2'>import email</a> |
         <a href='$dir?id=3'>php information</a> |
         <a href='$dir?id=4'>about</a> |
         <a href='$dir'onClick='window.close()'>exit</a>
         </DIV>
    </td>
</tr>
</table> <br />

phpMM;

$phpMM = <<<phpMM2

<center>

<table width=30%>

<form  name = 'form1' action='' onSubmit="return check()"  method='POST'>

<tr>
   <td>Agent: <br /> &nbsp;</td>
   <td>
       <select size='1' name='Smtp'>
           <option value='' selected='true'>default</option>
           <option value='smtp'>smtp server</option>
       </select> <br /> &nbsp;
   </td>
</tr>
<!-- <tr>
   <td>Smtp Server: <br /> &nbsp;</td>
   <td>
       <select size='1' name='smtpServer'>
           <option value='esmtp'>esmtp</option>
           <option value='smtp'>smtp</option>
           <option value='test'>test</option>
       </select> <br /> &nbsp;
   </td>
</tr>
<tr>
   <td>Smtp host: <br /> &nbsp;</td>
   <td><input name='SmtpHost' type='text' value='test.ru'><br /> &nbsp; </td>
</tr>
<tr>
   <td>Smtp port: <br /> &nbsp;</td>
   <td><input name='SmtpPort' type='text' value='25'><br /> &nbsp; </td>
</tr>
<tr>
   <td>Smtp user:</td>
   <td><input name='UserSmtp' type='text' value='max'><br /> &nbsp;</td>
</tr>
<tr>
   <td>Smtp password:<br /> &nbsp;</td>
   <td><input name='PassSmtp' type='password' value=''><br /> &nbsp;</td>
</tr>
<tr>
   <td>Authenticate:<br /> &nbsp;</td>
   <td>
       <select size='1' name='Authenticate'>
          <option value=''>none</option>
          <option value='LOGIN'>login</option>
          <option value='PLAIN'>plain</option>
       </select>
       <br /> &nbsp;
   </td>
</tr> -->
<tr>
   <td>To: <br /> &nbsp;</td>
   <td><input name='Whom' type='text' value='cartoonbank.ru@gmail.com'><br /> &nbsp;</td>
</tr>
<tr>
   <td>From: <br /> &nbsp;</td>
   <td><input name='From' type='text' value='cartoonbank.ru@gmail.com'><br /> &nbsp;</td>
</tr>
<tr>
   <td>Subject: <br /> &nbsp;</td>
   <td><input name='Subj' type='text' value='subject'><br /> &nbsp; </td>
</tr>
<tr>
   <td>Message: <br /> &nbsp;</td>
   <td><textarea name='Mess' rows=5 cols=35 wrap='off'>hello</textarea> <br /> &nbsp;</td>
</tr>
<tr>
   <td>The coding: <br /> &nbsp;</td>
   <td>
       <select size='1' name='Kod'>
          <option value='' selected='true'>html</option>
          <option value=1>text</option>
       </select>
       <input name="id" type="hidden" value="1">
       <br /> &nbsp;
   </td>
</tr>

<!--
<tr>
   <td>Number: <br /> &nbsp; </td>
   <td>
       <input name='Number' type='text' value=''>
       <br /> &nbsp;
   </td>
</tr>
-->

<tr>
   <td>Using: <br /> &nbsp;</td>
   <td>
       <select size='1' name='Use'>
          <option value='DB' selected='true'>DataBase</option>
          <option value='whom'>To</option>
          <option value='maillist'>maillist</option>
          <option value='all'>All</option>
       </select> <br /> &nbsp;
   </td>
</tr>
<!-- 
<tr>
   <td>Maillist:</td>
   <td><input name='List' type='file' value=''><br /><br /></td>
</tr>
<tr>
   <td>Attach:</td>
   <td>
       <input name='file1' type='file' value=''><br />
       <div id='input'>
       <input name='file2' type='file' value=''><br />
       <input name='file3' type='file' value=''><br />
       <input name='file4' type='file' value=''><br />
       <input name='file5' type='file' value=''>
       </div>
       <div id='input2'>
       <input name='file6' type='file' value=''><br />
       <input name='file7' type='file' value=''><br />
       <input name='file8' type='file' value=''><br />
       <input name='file9' type='file' value=''>
       </div>
       <div id='input3'>
       <input name='file10' type='file' value=''><br />
       <input name='file11' type='file' value=''><br />
       <input name='file12' type='file' value=''><br />
       <input name='file13' type='file' value=''>
       </div>
       <div id='input4'>
       <input name='file14' type='file' value=''><br />
       <input name='file15' type='file' value=''><br />
       <input name='file16' type='file' value=''><br />
       <input name='file17' type='file' value=''><br />
       <input name='file18' type='file' value=''><br />
       <input name='file19' type='file' value=''><br />
       <input name='file20' type='file' value=''><br />
       <input name='file21' type='file' value=''><br />
       <input name='file22' type='file' value=''><br />
       <input name='file23' type='file' value=''><br />
       <input name='file24' type='file' value=''><br />
       <input name='file25' type='file' value=''><br />
       <input name='file26' type='file' value=''><br />
       </div>
   </td>
</tr>
<tr>
   <td>
       <div id='id1'><input type='button' value = 'attach' onClick = 'output();'></div>
       <div id='id2'><input type='button' value = 'attach' onClick = 'output2();'></div>
       <div id='id3'><input type='button' value = 'attach' onClick = 'output3();'></div>
       <div id='id4'><input type='button' value = 'attach' onClick = 'output4();'></div>
       <input type='button' value = 'cancel' onClick = 'cancel();'>
   </td>
</tr>
<tr><td>&nbsp;</td></tr> -->
<tr>
   <td>
       <input type='submit' name = 'send' value='Send'>
       <!--
       <input type='submit' name = 'send' value='stop'>
       -->
       <input type = 'reset' name = 'Reset' value = 'Reset'>
   </td>
</tr>

</form>

</table>

</center>

<script type='text/javascript' language='JavaScript'>hide();</script>

<br />

phpMM2;

$about = <<<phpMM3
<center>
<table id = 'x35y' width = 28% height = 55%>
<tr>
   <td valign = 'top'> <br />
        <center>phpMassMailer(phpMM)</center>
        <br /> <br />
        &nbsp;Copyright (&copy;) Romanovsky Maxim, GPL  <br /> <br />
        &nbsp;http://www.phpclasses.net <br /> <br />
        &nbsp;romanovsky_m@yahoo.com
        <br /> <br /> <br /> <br /> <br /> <br /> <br /> <br /> <br />
        <br /> <br /> <br /> <br /> <br /> <br /> <br /> <br /> <br />
        <br /> <br />
   </td>
</tr>
</table> <br /> <br /> <br /> <br /> <br />
</center>

phpMM3;

$import = <<<phpMM4
<center>
<table>
<tr><td>
<form action='' method = 'post'>
<select size='1' name='dbfbasa'>
  <option value='mysql'>mysql</option>
  <option value='pgsql'>pgsql</option>
  <option value='ibase'>ibase</option>
  <option value='all'>all</option>
</select> <br />
<TEXTAREA NAME='e_mail' ROWS=48 COLS=54 WRAP='VIRTUAL'></TEXTAREA> <br />
<input name="id" type="hidden" value="2">
<input type='submit' value='Send'>
</form>
<tr><td>
</table>
</center>
phpMM4;

$id = (isset($_GET['id']))?$_GET['id']:'';

switch($id){
        case '1':
        echo $phpMM;
        break;
        case '2':
        echo $import;
        break;
        case '3':
        phpinfo();
        break;
        case '4':
        echo $about;
        break;
        default:
        echo $phpMM;
}

echo <<<phpMM4


<table id ='x79y' height=22 width=100%>
<tr>
   <td id = 'x79y'>
      <DIV id ='x78y'>phpMM &copy; Romanovsky Maxim (version $version)</DIV>
   </td>
</tr>
</table>

</center>

</body>

</html>

phpMM4;


?>