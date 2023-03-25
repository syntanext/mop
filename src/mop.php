<?php
namespace Mysql;

use Mysql\configuration as config;
use Mysqli;
use Pdo;

/*  
 *  description:Run MYSQL query faster and get result in a reliable way.;
 *  Version: 1.1.0;
 *  Type: website version.
 *  Recommended php version: >= 7;
 *  website: https://github.com/bringittocode/mop
 *  contact: bringittocode@gmail.com
 */



  // Handling mysql error
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
/**
 * create an instance of mop and start using it's features
 * @link https://github.com/Bringittocode/mop
 */
class mop
{

    /**
     * Get query result as csv with the header row.
     * @var string
     * @link https://github.com/Bringittocode/mop/wiki/Reference#get-column-as-csv-with-header
     */
    public $csv_header;

    /**
     * Get query result as csv without the header row.
     * @var string
     * @link https://github.com/Bringittocode/mop/wiki/Reference#get-column-as-csv
     */
    public $csv;

    /**
     * Get query result header row.
     * @var array
     */
    public $header_row = array();

    /**
     * Get all query result header row,
     * Loop through it to get each statement header row.
     * @var array
     * @link https://github.com/Bringittocode/mop/wiki/Reference#multiple-query---get-all-result-header-row
     */
    public $multi_header_row = array();

    /**
     * Check if there is any error
     * @var bool
     * @link https://github.com/Bringittocode/mop/wiki/Reference#get-error
     */
    public $error = false;

    /**
     * Check if there is MYSQL Warning.
     * LOG WARNING must be enable.
     * 
     * Only works in MYSQLI Connection
     * @var bool
     * @link https://github.com/Bringittocode/mop/wiki/Reference#get-warning
     */
    public $warning = false;

    /**
     * Get error message if there is any error
     * @var string
     * @link https://github.com/Bringittocode/mop/wiki/Reference#get-error-message
     */
    public $error_message;

    /**
     * Get MYSQL Warning error number.
     * LOG WARNING must be enable.
     * 
     * Only works in MYSQLI Connection
     * @var int
     * @link https://github.com/Bringittocode/mop/wiki/Reference#get-warning-error-number
     */
    public $warning_errno;

    /**
     * Get MYSQL Warning message.
     * LOG WARNING must be enable.
     * 
     * Only works in MYSQLI Connection
     * @var string
     * @link https://github.com/Bringittocode/mop/wiki/Reference#get-warning-message
     */
    public $warning_message;

    /**
     * Get MYSQL Warning sql state.
     * LOG WARNING must be enable.
     * 
     * Only works in MYSQLI Connection
     * @var string
     * @link https://github.com/Bringittocode/mop/wiki/Reference#get-warning-sql-state
     */
    public $warning_sqlstate;

    /**
     * Get MYSQLI or PDO object depends on which driver is currently in use
     * @var object
     * @link https://github.com/Bringittocode/mop/wiki/Reference#perform-your-query
     */
    public $connect;

    /**
     * Get number of affected or selected row
     * @var int
     * @link https://github.com/Bringittocode/mop/wiki/Reference#get-number-of-rows
     */
    public $num_of_rows = 0;

    /**
     * Get number of Warnings that occur.
     * LOG WARNING must be enable.
     * 
     * Only works in MYSQLI Connection
     * @var int
     * @link https://github.com/Bringittocode/mop/wiki/Reference#get-warning-number
     */
    public $num_of_warnings = 0;

    /**
     * Get last insert Id of an inert statement
     * @var int
     * @link https://github.com/Bringittocode/mop/wiki/Reference#get-last-insert-id
     */
    public $insert_id = 0;

    /**
     * Get all query result as csv without the header row,
     * Loop through it to get each statement csv result
     * @var array
     * @link https://github.com/Bringittocode/mop/wiki/Reference#multiple-query---get-all-result-as-csv
     */
    public $multi_csv = array();

    /**
     * Get all query result as csv with the header row,
     * Loop through it to get each statement csv result
     * @var array
     * @link https://github.com/Bringittocode/mop/wiki/Reference#multiple-query---get-all-result-as-csv-with-header
     */
    public $multi_csv_header = array();

    /**
     * Get number of rows that was imported
     * @var int
     * @link https://github.com/Bringittocode/mop/wiki/Reference#import-csv-num-of-rows
     */
    public $import_csv_num_of_rows = 0;



    //PRIVATE
    /**
     * Use by MOP
     * 
     * Check if error has already occur
     * @var bool
     */
    private $first_error;

    /**
     * Use by MOP
     * 
     * Stores MYSQLI stuff,
     * It can be anything.
     */
    private $prepare_query;

    /**
     * Use by MOP
     * 
     * Stores PDO stuff,
     * It can be anything.
     */
    private $pdo_query;

    /**
     * Use by MOP
     * 
     * Stores the driver being used.
     * @var string
     */
    private $driver = 'MYSQLI';

    /**
     * Use by MOP
     * 
     * Stores the host name,
     * @var string
     */
    private $host;

    /**
     * Use by MOP
     * 
     * Stores the username
     * @var string
     */
    private $username;

    /**
     * Use by MOP
     * 
     * Stores the password of database
     * @var string
     */
    private $password;

    /**
     * Use by MOP
     * 
     * Stores the database name
     * @var string
     */
    private $databasename;

    //PROTECTED
    /**
     * Use by MOP
     * 
     * Stores the configuration settings in mopconfig file.
     * @var array
     */
    protected $config = array();

    /**
     * Use by MOP
     * 
     * Stores the query result has raw so that column can be gotten by it's name
     * @var array
     */
    protected $raw_result_query = array();

    /**
     * Use by MOP
     * 
     * Stores each last insert id of an insert statement in multi query
     * @var array
     */
    protected $multi_insert_id = array();

    /**
     * Use by MOP
     * 
     * Stores number of affected of selected row of each statement in multi query
     */
    protected $multi_num_of_rows = array();

    /**
     * Use by MOP
     * 
     * Stores the query result has raw so that column can be gotten by it's name,
     * in multi query
     * @var array
     */
    protected $multi_raw_result_query = array();

    /**
     * Use by MOP
     * 
     * Stores the display error info
     * @var bool
     */
    protected $display_error = true;

    /**
     * Use by MOP
     * 
     * Stores if there is internal error,
     * E.g trying to get column that does not exist
     * 
     * will trigger this and set it to true
     * @var bool
     */
    protected $runtime_error = false;

    /**
     * Use by MOP
     * 
     * Stores log warning info
     * @var bool
     */
    protected $log_warning = false;

    /**
     * Use by MOP
     * 
     * Stores the index of where error occured of any query in multi query
     * @var int
     */
    protected $multi_query_error_index = 0;

    /**
     * Use by MOP
     * 
     * Stores the seperator use to import csv string or file
     * @var string
     */
    protected $import_csv_seperator = ',';

    /**
     * Use by MOP
     * 
     * Stores the enclosure use to import csv string or file
     * @var string
     */
    protected $import_csv_enclosure = '"';

    /**
     * Use by MOP
     * 
     * Stores if provided csv is string or a file path.
     * 
     * FALSE for file path TRUE for string
     * @var bool
     */
    protected $import_csv_is_string = false;

    /**
     * Use by MOP
     * 
     * Stores the insert type use to import csv string or file
     * 
     * OPTIONS : UPDATE, IGNORE, REPLACE
     * @var string
     */
    protected $import_csv_insert_type = "UPDATE";


    // MOP initialization method
    /**
     * Represents a connection between PHP and a MySQL database. and an optional driver to use MYSQLI or PDO
     * @param string $DB_ADDRESS
     * Can be either a host name or an IP address
     * @param string $DB_USER
     * Database username
     * @param string $DB_PASS
     * Database password
     * @param string $DB_NAME
     * Database name
     * @param string $DRIVER
     * [optional] specify which driver to use. This will overide the driver specified in the config file
     * [options] MYSQLI or PDO
     * @link https://github.com/Bringittocode/mop/wiki/Reference#connecting
     */
    function __construct(
        string $DB_ADDRESS,
        string $DB_USER,
        string $DB_PASS,
        string $DB_NAME,
        string $DRIVER = "MYSQLI")
    {
        
        if (file_exists(__DIR__.'/mopconfig.php'))
        {
            require_once __DIR__.'/mopconfig.php';
            $configuration = new config;
            $this->config = $configuration->config();
        }

        $this->host = $DB_ADDRESS;
        $this->username = $DB_USER;
        $this->password = $DB_PASS;
        $this->databasename = $DB_NAME;

        //check for display error
        if (isset($this->config['display_error']))
        {
            if ($this->config['display_error']===false)
            {
                $this->display_error = false;
            }
        }

        //check for log warning
        if (isset($this->config['log_warning']))
        {
            if ($this->config['log_warning']===true)
            {
                $this->log_warning = true;
            }
        }

        //check if connection should be made by PDO or not
        if ((strtoupper($DRIVER) === 'PDO') || isset($this->config['driver']))
        {
            if (strtoupper($DRIVER) === 'PDO')
            {
                $this->driver = strtoupper($DRIVER);
            }

            elseif (strtoupper($DRIVER) === 'MYSQLI') {
                $this->driver = strtoupper($DRIVER);
            }

            elseif (!empty($this->config['driver']))
            {
                //just to make sure it really pdo the user change it to
                if (strtoupper($this->config['driver']) == 'PDO')
                {
                    $this->driver = "PDO";
                }
            }

            if ($this->driver ==='PDO')
            {
                $this->pdo_connection(3);

            }
            else
            {
                $this->mysqli_connection(3);
            }

        }

        else
        {
            $this->mysqli_connection(3);
        }
    }

    /** private METHOD */
    /**
     * USE BY MOP
     * 
     * construct the error message
     * @param string $message
     * Error message to send to user
     * @param int $index
     * Use for the trace back
     */
    private function error()
    {

        $args = func_get_args();

        if(isset($args[0]))
        {
            $message = $args[0];
        }


        if(isset($args[1]))
        {
            $index = $args[1];
        }
        else
        {
            $index = 2;
        }

        $errors = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,$index);
        $errors = end($errors);
        $caller = $errors;
        $error_message = '';

        if (!$this->first_error === true || $this->runtime_error === true)
        {
            $error_message .= '<b>MOP Error: </b>'.$message.' on line '.'<b>'.$caller['line'].'</b> in <b>'.$caller['file'].'</b>: : : : : :'."\n";
            $this->first_error = true;
        }

        else
        {
            $error_message .= '<b>MOP Error: </b> This property or method'.' on line '.'<b>'.$caller['line'].'</b> can not get execute because of previous MOP error'.'</b> in <b>'.$caller['file'].'</b>: : : : : :'."\n";
        }
        
        $this->error_message .= $error_message;
        $this->error = true;
        if ($this->display_error || $this->runtime_error)
        {
            trigger_error($error_message, E_USER_ERROR);
        }

    }

    /**
     * USE BY MOP
     * 
     * construct the error message for importing of csv string or file
     * @param string $message
     * Error message to send to user
     * @param int $index
     * Use for the DEBUG_BACKTRACE
     */
    private function import_csv_error()
    {
        $args = func_get_args();

        if(isset($args[0]))
        {
            $message = $args[0];
        }

        $index = $args[1] ?? 2;

        $errors = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,$index);
        $errors = end($errors);
        $caller = $errors;
        $error_message = '<b>MOP Error: </b>'.$message.' on line '.'<b>'.$caller['line'].'</b> in <b>'.$caller['file'].'</b>: : : : : :'."\n";
        if ($this->display_error)
        {
            trigger_error($error_message, E_USER_ERROR);
        }
    }

    /**
     * USE BY MOP
     * 
     * construct csv and other information from the query result
     * @param mixed $result
     * query result
     */
    private function csv()
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

            if($this->driver === 'MYSQLI')
            {
                $result = $args[0];

                //Get all Header row
                while ($fieldinfo = $result->fetch_field())
                {
                    $name = $fieldinfo->name;
                    $name = str_replace("\"","\"\"",$name);
                    $csv_header .= "\"$name\"".",";
                    array_push($this->header_row,$name);
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
                    if ($value === NULL)
                    {
                        $csv .= 'NULL'.",";
                        $csv_header .= 'NULL'.",";
                    }
                    elseif ($value === '')
                    {
                        $csv .= "\"\"".",";
                        $csv_header .= "\"\"".",";
                    }
                    
                    else
                    {
                        $col = $value;
                        $col = str_replace("\"","\"\"",$col);
                        $csv .= "\"$col\"".",";
                        $csv_header .= "\"$col\"".",";
                    }
                }
                    $csv = rtrim($csv, ",")."\n";
                    $csv_header = rtrim($csv_header, ",")."\n";
                }
                $csv = rtrim($csv, "\n");
                $csv_header = rtrim($csv_header, "\n");

                $this->csv = $csv;
                $this->csv_header = $csv_header;
                mysqli_next_result($this->connect);
            }

            elseif ($this->driver === 'PDO')
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
                    $this->raw_result_query[] = $value;
                    for ($b=0; $b < $count; $b++)
                    {
                        if ($value[$b] === NULL)
                        {
                            $csv .= 'NULL'.",";
                            $csv_header .= 'NULL'.",";
                        }
                        elseif ($value[$b] === '')
                        {
                            $csv .= "\"\"".",";
                            $csv_header .= "\"\"".",";
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

                $csv = rtrim($csv, "\n");
                $csv_header = rtrim($csv_header, "\n");

                $this->csv = $csv;
                $this->csv_header = $csv_header;
            }
        }
    }

    /**
     * USE BY MOP
     * 
     * Get the header row
     */
    private function csv_import_get_result_header()
    {
        if ($this->error)
        {
            $this->error();
        }
        else
        {
            if ($this->driver === 'PDO')
            {

                $count = $this->pdo_query->columnCount();

                //Get all Header row
                for ($a = 0; $a < $count; $a++)
                {
                    $columnName = $this->pdo_query->getColumnMeta($a);
                    $name = $columnName['name'];
                    $name = str_replace("\"", "\"\"", $name);
                    array_push($this->header_row, $name);
                }
            }
        }
    }

    /**
     * USE BY MOP
     * 
     * Start connection using MYSQLI
     * @param int $index
     * Use for the DEBUG_BACKTRACE
     */
    private function mysqli_connection()
    {
        $host = $this->host;
        $username = $this->username;
        $password = $this->password;
        $database = $this->databasename;
        $args = func_get_args();
        $index = $args[0];

        try
        {
            @$this->connect = new mysqli ($host,$username,$password,$database);   //connect
            $this->driver = 'MYSQLI';

        }
            
        catch (\Throwable $e)
        {
            $message = "Database Connection Failed: " . $e->getMessage();   //reports a DB connection failure
            $this->error($message,$index);
        }
    }

    /**
     * USE BY MOP
     * 
     * Start connection using PDO
     * @param int $index
     * Use for the DEBUG_BACKTRACE
     */
    private function pdo_connection()
    {
        $host = $this->host;
        $username = $this->username;
        $password = $this->password;
        $database = $this->databasename;
        $args = func_get_args();
        $index = $args[0];

        try
        {
            $this->connect = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            $this->connect->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $this->driver = 'PDO';

        }
        catch (\Throwable $e)
        {
            $message = "Database Connection Failed:" . $e->getMessage();
            $this->error($message,$index);
        }
    }

    /**
     * USE BY MOP
     * 
     * Start a new connection
     */
    private function new_connection()
    {
        $this->free_results();
        if ($this->driver === 'MYSQLI')
        {
            $this->mysqli_connection(4);
        }

        elseif ($this->driver === 'PDO')
        {
            $this->pdo_connection(4);
        }
    }

    /**
     * USE BY MOP
     * 
     * construct csv and other information from the query result in multi query
     * @param mixed $result
     * query result
     */
    private function multi_query_csv(mixed $result)
    {
    
        do
        {
            $this->multi_query_error_index = $this->multi_query_error_index + 1;
            $affected = $this->connect->affected_rows;

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
                    $name = $fieldinfo->name;
                    $name = str_replace("\"","\"\"",$name);
                    $csv_header .= "\"$name\"".",";
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
                        if ($value === NULL)
                        {
                            $csv .= 'NULL'.",";
                            $csv_header .= 'NULL'.",";
                        }
                        elseif ($value === '')
                        {
                            $csv .= "\"\"".",";
                            $csv_header .= "\"\"".",";
                        }
                        else
                        {
                            $col = $value;
                            $col = str_replace("\"","\"\"",$col);
                            $csv .= "\"$col\"".",";
                            $csv_header .= "\"$col\"".",";
                        }
                    }

                    $csv = rtrim($csv, ",")."\n";
                    $csv_header = rtrim($csv_header, ",")."\n";
                }

                $csv = rtrim($csv, "\n");
                $csv_header = rtrim($csv_header, "\n");

                array_push($this->multi_raw_result_query, $multi_raw_result_query);
                array_push($this->multi_csv,$csv);
                array_push($this->multi_csv_header,$csv_header);
                array_push($this->multi_num_of_rows ,$this->connect->affected_rows);
            }
        
        }

        while ($this->connect->more_results() && $this->connect->next_result());
        
    }

    /**
     * USE BY MOP
     * 
     * Run query used in csv_import() method
     */
    private function csv_import_run(...$args)
    {
    
        try
        {
            @$this->pdo_query->execute(...$args);
            $this->pdo_query->closeCursor();
        }
        catch (\Throwable $e)
        {
            $this->error = true;
            $this->error_message = $e->getMessage();
        }
    }

    /**
     * USE BY MOP
     * 
     * Run query and get header row used in import_csv() method
     */
    private function csv_import_get_header()
    {
    
        try
        {
            @$this->pdo_query->execute();
            $this->csv_import_get_result_header();
            $this->pdo_query->closeCursor();
        }
        catch (\Throwable $e)
        {
            $this->error = true;
            $this->error_message = $e->getMessage();
        }
    }

    /**
     * USE BY MOP
     * 
     * construct the csv string or file and execution the query
     * @param string $csv
     * csv string to process
     * @param array $column_name
     * array of column order
     * @param string $table_name
     * table name
     * @param bool $has header
     * If csv has header row or not
     */
    private function csv_import()
    {
        $args = func_get_args();
        $csv = $args[0];
        $columnName = $args[1];
        $table = $args[2];
        $has_header = $args[3];

        $row = str_getcsv($csv, "\n");
        $length = count($row);

        if($has_header)
        {
            $index = 1;
        }
        else
        {
            $index = 0;
        }

        $csv_column = str_getcsv($row[0], $this->import_csv_seperator, $this->import_csv_enclosure);
        $csv_column_count = count($csv_column);
        $columnName_count = count($columnName);

        if($columnName_count == $csv_column_count)
        {
            $bind = '';
            for ($i=0; $i < $columnName_count; $i++)
            { 
                $bind .= '?,';
            }
            $bind = rtrim($bind,',');

            $update_column = '';
            for ($i=0; $i < $columnName_count; $i++)
            {
                $update_column .= "`$columnName[$i]` = VALUES (`$columnName[$i]`),";
            }
            $update_column = rtrim($update_column,',');

            $column_order = '(';
            for ($i=0; $i < $columnName_count; $i++)
            {
                $column_order .= "`$columnName[$i]`,";
            }
            $column_order = rtrim($column_order,',');
            $column_order .= ')';

            $insert_type = strtoupper($this->import_csv_insert_type);
            if($insert_type === 'IGNORE')
            {
                $query = "INSERT IGNORE INTO $table $column_order VALUES ($bind)";
            }
            elseif($insert_type === 'REPLACE')
            {
                $query = "REPLACE INTO $table $column_order VALUES ($bind)";
            }
            else
            {
                $query = "INSERT INTO $table $column_order VALUES ($bind) ON DUPLICATE KEY UPDATE $update_column";
            }

        
            for($i=$index; $i<$length; $i++) 
            {
                $data = str_getcsv($row[$i], $this->import_csv_seperator, $this->import_csv_enclosure);
                $this->query($query);
                $this->csv_import_run($data);

                if($this->error)
                {
                    $message = $this->error_message;
                    $this->import_csv_error($message,3);
                    break;
                }
                $this->import_csv_num_of_rows = $this->import_csv_num_of_rows + 1;
            }
        }
        else
        {
            $message = 'Columns name does not match csv columns';
            $this->import_csv_error($message,3);
        }
    }



    /** PUBLIC METHOD */
    /**
     * Get single query result has csv without the header row in multi query
     * @param int $index
     * query index starting from index 0
     * @return string
     * @link https://github.com/Bringittocode/mop/wiki/Reference#multiple-query---get-result-as-csv
     */
    public function multi_csv(int $index)
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

    /**
     * Get single query result has csv with the header row in multi query
     * @param int $index
     * query index starting from index 0
     * @return string
     * @link https://github.com/Bringittocode/mop/wiki/Reference#multiple-query---get-result-as-csv-with-header-row
     */
    public function multi_csv_header(int $index)
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

    /**
     * Get single query number of affected or selected row in multi query
     * @param int $index
     * query index starting from index 0
     * @return int
     * @link https://github.com/Bringittocode/mop/wiki/Reference#multiple-query---get-number-of-affected-or-selected
     */
    public function multi_num_of_rows(int $index)
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

    /**
     * Get single query number of insert id in multi query
     * @param int $index
     * query index starting from index 0
     * @return int
     * @link https://github.com/Bringittocode/mop/wiki/Reference#multiple-query---get-last-insert-id
     */
    public function multi_insert_id(int $index)
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

    /**
     * Get single query header row in multi query
     * @param int $index
     * query index starting from index 0
     * @return array
     * @link https://github.com/Bringittocode/mop/wiki/Reference#multiple-query---get-result-header-row
     */
    public function multi_header_row(int $index)
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

    /**
     * Get column using it's index or name
     * @param mixed $column
     * column index or name
     * @return array
     * @link https://github.com/Bringittocode/mop/wiki/Reference#get-column
     */
    public function get_column(mixed $column)
    {
        if ($this->error)
        {
            $this->error();
        }

        else
        {
            
            if (gettype($column) == 'string')
            {
                $ColumnRow = array();
                $columnName = $column;
                if (isset($this->raw_result_query[0][$column]))
                {
                    foreach ($this->raw_result_query as $row)
                    {
                        if ($row[$column] === 'NULL')
                        {
                            array_push($ColumnRow, NULL);
                        }
                        else
                        {
                            array_push($ColumnRow, $row[$column]);
                        }
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
                        if ($data[$column] === 'NULL')
                        {
                            array_push($ColumnRow, NULL);
                        }
                        else
                        {
                            array_push($ColumnRow, $data[$column]);
                        }
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

    /**
     * Set the settings to use to import csv
     * @param mixed $insert_type
     * what to do if duplicate key if found
     * 
     * [OPTIONS] UPDATE, IGNORE, REPLACE, [default = UPDATE]
     * 
     * use NULL to skip
     * @param mixed $seperator
     * the seperator use to seperate each column.
     * 
     * [default = , ]
     * 
     * use NULL to skip
     * @param mixed $enclosure
     * the enclosure use to enclose each column.
     * 
     * [default = " ]
     * 
     * use NULL to skip
     * @link https://github.com/Bringittocode/mop/wiki/Reference#import-csv-settings
     */
    public function import_csv_settings(
        string $insert_type = NULL,
        string $seperator = NULL,
        string $enclosure = NULL)
    {
        $this->import_csv_insert_type = $insert_type == NULL ? 'UPDATE' : $insert_type;
        $this->import_csv_seperator = $seperator == NULL ? ',' : $seperator;
        $this->import_csv_enclosure = $enclosure == NULL ? '"' : $enclosure;
    }

    /**
     * import csv string or file to your database
     * @param bool $is_string
     * specify if your csv is string or a file
     * @param string $path
     * csv string or csv file path
     * @param string $table
     * database table name
     * @param array $columnName
     * [optional] specify the order of your column
     * 
     * use NULL to skip
     * @param bool $has_header
     * [optional] specify if your csv has the header row
     * 
     * [default] false
     * @link https://github.com/Bringittocode/mop/wiki/Reference#import-csv
     */
    public function import_csv(
        bool $is_string,
        string $path,
        string $table,
        array $columnName = NULL,
        bool $has_header = NULL
    )
    {
        if ($this->error)
        {
            $this->error();
        }

        else
        {
            $columnName = $columnName == NULL ? array() : $columnName;
            $has_header = $has_header == NULL ? false : $has_header;

            //store the driver use
            $driver = $this->driver;
            //change the driver to pdo
            $this->driver('pdo');

            if(empty($columnName))
            {
                // to get the header row for the import
                $query = "SELECT * FROM $table limit 1";
                $this->query($query);
                $this->csv_import_get_header();
                if($this->error)
                {
                    $message = $this->error_message;
                    $this->import_csv_error($message);
                }
                else
                {
                    $columnName = $this->header_row;
                }
            }

            if(!$this->error)
            {
                if(($is_string === false))
                {
                    if (file_exists($path))
                    {
                        $csv = file_get_contents($path);
                        $this->csv_import($csv,$columnName,$table,$has_header);
                    }
                    else
                    {
                        $message = 'csv path <b>'.$path.'</b> does not exist';
                        $this->runtime_error = true;
                        $this->error($message);
                    }
                }

                else
                {
                    $this->csv_import($path,$columnName,$table,$has_header);
                }
            }
            //change the driver back
            $this->driver($driver);
        }
    }

    /**
     * Get column of a query in multi query
     * @param int $index
     * query index starting from index 0
     * @param mixed $column
     * column name or index
     * @return array
     * @link https://github.com/Bringittocode/mop/wiki/Reference#multiple-query---get-column
     */
    public function multi_get_column(int $index, mixed $column)
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
                        if ($row[$columnName] === 'NULL')
                        {
                            array_push($ColumnRow, NULL);
                        }
                        else
                        {
                            array_push($ColumnRow, $row[$columnName]);
                        }
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
                    return $ColumnRow;
                }
            }

            elseif (gettype($column) == 'integer')
            {

            
                $ColumnRow = array(); //variable where the column you want is stored
                $row = str_getcsv($this->multi_csv[$index], "\n");
                $length = count($row);
                $key_exist = false;

                for($i=0;$i<$length;$i++) 
                {
                    $data = str_getcsv($row[$i], ",");

                    if (array_key_exists($column,$data))
                    {
                        if ($data[$column] === 'NULL')
                        {
                            array_push($ColumnRow, NULL);
                        }
                        else
                        {
                            array_push($ColumnRow, $data[$column]);
                        }
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
                    return $ColumnRow;
                }
                
            }

            else 
            {
                $this->runtime_error = true;
                $message = 'Column argument expected to be string or integer, this is use to select column index in multi_query';
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

    /**
     * Run a query without parameter to bind... this will automatically run and get result
     * @param string $query
     * query
     * @link https://github.com/Bringittocode/mop/wiki/Reference#add-new-query
     */
    public function add_query(string $query)
    {
        if ($this->error)
        {
            $this->error();
        }

        else
        {
            if ($this->driver === 'MYSQLI')
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

                        catch (\Throwable $e)
                        {
                            $this->error($e->getMessage());
                        }
                    }
                }
                catch (\Throwable $e)
                {
                    $this->error($e->getMessage());
                }
            }

            elseif ($this->driver === 'PDO')
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
                    $this->error($e->getMessage());
                }
            }
        }
    }

    /**
     * prepare your query
     * @param string $query
     * query
     * @link https://github.com/Bringittocode/mop/wiki/Reference#query
     */
    public function query(string $query)
    {
        if ($this->error)
        {
            $this->error();
        }

        else
        {
            if ($this->driver === 'MYSQLI')
            {
                try 
                {
                    $this->prepare_query = $this->connect->prepare($query);
                    if ($this->prepare_query === false)
                    {
                        $message = 'Wrong query: '.$this->connect->error;
                        $this->error($message);
                    }
                }
                catch (\Throwable $e)
                {
                    $this->error($e->getMessage());
                }
                

            }

            elseif ($this->driver === 'PDO')
            {
                try
                {
                    $this->pdo_query = $this->connect->prepare($query);
                }
                
                catch (\Throwable $e)
                {
                    $this->error($e->getMessage());
                }
                
            }
        }
    }

    /**
     * Run multiple query without parameter to bind... this will automatically run and get result
     * @param string $query
     * multiple query
     * @link https://github.com/Bringittocode/mop/wiki/Reference#multiple-query
     */
    public function multi_query($query)
    {
        $driver = $this->driver;
        $this->driver('mysqli');

        try
        {
            $result = $this->connect->multi_query($query);
            if ($result)
            {
                $this->multi_query_csv($result);
            }
        }
        
        catch (\Throwable $e)
        {
            $message = $e->getMessage().' at query index <b>'.$this->multi_query_error_index .'</b>, this query and any other query that follow has failed';
            $this->error($message);
        }

        $this->driver($driver);
        
    }

    /**
     * Bind parameter to your query.
     * 
     * bind according to the driver been used
     * @param mixed $parameter
     * parameter
     * @link https://github.com/Bringittocode/mop/wiki/Reference#bind-parameter
     */
    public function param(...$args)
    {
        if ($this->error)
        {
        $this->error();
        }

        else
        {
            if ($this->driver === 'MYSQLI')
            {
                try
                {
                    @$param = $this->prepare_query->bind_param(...$args);
                    if ($param === false)
                    {
                        $error = $this->prepare_query->error ?: 'Number of variables doesn\'t match number of parameters in prepared statement  OR other error may occur';
                        $message = 'Query bind param failed: '.$error;
                        $this->error($message);
                    }

                }

                catch (\Throwable $e)
                {
                    $this->error($e->getMessage());
                }
            }

            elseif ($this->driver === 'PDO')
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
                
                catch (\Throwable $e)
                {
                    $this->error($e->getMessage());
                }
            }
      
        }
    }

    /**
     * Run query with parameter of an array or object
     * 
     * When using this you don't need param or run method
     * @param mixed $parameter
     * parameter
     * @link https://github.com/Bringittocode/mop/wiki/Reference#advance-bind-parameter
     */
    public function run_all(...$args)
    {
        if ($this->error)
        {
            $this->error();
        }

        else
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
                    $this->error($e->getMessage());
                }
            }

            else
            {
                $message = 'RUN_ALL: connection must be made using <b>PDO</b>';
                $this->error($message);
            }
        }
    }

    /**
     * Run your query.
     * @link https://github.com/Bringittocode/mop/wiki/Reference#run-query
     */
    public function run()
    {
        if ($this->error)
        {
            $this->error();
        }

        else
        {

            if ($this->driver === 'MYSQLI')
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
                catch (\Throwable $e)
                {
                    $this->error($e->getMessage());
                }
                
            }
        
            elseif($this->driver === 'PDO')
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
                    $this->error($e->getMessage());
                }
                
            }
        
        }
    }

    /**
     * Set all result query to empty
     * @link https://github.com/Bringittocode/mop/wiki/Reference#free-results
     */
    public function free_results()
    {
        $this->csv_header = '';
        $this->csv = '';
        $this->header_row = array();
        $this->multi_header_row = array();
        $this->error_message = '';
        $this->warning_errno = '';
        $this->warning_message = '';
        $this->warning_sqlstate = '';
        $this->num_of_rows = 0;
        $this->num_of_warnings = 0;
        $this->insert_id = 0;
        $this->multi_csv = array();
        $this->multi_csv_header = array();

        $this->raw_result_query = array();
        $this->multi_insert_id = array();
        $this->multi_num_of_rows = array();
        $this->multi_raw_result_query = array();
        $this->multi_query_error_index = 0;
    }

    /**
     * On or off logging of mysql warning
     * 
     * Only support by MYSQLI connection
     * @param bool $log
     * TRUE to on , FALSE to off
     * @link https://github.com/Bringittocode/mop/wiki/Reference#log-warning
     */
    public function log_warning(bool $log)
    {
        if ($log == true)
        {
            $this->log_warning = true;
        }
        else
        {
            $this->log_warning = false;
        }
    }

    /**
     * On or off displaying of mysql error
     * @param bool $error
     * TRUE to on , FALSE to off
     * @link https://github.com/Bringittocode/mop/wiki/Reference#display-error
     */
    public function display_error(bool $error)
    {
        if ($error == true)
        {
            $this->display_error = true;
        }
        else
        {
            $this->display_error = false;
        }
    }

    /**
     * Change the driver been used
     * 
     * This will close the connection and reconnect
     * @param string $driver
     * [options] MYSQLI or PDO
     * @link https://github.com/Bringittocode/mop/wiki/Reference#driver
     */
    public function driver(string $driver)
    {
        
        $driver = strtoupper($driver);
        if ($driver == 'PDO')
        {
            $this->close();
            $this->driver = 'PDO';
            $this->new_connection();
        }
        else
        {
            $this->close();
            $this->driver = 'MYSQLI';
            $this->new_connection();
        }
    }

    /**
     * Change database host
     * 
     * This will close the connection
     * @param string $host
     * Database host name
     * @link https://github.com/Bringittocode/mop/wiki/Reference#change-host
     */
    public function change_host(string $host)
    {
        
        $this->close();
        $this->host = $host;
    }

    /**
     * Change database username
     * 
     * This will close the connection
     * @param string $username
     * Database username
     * @link https://github.com/Bringittocode/mop/wiki/Reference#change-username
     */
    public function change_username(string $username)
    {
        $this->close();
        $this->username = $username;
    }

    /**
     * Change database password
     * 
     * This will close the connection
     * @param string $password
     * Database password
     * @link https://github.com/Bringittocode/mop/wiki/Reference#change-password
     */
    public function change_password(string $password)
    {
    
        $this->close();
        $this->password = $password;
    }

    /**
     * Change database name
     * 
     * This will close the connection
     * @param string $name
     * Database name
     * @link https://github.com/Bringittocode/mop/wiki/Reference#change-database
     */
    public function change_db(string $name)
    {
        
        $this->close();
        $this->databasename = $name;
    }

    /**
     * Change all database information
     * 
     * This will close the connection
     * @param string $host
     * Database host
     * @param string $username
     * Database username
     * @param string $password
     * Database password
     * @param string $databasename
     * Database name
     * @link https://github.com/Bringittocode/mop/wiki/Reference#change-all-information
     */
    public function change_all(
        string $host,
        string $username,
        string $password,
        string $databasename)
    {
        
        $this->close();
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->databasename = $databasename;
    }

    /**
     * Reconnect the database
     * @link https://github.com/Bringittocode/mop/wiki/Reference#reconnect
     */
    public function reconnect()
    {
        $this->close();
        $this->new_connection();
    }

    /**
     * Close the connection
     * @link https://github.com/Bringittocode/mop/wiki/Reference#close-connection
     */
    public function close()
    {
        if ($this->driver === 'MYSQLI')
        {
            @$this->connect->close();
        }

        elseif ($this->driver === 'PDO')
        {
            $this->connect = null;
        }
    }
}

?>
