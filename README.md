# Mysql Optimizer
mysql optimizer also known as **OSQL** is a **php query handling and manipulation** library providing easy and reliable way to manipulate query and get result in a fastest way.

## Recomended Requirement
- PHP >= 7

## Supported Query Handler
- MYSQLI
- PDO

## Geting Started
  **Over All Preview**
  ```php
    //connect using default driver
    $firstconnection = new mysql/osql($DB_ADDRESS,$DB_USER,$DB_PASS,$DB_NAME);
    
    // OR connect to pdo
    $firstconnection = new mysql/osql($DB_ADDRESS,$DB_USER,$DB_PASS,$DB_NAME,'pdo');
    
    //OR connect to mysqli
    $firstconnection = new mysql/osql($DB_ADDRESS,$DB_USER,$DB_PASS,$DB_NAME,'mysqli');
    
    //run query
    $query = "SELECT ....";
    $firstconnection->query($query);
    
    // run query with parameter
    $query = "SELECT .... WHERE name = ? OR ?";
    $firstconnection->query($query);
    
    //bind param to query if connection is by mysqli
    $name = 'my name';
    $name2 = 'second';
    $firstconnection->param('ss',$name,$second)
    
    //bind param to query if connection is by pdo and any supported bind param
    $name = 'my name';
    $firstconnection->param(:name,$name);
    $firstconnection->param(:name2,$name2);
    
    //OR bind param using an array and skip param() method if connection is by pdo also support associative array
    $arrayname = array('my name','second');
    $firstconnection->run_all($arrayname);
    
    // OR bind param using object and skip param() method if connection is by pdo
    $objectname = names() //object is reture
    $firstconnection->run_all($objectname);
    
    // Run any query
    $firstconnection->run();
    
    // Get result as csv
    $firstconnection->csv;
    
    // Get column by naame
    $firstconnection->column('columnname');
    
    // Get column by index
    $firstconnection->column(index);
    
    // IS THAT ALL? NO IS MORE THAN THAT
    // CHECK DOCUMENTATION FOR MORE
    
  ```
## Documentation
 [Documentation](https://github.com/hazeezet/mysql/wiki/)
 
## Installation
  It can be **included** or **required** in any php file or download using composer
  > Composer install
  ```
  composer require hazeezet/mysql
  ```
  > Manual install

  download both files in src folder and place them anywhere in your folder directory then **include** or **reqiure** osql.php file.
  
## Configuration
> Composer

open **osqlconfig.php** in **vendor/hazeezet/mysql/src/osqlconfig.php**

> Manual

open **osqlconfig.php** in the directory you place both file you download.

Three main settings are there which are
* [Log Warning](#log_warning) (Boolean) to log mysql warning if there is.
* [Display error](#display_error) (Boolean) to display any error if there is. 
* [Driver](#driver) (String) change the default driver to either **PDO** or **MYSQLI**
