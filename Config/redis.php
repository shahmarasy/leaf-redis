<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Redis host
    |--------------------------------------------------------------------------
    |
    | Set the host for redis connection
    |
    */
    "host" => "127.0.0.1",

    /*
    |--------------------------------------------------------------------------
    | Redis host port
    |--------------------------------------------------------------------------
    |
    | Set the port for redis host
    |
    */
    "port" => 6379,

    /*
    |--------------------------------------------------------------------------
    | Redis auth
    |--------------------------------------------------------------------------
    |
    | Set the password for redis connection
    |
    */
    "password" => null,

    /*
    |--------------------------------------------------------------------------
    | Redis session handler
    |--------------------------------------------------------------------------
    |
    | Set redis as session save handler
    |
    */
    "session" => false,

    /*
    |--------------------------------------------------------------------------
    | Redis session save_path
    |--------------------------------------------------------------------------
    |
    | Save path for redis session. Leave null to automatically
    | generate the session save path. You can also use multiple save urls
    | by passing in an array.
    |
    */
    "session.savePath" => null,

    /*
    |--------------------------------------------------------------------------
    | Redis session save_path options
    |--------------------------------------------------------------------------
    |
    | Options for session save path. You can pass in multiple options in
    | the order of the save path above.
    |
    */
    "session.saveOptions" => [],
];
