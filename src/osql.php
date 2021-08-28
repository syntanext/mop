<?php
namespace mysql;

use mysql\configuration as config;
use Mysqli;
use Pdo;

/*  
 *  description:Run MYSQL query faster and get result in a reliable way.;
 *  Version: 1.0.0;
 *  Recommended php version: >= 7;
 * 
 * 
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
  public $multi_header_row = array();
  public $error = false;
  public $warning = false;
  public $error_message;
  public $warning_errno;
  public $warning_message;
  public $warning_sqlstate;
  public $connect;
  public $num_of_rows = 0;
  public $num_of_warnings = 0;
  public $insert_id = 0;
  public $multi_csv = array();
  public $multi_csv_header = array();



  //PRIVATE
  private $sql_type;
  private $first_error;
  private $prepare_query;
  private $pdo_query;

  //PROTECTED
  protected $config;
  public $raw_result_query;
  protected $multi_insert_id = array();
  protected $multi_num_of_rows = array();
  protected $multi_raw_result_query = array();
  protected $display_error = false;
  protected $runtime_error = false;
  protected $log_warning = false;
  protected $multi_query_error_index = 0;




  // Osql initialization method
  function __construct()
  {

     if (file_exists(__DIR__.'/osqlconfig.php'))
    {
        require __DIR__.'/osqlconfig.php';
        $configuration = new config;
        $this->config = $configuration->config();
    }

    $args = func_get_args();
    /*
    * check for display error
    *
    */
    if (isset($this->config['display_error']))
    {
        if ($this->config['display_error']===true)
        {
          $this->display_error = true;
        }
    }

    /*
    * check for log warning
    *
    */
    if (isset($this->config['log_warning']))
    {
        if ($this->config['log_warning']===true)
        {
          $this->log_warning = true;
        }
    }



    /*
    * check if connection should be made by PDO or not
    *
    */
    if (isset($args[4]) || isset($this->config['driver']))
    {
      if (!empty($args[4]))
      {
        $driver = strtolower($args[4]);
      }

      elseif (!empty($this->config['driver']))
      {
        $driver = strtolower($this->config['driver']);
      }

      if ($driver ==='pdo')
      {
        try {
          $this->connect = new PDO("mysql:host=$args[0];dbname=$args[3]", $args[1], $args[2]);
          $this->connect->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
          $this->sql_type = 'PDO';

        } catch (\Exception $e) {
          $message = "Database Connection Failed:" . $e->getMessage();
          $this->error($message);
        }

      }

      //else connect using mysqli
      else
      {
        try {
          $this->connect = new mysqli ($args[0],$args[1],$args[2],$args[3]);   //connect
          $this->sql_type = 'MYSQLI';

        }
        
        catch (Exception $e) {
            $message = "Database Connection Failed: " . $e->getMessage();   //reports a DB connection failure
            $this->error($message);
        }
      }

    }

    else
    {
      try {
            $this->connect = new mysqli ($args[0],$args[1],$args[2],$args[3]);   //connect
            $this->sql_type = 'MYSQLI';
          }
          
      catch (Exception $e) {
          $message = "Database Connection Failed: " . $e->getMessage();   //reports a DB connection failure
          $this->error($message);
      }
    }

  }

  /** PROTECTED METHOD */
  protected function error()
  {

    $args = func_get_args();

    if(isset($args[0]))
    {
      $message = $args[0];
    }
    $errors = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
    $errors = end($errors);
    $caller = $errors;
    $error_message = '';

    if (!$this->first_error === true || $this->runtime_error === true)
    {
      $error_message .= '<b>Osql Error: </b>'.$message.' on line '.'<b>'.$caller['line'].'</b> in <b>'.$caller['file'].'</b>: : : : : :'."\n";
      $this->first_error = true;
    }

    else
    {
      $error_message .= '<b>Osql Error: </b> This property or method'.' on line '.'<b>'.$caller['line'].'</b> can not get execute because of previous Osql error'.'</b> in <b>'.$caller['file'].'</b>: : : : : :'."\n";
    }
    
    $this->error_message .= $error_message;
    $this->error = true;
    if ($this->display_error || $this->runtime_error)
    {
        trigger_error($error_message, E_USER_ERROR);
    }

  }

  protected function csv()
  {
    if ($this->error)
    {
      $this->error();
    }

    else
    {
      $csv = '';
      $csv_header = '';
      $args = func_get_args();

      if($this->sql_type === 'MYSQLI')
      {
        $result = $args[0];

        //Get all Header row
        while ($fieldinfo = $result->fetch_field())
        {
          $csv_header .= $fieldinfo->name.",";
          array_push($this->header_row,$fieldinfo->name);
        }
        $csv_header = rtrim($csv_header, ",")."\n";

        //Get all Rows and colums
        $result->data_seek(0);
        $this->raw_result_query = [];
        while($row = $result->fetch_assoc())
        {
          $this->raw_result_query[] = $row;
          foreach ($row as $key => $value)
          {
            if ($value == '')
            {
              $csv .= ' '.",";
              $csv_header .= ' '.",";
            }
            
            else
            {
              $csv .= $value.",";
              $csv_header .= $value.",";
            }
          }
          $csv = rtrim($csv, ",")."\n";
          $csv_header = rtrim($csv_header, ",")."\n";
        }
        $this->csv = $csv;
        $this->csv_header = $csv_header;
      }

      elseif ($this->sql_type === 'PDO')
      {

        $count = $this->pdo_query->columnCount();

        //Get all Header row
        for ($a=0; $a < $count; $a++)
        {
          $columnName = $this->pdo_query->getColumnMeta($a);
          $csv_header .= $columnName['name'].",";
          array_push($this->header_row,$columnName['name']);
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
              $csv .= $value[$b].",";
              $csv_header .= $value[$b].",";
            }

          }
          $csv = rtrim($csv, ",")."\n";
          $csv_header = rtrim($csv_header, ",")."\n";
        }

        $this->csv = $csv;
        $this->csv_header = $csv_header;
      }
    }
  }

  protected function multi_query_csv($result)
  {
   
    do
    {
      $this->multi_query_error_index = $this->multi_query_error_index + 1;
      $affected = $this->connect->affected_rows;
      echo $this->multi_query_error_index;

      if ($affected >= 0)
      {
          array_push($this->multi_num_of_rows ,$affected);
          array_push($this->multi_csv,null);
          array_push($this->multi_csv_header,null);
          array_push($this->multi_header_row,null);

      }

      array_push($this->multi_insert_id,$this->connect->insert_id);


      if ($result = $this->connect->store_result())
      {
          $csv = '';
          $csv_header = '';
          $multi_header_row = [];

          //Get all Header row
          while ($fieldinfo = $result->fetch_field())
          {
            $csv_header .= $fieldinfo->name.",";
            $multi_header_row[] = $fieldinfo->name;
          }
          $csv_header = rtrim($csv_header, ",")."\n";
          array_push($this->multi_header_row,$multi_header_row);

          //Get all Rows and columns
          $result->data_seek(0);
          $multi_raw_result_query = [];

          while($row = $result->fetch_assoc())
          {
            $multi_raw_result_query[] = $row;
            foreach ($row as $key => $value)
            {
              if ($value == '') {
                $csv .= ' '.",";
                $csv_header .= ' '.",";
              }else {
                $csv .= $value.",";
                $csv_header .= $value.",";
              }
            }

            $csv = rtrim($csv, ",")."\n";
            $csv_header = rtrim($csv_header, ",")."\n";
          }

          array_push($this->multi_raw_result_query, $multi_raw_result_query);
          array_push($this->multi_csv,$csv);
          array_push($this->multi_csv_header,$csv_header);
          array_push($this->multi_num_of_rows ,$this->connect->affected_rows);


          $result->free_result();
      }
     
    }

     while ($this->connect->more_results() && $this->connect->next_result());
    
  }


  /** PUBLIC METHOD */
  public function multi_csv()
  {
    if (func_num_args() != 1)
      {
        $message = 'Multi csv expecting one argument';
        $this->runtime_error = true;
        $this->error($message);
      }

    else
    {
        $args = func_get_args();
        $index = $args[0];

        if (gettype($index) == 'integer')
        {
          
          if (array_key_exists($index,$this->multi_csv))
          {
            return $this->multi_csv[$index];
          }

          else
          {
            $message = 'query index does not exist in multi_query';
            $this->runtime_error = true;
            $this->error($message);
          }
        }

        else
        {
          $this->runtime_error = true;
          $message = 'Argument expected to be an integer.';
          $this->error($message);
        }
    }
  }

  public function multi_csv_header()
  {
    if (func_num_args() != 1)
      {
        $message = 'Multi csv expecting one argument';
        $this->runtime_error = true;
        $this->error($message);
      }

    else
    {
        $args = func_get_args();
        $index = $args[0];

        if (gettype($index) == 'integer')
        {
          
          if (array_key_exists($index,$this->multi_csv_header))
          {
            return $this->multi_csv_header[$index];
          }

          else
          {
            $message = 'query index does not exist in multi_query';
            $this->runtime_error = true;
            $this->error($message);
          }
        }

        else
        {
          $this->runtime_error = true;
          $message = 'Argument expected to be an integer.';
          $this->error($message);
        }
    }
  }

  public function multi_num_of_rows()
  {
    if (func_num_args() != 1)
    {
      $message = 'multi_num_of_rows expected one argument';
      $this->runtime_error = true;
      $this->error($message);
    }

    else
    {
      $args = func_get_args();
      $index = $args[0];

      if (gettype($index) == 'integer')
      {
        if (array_key_exists($index,$this->multi_num_of_rows))
        {
          return $this->multi_num_of_rows[$index];
        }

        else
        {
          $message = 'query index does not exist in multi_query';
          $this->runtime_error = true;
          $this->error($message);
        }
      }

      else
      {
        $this->runtime_error = true;
        $message = 'Argument expected to be an integer.';
        $this->error($message);
      }
    }
  }

  public function multi_insert_id()
  {
    if (func_num_args() != 1)
    {
      $message = 'multi_insert_id expected one argument';
      $this->runtime_error = true;
      $this->error($message);
    }

    else
    {
      $args = func_get_args();
      $index = $args[0];

      if (gettype($index) == 'integer')
      {
        if (array_key_exists($index,$this->multi_insert_id))
        {
          return $this->multi_insert_id[$index];
        }

        else
        {
          $message = 'query index does not exist in multi_query';
          $this->runtime_error = true;
          $this->error($message);
        }
      }

      else
      {
        $this->runtime_error = true;
        $message = 'Argument expected to be an integer.';
        $this->error($message);
      }
    }
  }

  public function multi_header_row()
  {
    if (func_num_args() != 1)
    {
      $message = 'multi_header_row expected one argument';
      $this->runtime_error = true;
      $this->error($message);
    }

    else
    {
      $args = func_get_args();
      $index = $args[0];

      if (gettype($index) == 'integer')
      {
        if (array_key_exists($index,$this->multi_header_row))
        {
          return $this->multi_header_row[$index];
        }

        else
        {
          $message = 'query index does not exist in multi_query';
          $this->runtime_error = true;
          $this->error($message);
        }
      }

      else
      {
        $this->runtime_error = true;
        $message = 'Argument expected to be an integer.';
        $this->error($message);
      }
    }
  }

  public function get_column()
  {
    if ($this->error)
    {
      $this->error();
    }

    else
    {
      if (func_num_args() != 1)
      {
        $message = 'Expecting one argument';
        $this->runtime_error = true;
        $this->error($message);
      }

      else
      {

        $args = func_get_args();
        $column = $args[0];
        $key_exist = false;

        if (gettype($column) == 'string')
        {
            $ColumnRow = array();
            $columnName = $column;

            if (array_key_exists($column,$this->raw_result_query[0]))
            {
              foreach ($this->raw_result_query as $row)
              {
                array_push($ColumnRow, $row[$column]);
              }
              $key_exist = true;
            }

            else
            {
              $message = 'Column <b>'.$column.' </b> does not exist';
              $this->runtime_error = true;
              $this->error($message);
            }

            if ($key_exist)
            {
              return $ColumnRow;
            }
        }

        elseif (gettype($column) == 'integer')
        {

          $ColumnRow = array(); //variable where the column you want is stored
          $row = str_getcsv($this->csv, "\n");
          $length = count($row);
          $key_exist = false;
          
          for($i=0;$i<$length;$i++) 
          {
              $data = str_getcsv($row[$i], ",");

              if (array_key_exists($column,$data))
              {
                array_push($ColumnRow, $data[$column]);
                $key_exist = true;
              }

              else
              {
                $message = 'Column <b>'.$column.'</b> does not exist';
                $this->runtime_error = true;
                $this->error($message);
              }
          }

          if ($key_exist)
          {
            return $ColumnRow;
          }
        }

        else
        {
          $message = 'Only string and integer is expected';
          $this->runtime_error = true;
          $this->error($message);
        }

      }

    }
  }

  public function multi_get_column()
  {
      if (func_num_args() != 2)
      {
        $message = 'Multi query expecting two argument';
        $this->runtime_error = true;
        $this->error($message);
      }

      else
      {

        $args = func_get_args();
        $index = $args[0];
        $column = $args[1];

        if (gettype($index) == 'integer')
        {
          
          if (array_key_exists($index,$this->multi_csv))
          {
            if (gettype($column) == 'string')
            {

              $ColumnRow = array();
              $columnName = $column;
              $key_exist = false;

              if (array_key_exists($column,$this->multi_raw_result_query[$index][0]))
              {
                foreach ($this->multi_raw_result_query[$index] as $row)
                {
                  array_push($ColumnRow, $row[$columnName]);
                }
                $key_exist = true;
              }

              else
              {
                $this->runtime_error = true;
                $message = 'Column <b>'.$column.' </b> does not exist at query index <b>'.$index.'</b> in multi_query';
                $this->error($message);
              }
              
              if ($key_exist)
              {
                return $ColumnRow[0];
              }
            }

            elseif (gettype($column) == 'integer')
            {

              
                $ColumnRow = array(); //variable where the column you want is stored
                $ColumnNum = $column;
                $row = str_getcsv($this->multi_csv[$index], "\n");
                $length = count($row);
                $key_exist = false;

                for($i=0;$i<$length;$i++) 
                {
                    $data = str_getcsv($row[$i], ",");

                    if (array_key_exists($column,$data))
                    {
                      array_push($ColumnRow, $data[$ColumnNum]);
                      $key_exist = true;
                    }

                    else
                    {
                      $this->runtime_error = true;
                      $message = 'Column <b>'.$column.' </b> does not exist at query index <b>'.$index.'</b> in multi_query';
                      $this->error($message);
                    }
                }

                if ($key_exist)
                {
                  return $ColumnRow[0];
                }
                
            }

            else 
            {
              $this->runtime_error = true;
              $message = 'Second argument expected to be string or integer, this is use to select column index in multi_query';
              $this->error($message);
            }

          }

          else
          {
            $message = 'query index does not exist in multi_query';
            $this->runtime_error = true;
            $this->error($message);
          }
        }

        else
        {
          $this->runtime_error = true;
          $message = 'First argument expected to be an integer, this is use to select query index in multi_query';
          $this->error($message);
        }

      }
  }

  public function add_query($query)
  {
    if ($this->error)
    {
      $this->error();
    }

    else
    {
      if ($this->sql_type === 'MYSQLI')
      {
          try 
          {
            $this->prepare_query = $this->connect->prepare($query);

            if ($this->prepare_query === false)
            {
                $message = 'Wrong query: '.$this->connect->error;
                $this->error($message);
            }

            else
            {
              try
              {
  
                $execute =  $this->prepare_query->execute();

                if ($execute === false)
                {
                  $message = 'Query execution failed: '.$this->prepare_query->error;
                  $this->error($message);
                }

                else
                {
                  $result = $this->prepare_query->get_result();
                  $this->num_of_rows = $this->prepare_query->affected_rows;
                  $this->insert_id = $this->connect->insert_id;

                  if($this->log_warning==true)
                  {
                    $warning_count = $this->connect->warning_count;
                    if ($warning_count > 0)
                    {
                      $this->warning = true;
                      $this->num_of_warnings = $warning_count;
                      $warning = $this->connect->get_warnings();
                      $warning_errno = array();
                      $warning_message = array();
                      $warning_sqlstate = array();

                      do 
                      {
                        array_push($warning_errno,$warning->errno);
                        array_push($warning_message,$warning->message);
                        array_push($warning_sqlstate,$warning->sqlstate);
                      }
                      while ($warning->next());

                      $this->warning_errno = $warning_errno;
                      $this->warning_message = $warning_message;
                      $this->warning_sqlstate = $warning_sqlstate;
                    }
                  }

                  if ($result)
                  {
                    $this->csv($result);
                  }
                }
              }

              catch (\Exception $e)
              {
                $this->error($e->getMessage());
              }
            }
          }
          catch (\Exception $e)
          {
            $this->error($e->getMessage());
          }
      }

      elseif ($this->sql_type === 'PDO')
      {
        try
        {
          $this->pdo_query = $this->connect->query($query);#->fetchAll(PDO::FETCH_ASSOC);
          $this->num_of_rows = $this->pdo_query->rowCount();
          $this->insert_id = $this->connect->lastInsertId();
          $this->pdo_csv();
          $this->pdo_query->closeCursor();
        }
        catch (\Exception $e)
        {
          $this->error($e->getMessage());
        }
      }
    }
  }

  public function query($query)
  {
    if ($this->error)
    {
      $this->error();
    }

    else
    {
      if ($this->sql_type === 'MYSQLI')
      {
        try 
        {
            $this->prepare_query = $this->connect->prepare($query);
            if ($this->prepare_query === false) {
                $message = 'Wrong query: '.$this->connect->error;
                $this->error($message);
            }
        }
        catch (\Exception $e)
        {
          $this->error($e->getMessage());
        }
      }

      elseif ($this->sql_type === 'PDO')
      {
        try
        {
          $this->pdo_query = $this->connect->prepare($query);
        }
        
        catch (\Exception $e)
        {
          $this->error($e->getMessage());
        }
        
      }
    }
  }

  public function multi_query($query)
  {
    if($this->sql_type === 'MYSQLI')
    {
      try
      {
            $result = $this->connect->multi_query($query);
            if ($result) {
              $this->multi_query_csv($result);
            }
      }
      
      catch (\Exception $e)
      {
        $message = $e->getMessage().' at query index <b>'.$this->multi_query_error_index .'</b>, this query and any other query that follow has failed';
        $this->error($message);
      }
    }

    else
    {
      $message = 'MULTI_QUERY: connection must be made using <b>MYSQLI</b>.';
      $this->runtime_error = true;
      $this->error($message);
    }
  }

  public function param(...$args)
  {
    if ($this->error)
    {
      $this->error();
    }

    else
    {
      if ($this->sql_type === 'MYSQLI')
      {
        try
        {
            $param = $this->prepare_query->bind_param(...$args);
            if ($param === false)
            {
                $error = $this->prepare_query->error ?: 'Number of elements in type definition string may not match number of bind variables OR other error may occur';
                $message = 'Query bind param failed: '.$error;
                $this->error($message);
            }

        }

        catch (\Exception $e)
        {
            $this->error($e->getMessage());
        }
      }

      elseif ($this->sql_type === 'PDO')
      {
        try
        {
            $param = $this->pdo_query->bindParam(...$args);
            if ($param === false)
            {
                $error = $this->pdo_query->error ?: 'Number of elements in type definition string may not match number of bind variables OR other error may occur';
                $message = 'Query bind param failed: '.$error;
                $this->error($message);
            }
        }
        
        catch (\Exception $e) {
          $this->error($e->getMessage());
        }
      }
      
    }
  }

  public function run_all(...$args)
  {
    if ($this->error)
    {
      $this->error();
    }

    else
    {
      if ($this->sql_type === 'PDO')
      {
        try
        {
          $this->pdo_query->execute(...$args);
          $this->num_of_rows = $this->pdo_query->rowCount();
          $this->insert_id = $this->connect->lastInsertId();
          $this->csv();
          $this->pdo_query->closeCursor();
        }
        catch (\Exception $e) {
          $this->error($e->getMessage());
        }
      }

      else
      {
        $message = 'RUN_ALL: connection must be made using <b>PDO</>';
      }
    }
  }

  public function run()
  {
    if ($this->error)
    {
      $this->error();
    }

    else
    {

      if ($this->sql_type === 'MYSQLI')
      {
        try {
  
            $execute =  $this->prepare_query->execute();

            if ($execute === false)
            {
              $message = 'Query execution failed: '.$this->prepare_query->error;
              $this->error($message);
            }

            else
            {
              $result = $this->prepare_query->get_result();
              $this->num_of_rows = $this->prepare_query->affected_rows;
              $this->insert_id = $this->connect->insert_id;

              if($this->log_warning==true)
              {
                $warning_count = $this->connect->warning_count;
                if ($warning_count > 0)
                {
                  $this->warning = true;
                  $this->num_of_warnings = $warning_count;
                  $warning = $this->connect->get_warnings();
                  $warning_errno = array();
                  $warning_message = array();
                  $warning_sqlstate = array();

                  do 
                  {
                    array_push($warning_errno,$warning->errno);
                    array_push($warning_message,$warning->message);
                    array_push($warning_sqlstate,$warning->sqlstate);
                  }
                  while ($warning->next());

                  $this->warning_errno = $warning_errno;
                  $this->warning_message = $warning_message;
                  $this->warning_sqlstate = $warning_sqlstate;
                }
              }

              if ($result)
              {
                $this->csv($result);
              }
            }
        }
        catch (\Exception $e)
        {
          $this->error($e->getMessage());
        }
      }
      
      elseif($this->sql_type === 'PDO')
      {
        try
        {
          $this->pdo_query->execute();
          $this->num_of_rows = $this->pdo_query->rowCount();
          $this->insert_id = $this->connect->lastInsertId();
          $this->csv();
          $this->pdo_query->closeCursor();

        }
        catch (\Exception $e) {
          $this->error($e->getMessage());
        }
        
      }
      
    }
  }

  public function log_warning()
  {
    if (func_num_args() != 1)
      {
        $message = 'Log warning expected one argument';
        $this->runtime_error = true;
        $this->error($message);
      }

    else
    {
      $args = func_get_args();
      if ($args[0] == true)
      {
        $this->log_warning = true;
      }
    }
  }

  public function close()
  {
    if ($this->sql_type === 'MYSQLI')
    {
      $this->connect->close();
    }

    elseif ($this->sql_type === 'PDO')
    {
      $this->connect = null;
    }
  }
}




?>
