<?php
namespace PhippsyTech\WebsocketServer;

// Send the payload to all devices subscribed to a channel
final Class SendToChannel{

    public function __invoke($obj, $channel, $message ){
        foreach ($obj->devices as $device){
            $data = $obj->devices->offsetGet($device);
            if( isset($data['channels']) && in_array($channel, $data['channels']) ){
                $json = json_encode($message);
                $device->send($json);
            }
        }
    }
}