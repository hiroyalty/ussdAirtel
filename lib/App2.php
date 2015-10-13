<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of App
 *
 * @author gbolaga
 */
class App {

    //put your code here


    public static function setNormalHeaders($headers) {
        header("Expires: -1");
        #header("Content-Type: UTF-8");
        header("Path: " . $_SERVER['PHP_SELF']);
        header("Cache-Control: max-age=0");
        # set custom headers
        foreach ($headers as $key => $value) {
            header("$key:$value");
        }
    }

    public static function logRequestToFile($msg) {
        #$date_time = date("Y-m-d h:i:s");
        #$logpath = '/var/www/html/nsl/';
        #$logFile = "call.log";
        //$log = "$date_time >> $msg";
        $logFile = "/var/www/html/flares/flares.log";
        $fp = fopen($logFile, 'a+');
        fputs($fp, "Logging USSD Request: $msg\n");
        fclose($fp);
        return TRUE;
    }

    public static function requestToArray() {
        $log = "";
        if (isset($_REQUEST)) {
            foreach ($_REQUEST as $key => $value) {
                $log.= "$key : $value,";
            }
            #App::logRequesToFile($log);
        }
        return $log;
    }

    public static function getResponse() {

        $input = trim($_REQUEST['INPUT']) ? $_REQUEST['INPUT'] : '676';
        $msisdn = isset($_REQUEST['msisdn']) ? trim($_REQUEST['msisdn']) : '2348132614337';
        $code = $_REQUEST['code'] ? $_REQUEST['code'] : '676'; //always the same
        $sessionid = isset($_REQUEST['sessionid']) ? $_REQUEST['sessionid'] : "52277817";
        // find out what ussd code is running and map it to the right app
        /*
         * 776 is mhealth, 676 is mmarket
         */
        try {
            $content = App::putText($input, $sessionid, $msisdn,$code);
        } catch (Exception $e) {
            $content = "MMarket App";
        }

        #$logFile = "/var/www/html/flares/flares.log";
        $txt = App::requestToArray();
        #echo $txt;
        App::logRequestToFile($txt);
        return $content;
        #return $txt;
    }

    public static function setCleanUpHeaders() {
        header("Path: " . $_SERVER['PHP_SELF']);
        header("Expires: -1");
        #header("Content-Type: UTF-8");
        header("Cache-Control: max-age=0");
    }

    public static function setContent($content) {
        header('Content-Type: text/plain');
        echo $content;
        
    }

    public static function putText($input, $sessionid, $msisdn,$code) {
        include_once 'rb.php';
        $dbfile = 'data-' . date('Ymd') . '.db';
        #R::setup("sqlite:data/$dbfile", 'user', 'password');
        R::setup('mysql:host=localhost;dbname=ussd', 'ussd', 'ussd');
        if ($input == '676') {
            $level = 0;
            $sess = R::dispense("session");
            $sess->session_id = $sessionid;
            $sess->level = $level;
            $sess->last_entry = $input;
            $sess->last_action = 'menu0';
            $sess->msisdn = $msisdn;
            # save json object to string
            //$query['code']= $code;
            //$sess->query = json_encode($query);
            $sess->path = "/$code";
            $next = $level;
            $id = R::store($sess);
            $main = App::menu_by_title('menu0');
            return $main->text;
            
        } else {

            $i = R::getRow('select * from session where session_id=?', array($sessionid));
            $last_action = $i['last_action'];
            $text = "Menu Error";
            #echo "Last action = $last_action<br>";
            //print_r($list);
            $last_menu = App::menu_by_title($last_action);
            #echo "Last Menu: <br>";
            #print_r($last_menu);
            $valid_options = explode(',', $last_menu->options);
            $level = $i['level'];
            #echo "<br>Last Level : $level<br>";
            if (in_array($input, $valid_options)) {
                # was a valid input so update session level, and move to next step
                $next = explode(',', $last_menu->next);
                //$next_menu = get_menu
                $next_menu = $next[$input];
                R::exec('update session set level=level+1,last_action=? where session_id=?', array($next_menu, $sessionid));
                $menu = App::menu_by_title($next_menu);
                if ($menu->contenttype == "text") {
                    $text = $menu->text;
                }
                if ($menu->contenttype == "file") {
                    #$text = file_get_contents($menu->filesource);
                    $text = App::text_from_xml($menu->filesource);
                }
                # handle some event here
                
              
            } else {
               
                #echo "<br>Invalid Input: ";
                $menu = $last_menu;
                if ($menu->contenttype == "text") {
                    $text = $menu->text;
                }
                if ($menu->contenttype == "file") {
                  
                    $text = App::text_from_xml($menu->filesource);
                }
            }
            return $text;
        }
    }

    public static function menu_by_title($title) {
        $xml = simplexml_load_file("mmarket.xml");
        #var_dump($xml);
        $objects = $xml->xpath("/root/menu[title='$title']");
        #echo $obj[0]->title;
        #var_dump($objects[0]);
        return $objects[0];
    }
    public static function text_from_xml($src) {
        $xml = simplexml_load_file($src);
        #var_dump($xml);
        $objects = $xml->xpath("/root/menu/text");
        #echo $obj[0]->title;
        #var_dump($objects[0]);
        return $objects[0];
    }

}

?>
