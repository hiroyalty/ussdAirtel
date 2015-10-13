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
         include_once 'mmarket.php';
        $input = trim($_REQUEST['INPUT']) ? $_REQUEST['INPUT'] : '676';
        $msisdn = isset($_REQUEST['msisdn']) ? trim($_REQUEST['msisdn']) : '2348132614337';
        $code = $_REQUEST['code'] ? $_REQUEST['code'] : '676'; //always the same
        $sessionid = isset($_REQUEST['sessionid']) ? $_REQUEST['sessionid'] : "52277817";
        // find out what ussd code is running and map it to the right app
        /*
         * 776 is mhealth, 676 is mmarket
         */
        switch ($code) {
            case '776':
                include_once 'mhealth.php';
                try {
                    $content = Mhealth::getContent($input, $sessionid, $msisdn);
                } catch (Exception $e) {
                    $content = "Mhealth App";
                }
                break;

            case '676':
                try {
                    $content = self::putText($input);
                } catch (Exception $e) {
                    $content = "MMarket App";
                }
                break;
            default:
                break;
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
    public static function putText($input) {
      if ($input == '676') {
        $main = self::menu_by_title('menu0');
        #echo "Loading App";
        #echo $main->text;
        return $main->text;
    } else {
        return "MMarket Content";
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
    
    

}

?>
