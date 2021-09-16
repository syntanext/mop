<?php
namespace mysql;

if(!isset($OSQL)){ die('direct access is not allow');};

use mysql\configuration as config;
use Mysqli;
use Pdo;

/*  
 *  description:Run MYSQL query faster and get result in a reliable way.;
 *  Version: 1.2.1;
 *  Type: App inventor version.
 *  Recommended php version: >= 7;
 *  website: https://github.com/hazeezet/mysql
 *  contact: hazeezet@gmail.com
 * 
 * 
 */



  // Handling mysql error
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class osql
{

  //PUBLIC
    public $csv_header;
    public $csv;
    public $header_row = array();
    public $connect;
    public $num_of_rows = 0;
    public $insert_id = 0;



  //PRIVATE
    private $first_error;
    private $prepare_query;
    private $pdo_query;

    private $driver = 'MYSQLI';
    private $host;
    private $username;
    private $password;
    private $databasename;
    private $sqlkey;
    private $masterkey;
    private $post_masterkey = '';
    private $query;
    private $injection;
    private $DefaultInjection = array("create","drop","truncate","1,1","https","http","top 0 ","top 1 ","benchmark","union","root","delay","true","false","getRequestString","schema","syscolums","sysobjects","dump","sleep","ascii","extractvalue","database","version","shutdown","declare","begin","end","not in","not exist","isnull","load","admin","convert","pytW"," 1 ","%","||"," 0 ","injectx");

  //PROTECTED
    protected $config;
    protected $raw_result_query = array();




  // Osql initialization method
  function __construct()
  {
   
      if (file_exists(__DIR__.'/osqlconfig.php'))
      {
          require_once __DIR__.'/osqlconfig.php';
          $configuration = new config;
          $this->config = $configuration->config();

          $this->host = $this->config['host'];
          $this->username = $this->config['username'];
          $this->password = $this->config['password'];
          $this->databasename = $this->config['database'];

          $this->sqlkey = $this->config['sqlkey'];
          $this->masterkey = $this->config['masterkey'];
          $this->query = urldecode($_POST['query']);
          $this->injection = $this->config['injection'];

          if(isset($_POST['masterkey']))
          {
              $this->post_masterkey = $_POST['masterkey'];
          }
      }

      else
      {
        $message = "OSQL require it's configuration file.";
        header("HTTP/1.0 400 Bad Request");
        die($message);
      }

  }


  private function csv()
  {

      $csv = '';
      $csv_header = '';

      if ($this->driver === 'PDO')
      {

        $count = $this->pdo_query->columnCount();

        //Get all Header row
        for ($a=0; $a < $count; $a++)
        {
          $columnName = $this->pdo_query->getColumnMeta($a);
          $name = $columnName['name'];
          $name = str_replace("\"","\"\"",$name);
          $csv_header .= "\"$name\"".",";
          array_push($this->header_row,$name);
        }
        $csv_header = rtrim($csv_header, ",")."\n";

        //Get all Rows and colums
        foreach ($this->pdo_query as $column => $value)
        {
          for ($b=0; $b < $count; $b++)
          {
            if ($value[$b] == '')
            {
              $csv .= ' '.",";
              $csv_header .= ' '.",";
            }
            
            else
            {
              $col = $value[$b];
              $col = str_replace("\"","\"\"",$col);
              $csv .= "\"$col\"".",";
              $csv_header .= "\"$col\"".",";
            }

          }
          $csv = rtrim($csv, ",")."\n";
          $csv_header = rtrim($csv_header, ",")."\n";
        }

        $this->csv = $csv;
        $this->csv_header = $csv_header;
      }
  }

  private function mysqli_connection()
  {
    $host = $this->host;
    $username = $this->username;
    $password = $this->password;
    $database = $this->databasename;

    try
    {
      @$this->connect = new mysqli ($host,$username,$password,$database);   //connect
      $this->driver = 'MYSQLI';

    }
          
    catch (\Throwable $e)
    {
        $message = "Database Connection Failed: " . $e->getMessage();   //reports a DB connection failure
        header("HTTP/1.0 400 Bad Request");
        die($message);
    }
  }

  private function pdo_connection()
  {
    $host = $this->host;
    $username = $this->username;
    $password = $this->password;
    $database = $this->databasename;

    try
    {
      $this->connect = new PDO("mysql:host=$host;dbname=$database", $username, $password);
      $this->connect->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $this->driver = 'PDO';

    }
    catch (\Throwable $e)
    {
      $message = "Database Connection Failed:" . $e->getMessage();
      header("HTTP/1.0 400 Bad Request");
      die($message);
    }
  }


  /** PUBLIC METHOD */
  public function verify($query)
  {

    if(!isset($_POST['key']))
    {
        header("HTTP/1.0 400 Bad Request");
        die('Bad request');
    }

    elseif($_POST['key'] != $this->sqlkey)
    {
        header("HTTP/1.0 400 Bad Request");
        die('Bad request');
    }

    if ($this->masterkey != $this->post_masterkey)
    {
        if (!empty($this->injection))
        {
            foreach ($this->injection as $Injection)
            {
                if (strlen(stristr($query,$Injection)) > 0)
                {
                    header("HTTP/1.0 400 Bad Request");
                    die('You may not have the permission to run this query');
                    break;
                }
            }
        }
    }

    if(true)
    {
        foreach ($this->DefaultInjection as $Injection)
        {
            if (strlen(stristr($query,$Injection)) > 0)
            {
                header("HTTP/1.0 400 Bad Request");
                die('You may not have the permission to run this query');
                break;
            }
        }

        $Rquery0 = str_replace("="," = ",$query);  // It Rearrange the query
        $Rquery1 = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $Rquery0))); //It Rearrange the query
        $Rquery2 = str_replace( array( "' '","' '","''","'-'","'_'","'&'","'^'","'*'","'x'","1__","1--"," i ","--","|","?","'= 1'","1 = 1","#","' = '","top 1","x = x","1 = 0","x = y", ),'',$Rquery1 ); // It Rearrange the query
        $Rquery3 = trim(preg_replace('/" = "|""|= "|"|<!--.*?--> <!--.*?-->|"&"|"^"|"*"|"x"|[0-9]+ = [0-9]+|top 1 /', ' ', $Rquery2)); // Rearrange the query
        $Rquery4 = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $Rquery3))); // It Rearrange the query

        $this->query = $Rquery4;


    }


  }

  public function connect()
  {
    if (isset($this->config['driver']))
    {

      if (!empty($this->config['driver']))
      {
        $this->driver = strtoupper($this->config['driver']);
      }

      if ($this->driver ==='PDO')
      {
        $this->pdo_connection();

      }

      //else connect using mysqli
      else
      {
        $this->mysqli_connection();
      }

    }

    else
    {
      $this->mysqli_connection();
    }
  }

  public function add_query($query)
  {

    if ($this->driver === 'PDO')
    {
      try
      {
        $this->pdo_query = $this->connect->query($query);#->fetchAll(PDO::FETCH_ASSOC);
        $this->num_of_rows = $this->pdo_query->rowCount();
        $this->insert_id = $this->connect->lastInsertId();
        $this->csv();
        $this->pdo_query->closeCursor();
      }
      catch (\Throwable $e)
      {
        $message = $e->getMessage();
        header("HTTP/1.0 400 Bad Request");
        die($message);
      }
    }
  }

  public function query()
  {
      if ($this->driver === 'PDO')
      {
        $query = $this->query;

        try
        {
          $this->pdo_query = $this->connect->prepare($query);
        }
        
        catch (\Throwable $e)
        {
          $message = $e->getMessage();
          header("HTTP/1.0 400 Bad Request");
          die($message);
        }
        
    }
  }

  public function param(...$args)
  {
    if ($this->driver === 'PDO')
      {
        try
        {
            $param = $this->pdo_query->bindParam(...$args);
            if ($param === false)
            {
                $error = $this->pdo_query->error ?: 'Number of elements in type definition string may not match number of bind variables OR other error may occur';
                $message = 'Query bind param failed: '.$error;
                header("HTTP/1.0 400 Bad Request");
                die($message);
            }
        }
        
        catch (\Throwable $e)
        {
          $message = $e->getMessage();
          header("HTTP/1.0 400 Bad Request");
          die($message);
        }
      }
  }

  public function run_all(...$args)
  {
      if ($this->driver === 'PDO')
      {
        try
        {
          $this->pdo_query->execute(...$args);
          $this->num_of_rows = $this->pdo_query->rowCount();
          $this->insert_id = $this->connect->lastInsertId();
          $this->csv();
          $this->pdo_query->closeCursor();
        }
        catch (\Throwable $e)
        {
          $message = $e->getMessage();
          header("HTTP/1.0 400 Bad Request");
          die($message);
        }
      }

  }

  public function run()
  {
      if($this->driver === 'PDO')
      {
        try
        {
          $this->pdo_query->execute();
          $this->num_of_rows = $this->pdo_query->rowCount();
          $this->insert_id = $this->connect->lastInsertId();
          $this->csv();
          $this->pdo_query->closeCursor();

        }
        catch (\Throwable $e)
        {
          $message = $e->getMessage();
          header("HTTP/1.0 400 Bad Request");
          die($message);
        }
        
      }
  }

  public function free_results()
  {
    $this->csv_header = '';
    $this->csv = '';
    $this->num_of_rows = 0;
    $this->insert_id = 0;

    $this->raw_result_query = array();
  }

  public function close()
  {
    if ($this->driver === 'PDO')
    {
      $this->connect = null;
    }
  }
}
