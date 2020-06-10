<?php
require("phpMQTT.php");
include 'mqtt_credentials.php';

function mqtt_publish($topic, $message) {
    
    //MQTT client id to use for the device. "" will generate a client id automatically
    $mqtt = new phpMQTT($GLOBALS['mqttHost'], $GLOBALS['mqttPort'], "ClientID".rand());

    if ($mqtt->connect(true,NULL,$GLOBALS['mqttUser'],$GLOBALS['mqttPassword'])) {
        $mqtt->publish($topic,$message, 0);
        $mqtt->close();
    }else{
        echo "Fail or time out";
    }
}

?>