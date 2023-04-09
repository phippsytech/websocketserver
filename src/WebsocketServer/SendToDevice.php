<?php
namespace PhippsyTech\WebsocketServer;

// Send the message to one device
final Class SendToDevice{

    public function __invoke($obj, $device_id, $message){
        foreach ($obj->devices as $device){
            $data = $obj->devices->offsetGet($device);
            if( isset($data['device_id']) && $data['device_id']==$device_id ) {
                $json = json_encode($message);
                $device->send($json);
            }
        }
    }
}