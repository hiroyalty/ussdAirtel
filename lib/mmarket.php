<?php

function getResponse($input) {
    if ($input == '676') {
        $main = menu_by_title('menu0');
        echo $main->text;
        return $main->text;
    } else {
        return "Mmarket";
    }
}

function loadContent($input) {
    if ($input == '676') {
        $main = menu_by_title('menu0');
        #echo "Loading App";
        echo $main->text;
        return $main->text;
    } else {
        return "MMarket Content";
    }
}

function response() {
    return "Welcome to Call a Doctor:\n Enter";
}

function menu_by_title($title) {
    $xml = simplexml_load_file("mmarket.xml");
    #var_dump($xml);
    $objects = $xml->xpath("/root/menu[title='$title']");
    #echo $obj[0]->title;
    #var_dump($objects[0]);
    return $objects[0];
}

?>
