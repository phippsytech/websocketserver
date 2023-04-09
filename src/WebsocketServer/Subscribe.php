<?php
namespace PhippsyTech\WebsocketServer;

// Subscribe to a channel
final Class Subscribe{

    public function __invoke($obj, $device_id, $channel){
        foreach ($obj->devices as $device){
            $data = $obj->devices->offsetGet($device);
            if( isset($data['device_id']) && $data['device_id']==$device_id ) {
                if( !in_array($channel, $data['channels']) ){
                    $data['channels'][] = $channel;
                    $obj->devices->offsetSet($device, $data);
                }
            }
        }
    }

}