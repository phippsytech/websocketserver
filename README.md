# websocketserver
A simple websocket server written in PHP

WORK IN PROGRESS!!

## Overview
Here's the idea:

This websocket server is designed to send data only.  After running the server you would connect your front end to it to receive data.
To send data from your backend via the websocket you send messages via a Unix Socket.


The possible message actions are:
* multicast - sends to all connected devices
* sendToChannel - sends to all devices subscribed to a channel
* sendToDevice - sends to a connected device identified by a device_id
* subscribe - subscribes a device to a channel
* unsubscribe - unsubscribes a device from a channel


There is a JSON format for messages:

```JSON
{
    "action": "sendToDevice",
    "device": "1234567",
    "data": {
        "action": "someAction",
        "data": {
            "some_key": "some_value",
            ...
        }
    }
}
```

## Install
For now you will have to add this to composer manually

```JSON
{
    "require": {
        "phippsytech/websocketserver": "1.0.0"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/phippsytech/websocketserver.git"
        }
    ]
}
```


## Usage

This is a sample setup for the server.  It uses SSL Certificates generated by Lets Encrypt.

websocketserver.php
```PHP
require 'vendor/autoload.php';

$DOMAIN = 'yourdomain.com';
$WEBSOCKET_PORT = 8443;
$unix_socket_file = '/tmp/myapp.sock'; 


## WEBSOCKET SERVER
$websocketServer = new \PhippsyTech\WebsocketServer();

// Sets up the websocket server
$webSock = new \React\Socket\Server('0.0.0.0:'.$WEBSOCKET_PORT);
$webSock = new \React\Socket\SecureServer($webSock, null, [
    'local_cert' => '/etc/letsencrypt/live/'.$DOMAIN.'/fullchain.pem',
    'local_pk' => '/etc/letsencrypt/live/'.$DOMAIN.'/privkey.pem',
    'allow_self_signed' => FALSE,
    'verify_peer' => FALSE
]);

$webServer = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            $websocketServer
        )
    ),
    $webSock
);


## UNIX SOCKET
// Sets up the unix socket server for sending data to the websocket server
$unix_socket = new \React\Socket\UnixServer($unix_socket_file);

// If the server crashes or is stopped, delete the socket file
pcntl_signal(SIGINT, function() use ($unix_socket) {
    $unix_socket->close();
    unlink($unix_socket_file);
    exit;
});

$unix_socket->on('connection', function (\React\Socket\ConnectionInterface $connection) {
    $connection->on('data', function ($json) use ($connection) {
        $data = json_decode($json, true);
        if ($data !== null) {
            $websocketServer->handleSocketData($json);
            $response = "OK";
        } else {
            $response = "ERR";
        }
        $connection->write($response);
    });
});


## HEARTBEAT (prevents client disconnecting)
Loop::addPeriodicTimer(60, function() use ($websocketServer) {
    $websocketServer->multicast(["action"=>"hb"]);
});
```

You would then run this:
```
sudo php websocketserver.php
```
NOTE: You need to run using sudo because that gives you permissions to access the SSL certificates.  You would set up a daemon to keep this server running.



To send a message you can create a function like this in your application:
```PHP
    function sendViaWebsocket($json){
        $socket = stream_socket_client('unix:///tmp/myapp.sock');
        fwrite($socket, $json);
        $response = fread($socket, 8192);
        return $response;
    }
```

Now you can send your message via the websocket server by calling your function like this:
```PHP

$json = '{
    "action": "sendToDevice",
    "device": "1234567",
    "data": {
        "action": "someAction",
        "data": {
            "some_key": "some_value",
            ...
        }
    }
}';

$result = sendViaWebsocket($json);
```

Coming soon: Examples!