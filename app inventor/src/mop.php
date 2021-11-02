<?php
namespace mysql;

use mysql\configuration as config;
use Mysqli;
use Pdo;

if(!defined('MOP')){die('Direct access is not allow');}

/*  
 *  description:Run MYSQL query faster and get result in a reliable way.;
 *  Version: 2.1.3;
 *  Type: App inventor version.
 *  Recommended php version: >= 7;
 *  website: https://github.com/hazeezet/mysql
 *  contact: hazeezet@gmail.com
 * 
 * 
 */



  // Handling mysql error
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class mop
{

  //PUBLIC
    public $csv;
    public $connect;
    public $num_of_rows = 0;



  //PRIVATE
    private $prepare_query;
    private $pdo_query;

    private $driver = 'PDO';
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




  // Osql initialization method
  function __construct()
  {
   
      if (file_exists(__DIR__.'/mopconfig.php'))
      {
          require_once __DIR__.'/mopconfig.php';
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
        $message = "MOP require it's configuration file.";
        header("HTTP/1.0 206");
        die($message);
      }

  }


  private function csv()
  {

      $csv = '';

      if ($this->driver === 'PDO')
      {

        $count = $this->pdo_query->columnCount();
        $num_of_rows = $this->num_of_rows;

        if($count > 0)
        {
          for ($a=0; $a < $count; $a++)
          {
            
            $csv .= "\"$num_of_rows\"".",";
          }
          $csv = rtrim($csv, ",")."\n";
        }

        else
        {
          $csv .= "\"$num_of_rows\""."\n";
        }

        //Get all Rows and columns
        foreach ($this->pdo_query as $column => $value)
        {
          for ($b=0; $b < $count; $b++)
          {
            if ($value[$b] === NULL)
            {
              $csv .= 'NULL'.",";
            }
            elseif ($value[$b] === '')
            {
              $csv .= "\"\"".",";
            }
            
            else
            {
              $col = $value[$b];
              $col = str_replace("\"","\"\"",$col);
              $csv .= "\"$col\"".",";
            }

          }
          $csv = rtrim($csv, ",")."\n";
        }

        $csv = rtrim($csv, "\n");
        $this->csv = $csv;
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
      header("HTTP/1.0 206");
      die($message);
    }
  }


  /** PUBLIC METHOD */
  public function verify($query)
  {

    if($_POST['key'] != $this->sqlkey)
    {
        header("HTTP/1.0 206");
        die('Bad request');
    }

    elseif ($this->masterkey != $this->post_masterkey)
    {
        if (!empty($this->injection))
        {
            foreach ($this->injection as $Injection)
            {
                if (strlen(stristr($query,$Injection)) > 0)
                {
                    header("HTTP/1.0 206");
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
                header("HTTP/1.0 206");
                die('You may not have the permission to run this query');
                break;
            }
        }

        $Rquery0 = str_replace("="," = ",$query);  // It Rearrange the query
        $Rquery1 = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $Rquery0))); //It Rearrange the query
        $Rquery2 = str_replace( array( "' '","' '","''","'-'","'_'","'&'","'^'","'*'","'x'","1__","1--"," i ","--","|","'= 1'","1 = 1","#","' = '","top 1","x = x","1 = 0","x = y", ),'',$Rquery1 ); // It Rearrange the query
        $Rquery3 = trim(preg_replace('/" = "|""|= "|"|<!--.*?--> <!--.*?-->|"&"|"^"|"*"|"x"|[0-9]+ = [0-9]+|top 1 /', ' ', $Rquery2)); // Rearrange the query
        $Rquery4 = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $Rquery3))); // It Rearrange the query

        $this->query = $Rquery4;


    }


  }

  public function connect()
  {
      $this->pdo_connection();
  }

  public function add_query($query)
  {

    if ($this->driver === 'PDO')
    {
      try
      {
        $this->pdo_query = $this->connect->query($query);
        $this->num_of_rows = $this->pdo_query->rowCount();
        $this->insert_id = $this->connect->lastInsertId();
        $this->csv();
        $this->pdo_query->closeCursor();
      }
      catch (\Throwable $e)
      {
        $message = $e->getMessage();
        header("HTTP/1.0 206");
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
          header("HTTP/1.0 206");
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
          $this->csv();
          $this->pdo_query->closeCursor();
        }
        catch (\Throwable $e)
        {
          $message = $e->getMessage();
          header("HTTP/1.0 206");
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
          $this->csv();
          $this->pdo_query->closeCursor();

        }
        catch (\Throwable $e)
        {
          $message = $e->getMessage();
          header("HTTP/1.0 206");
          die($message);
        }
        
      }
  }

  public function free_results()
  {
    $this->csv = '';
    $this->num_of_rows = 0;
    $this->insert_id = 0;
  }

  public function close()
  {
    if ($this->driver === 'PDO')
    {
      $this->connect = null;
    }
  }
}
