<?php
namespace PhippsyTech\WebsocketServer;


// Handle messages that come in via the unix socket
final Class HandleSocketData{

    public function __invoke($obj, $message){

        if (!isset($message['action']) || !isset($message['data']) ) return;
 
        switch($message['action']){

            case "sendToChannel" :
                if (!isset($message['channel'])) return;
                (new \PhippsyTech\WebsocketServer\SendToChannel)($obj, $message['channel'], $message['data']);
                break;

            case "sendToDevice" :
                if (!isset($message['device'])) return;
                (new \PhippsyTech\WebsocketServer\SendToDevice)($obj, $message['device'], $message['data']);
                break;

            case "multicast" :
                (new \PhippsyTech\WebsocketServer\Multicast)($obj, $message['data']);
                break;

            case "subscribe" :
                if (!isset($message['device']) || !isset($message['channel'])) return;
                (new \PhippsyTech\WebsocketServer\Subscribe)($obj, $message['device'], $message['channel']);
                break;

            case "unsubscribe" :
                if (!isset($message['device']) || !isset($message['channel'])) return;
                (new \PhippsyTech\WebsocketServer\Unsubscribe)($obj, $message['device'], $message['channel']);
                break;

        }

    }
}