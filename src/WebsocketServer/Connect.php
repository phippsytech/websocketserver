<?php
namespace PhippsyTech\WebsocketServer;

use Hidehalo\Nanoid\Client as NanoIdClient;
use Hidehalo\Nanoid\GeneratorInterface;


final Class Connect{

    public function __invoke($obj, $conn){
        $obj->devices->attach($conn);
        $nanoidclient = new NanoIdClient();
        $data["device_id"]= $nanoidclient->generateId($size = 15, $mode = NanoIdClient::MODE_DYNAMIC); //19
        $message = [
            "action"=>"setDeviceId",
            "data"=>$data
        ];
        $obj->devices->offsetSet($conn, $data);
        $json = json_encode($message);
        $conn->send(json_encode($json));
    }

}