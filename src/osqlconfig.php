<?php
namespace mysql;

$configure = true;

class configuration
{
public function config ()
    {
        return [




        "log_warning" => false,          //boolean

        "display_error" => true,       //boolean
        
        "driver" => "mysqli",              //string mysqli or pdo









        ];

    }
}
