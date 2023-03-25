<?php
namespace Mysql;


/*  
 *  description:Run MYSQL query faster and get result in a reliable way.;
 *  Version: 1.1.0;
 *  Type: website version.
 *  Recommended php version: >= 7;
 *  website: https://github.com/bringittocode/mop
 *  contact: bringittocode@gmail.com
 */

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
