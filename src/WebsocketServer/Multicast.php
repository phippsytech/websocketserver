<?php
namespace PhippsyTech\WebsocketServer;

// Send to all connections
final Class Multicast{

    public function __invoke($obj, $message){
        $json = json_encode($message);
        foreach ($obj->devices as $device) $device->send($json);
    }
    
}