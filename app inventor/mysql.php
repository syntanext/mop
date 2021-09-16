<?php
use mysql\osql as mysql;

$OSQL = true;

require_once 'src/osql.php';

header('Cache-Control: no-cache, must-revalidate');

if(isset($_POST['query']) && isset($_POST['key']))
{   
    $connect = new mysql();

    $query = $_POST['query'];

    $connect->verify($query);

    $connect->connect();

    $connect->query();

    $parameter = array();

    if(isset($_POST['param']))
    {
        $param = $_POST['param'];

        foreach ($param as $key => $value)
        {
            array_push($parameter, $value);
        }

        $connect->run_all($parameter);
    }

    else
    {
        $connect->run();
    }

    if(isset($_POST['add_query']))
    {
        $query = $_POST['add_query'];

        $connect->verify($query);
        $connect->free_results();
        $connect->add_query($query);
    }

    if(!empty($connect->csv))
    {

        header("HTTP/1.0 200 Rows");

        echo $connect->csv;
    }

    else
    {

        header("HTTP/1.0 201 Rows");

        echo $connect->num_of_rows;
    }
    
    $connect->close();
    
}

else
{
    echo "Bad Request";
}
?>
