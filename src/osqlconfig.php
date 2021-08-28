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
        
        "driver" => "mysqli",              //string mysqli or pdo









        ];

    }
}
