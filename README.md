# Mysql Optimizer
mysql optimizer also known as **OSQL** is a **php query handling and manipulation** library providing easy and reliable way to manipulate query and get result in a fastest way.

## Recomended Requirement
- PHP >= 7

## Supported Query Handler
- MYSQLI
- PDO

## Geting Started
  you can actually run 4 lines of code and get your result with this library
  ```php
 $connect = new mysql\osql($DB_ADDRESS,$DB_USER,$DB_PASS,$DB_NAME);
 $connect->query("SELECT .....");
 $connect->run();
 $connect->get_column('name');
  ```
  **Over All Preview**
  ```php
    // SIMPLE PREVIEW
    
    // connect
    $firstconnection = new mysql\osql($DB_ADDRESS,$DB_USER,$DB_PASS,$DB_NAME);

    // query
    $query = "SELECT ....";
    $firstconnection->query($query);
    
    // Run query
    $firstconnection->run();
    
    // Get all column result as csv
    $firstconnection->csv;
    
    // Get column by name
    $firstconnection->get_column('columnname');
    
    // Get column by index
    $firstconnection->get_column(index);
    
    // Get number of affected or selected rows
    $firstconnection->num_of_rows;
    
    // IS THAT ALL? NO IS MORE THAN THAT
    // CHECK DOCUMENTATION FOR MORE
    
  ```
## Documentation
 * [Documentation](https://github.com/hazeezet/mysql/wiki)
 * [Reference](https://github.com/hazeezet/mysql/wiki/reference)
 * [Security](https://github.com/hazeezet/mysql/wiki/security)
 * [Video](https://www.youtube.com/watch?v=hyZr_QePqZs&list=PLcvp4KNBaSTS42ZOjOAJGpVMG8CKBRlTM)
 
## App Inventor Documentation
  * [Kodular](https://community.kodular.io/t/osql-mysql-freedom-is-here-injection-stop-and-bind-parameter-available/143704)


## Installation
  It can be **included** or **required** in any php file or download using composer
  > Composer install
  ```bash
  composer require hazeezet/mysql
  ```
  > Manual install

  download both files in src folder and place them anywhere in your folder directory then **include** or **reqiure** osql.php file.
  
  IF YOU GET UNKNOWN ERROR WITH THIS LIBRARY ON YOUR WEB SERVER...... YOU HAVE TO TRY THIS.
  
  ![osql_error](https://user-images.githubusercontent.com/52476329/133803606-93310987-82cb-464f-8186-d4bab7c9667c.png)
  
  Go to your cpanel, find change php version and click on it, when the page fullly loaded, first of all make sure you are using php >= 7.
  Then uncheck **mysqli** and check **nd_mysqli** save it and try again with the library , everything should work fine.
  
   **LICENSE**
   
   fork and feel free to pull request....
   
   OSQL is licensed under the [MIT License](http://opensource.org/licenses/MIT).

   Copyright 2021 hazeezet
