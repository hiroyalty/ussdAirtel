<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of App
 *
 * @author gbolaga

 *  */
include_once 'lib/config.php';

class App {

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
            $content = App::putText($input, $sessionid, $msisdn, $code);
        } catch (Exception $e) {
            $content = "Service Temporarily Unavailable";
        }
        $url = "http://172.24.87.125:31110/mmarket/app.request";
        return App::request($url);
        #return $txt;
    }

    public static function setCleanUpHeaders() {
        header("Path: " . $_SERVER['PHP_SELF']);
        header("Expires: -1");
        #header("Content-Type: UTF-8");
        header("Cache-Control: max-age=0");
    }

    public static function log_event($evnt) {
        $logFile = "/var/www/html/flares/events.log";
        $fp = fopen($logFile, 'a+');
        fputs($fp, "Logging USSD Event: $evnt\n");
        fclose($fp);
        return TRUE;
    }

    public static function setContent($content) {
        header('Content-Type: text/plain');
        echo $content;
    }

    public static function request($url) {
        try {
# try pushing request to url;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPGET, 1); // Make sure GET method it used
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return the result
            #curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
            #curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
            $res = curl_exec($ch); // Run the request
        } catch (Exception $ex) {

            $res = 'Service Temporarily Unavailable';
        }
        return $res;
    }
    public static function set_local_msisdn($number) {
        return "0" . substr($number, 3);
    }

    public static function checkDestination($number) {
        $first = substr($number, 0, 1);
        if ($first == '+') {
            return '0' . substr($number, 4);
        } else {
            return '0' . substr($number, 3);
        }
    }
    
}

?>
