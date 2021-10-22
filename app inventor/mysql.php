<?php
use mysql\mop as mysql;

define("MOP","mysql optimizer");

require_once 'src/mop.php';

header('Cache-Control: no-cache, must-revalidate');

if(isset($_POST['query']) && isset($_POST['key']))
{   
    $connect = new mysql();

    $query = urldecode($_POST['query']);

    $connect->verify($query);

    $connect->connect();

    $connect->query();

    $parameter = [];

    if(isset($_POST['param']))
    {
        $param = $_POST['param'];

        foreach ($param as $key => $value)
        {
            $parameter[] = urldecode($value);
        }

        $connect->run_all($parameter);
    }

    else
    {
        $connect->run();
    }

    if(isset($_POST['add_query']))
    {
        $query = urldecode($_POST['add_query']);

        $connect->verify($query);
        $connect->free_results();
        $connect->add_query($query);
    }

    if(!empty($connect->csv))
    {

        header("HTTP/1.0 200");

        echo $connect->csv;
    }

    else
    {

        header("HTTP/1.0 201");

        echo $connect->num_of_rows;
    }
    
    $connect->close();
    
}

else
{
    header("HTTP/1.0 204");
    echo "Bad Request";
}
?>
