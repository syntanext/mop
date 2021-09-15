<?php
namespace mysql;

if(!isset($OSQL)){ die('direct access is not allow');};

class configuration
{
public function config ()
    {
        return [




        "host" => 'localhost',          //host

        "username" => 'root',       //username

        "password" => '',       //password

        "database" => 'osql',       //database name

        "injection" => 
        [
            'username',
            'password',
            'osql',
        ],

        "masterkey" => 'key',

        "sqlkey" => 'key',









        ];

    }
}
