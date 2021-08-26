<?php
namespace mysql;

$configure = true;

class configuration
{
public function config ()
    {
        return [




        "log_warning" => true,          //boolean

        "display_error" => false,       //boolean
        
        "driver" => "pdo",              //string mysqli or pdo









        ];

    }
}