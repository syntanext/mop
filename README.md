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
    // SIMPLE PREVIEW
    
    // connect
    $firstconnection = new mysql/osql($DB_ADDRESS,$DB_USER,$DB_PASS,$DB_NAME);

    // query
    $query = "SELECT ....";
    $firstconnection->query($query);
    
    // Run query
    $firstconnection->run();
    
    // Get all column result as csv
    $firstconnection->csv;
    
    // Get column by naame
    $firstconnection->column('columnname');
    
    // Get column by index
    $firstconnection->column(index);
    
    // Get number of affected rows
    $firstconnection->num_of_rows;
    
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
