<?php
namespace mysql;

if(!defined('MOP')){die('Direct access is not allow');}

class configuration
{
public function config ()
    {
        return [




        "host" => 'localhost',          //host

        "username" => 'root',       //username

        "password" => '',       //password

        "database" => 'mop',       //database name

        "injection" => 
        [
            'username',
            'password',
            'mop',
        ],

        "masterkey" => 'key',

        "sqlkey" => 'key',









        ];

    }
}
