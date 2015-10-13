<?php

class Mhealth {

    public static function getResponse($input) {
        if($input == '776'){
            $main = self::menu_by_title('menu0');
            echo $main->text;
            return $main;
        }else{
            return "yeeepah";
        }
    }
    public static function getContent($input,$sessionid,$msisdn) {
        if($input == '776'){
            $main = self::menu_by_title('menu0');
            echo $main->text;
            return $main;
        }else{
            return "yeeepah";
        }
    }

    public static function response() {
        return "Welcome to Call a Doctor:\n Enter";
    }

    public static function menu_by_title($title) {
        $xml = simplexml_load_file("menu.xml");
        #var_dump($xml);
        $objects = $xml->xpath("/root/menu[title='$title']");
        #echo $obj[0]->title;
        #var_dump($objects[0]);
        return $objects[0];
    }

}

?>
