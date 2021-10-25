
![MOP_logo](https://user-images.githubusercontent.com/52476329/137361314-296884d7-2b98-4069-b753-c20d15ac4c67.png)

# Mysql Optimizer
mysql optimizer also known as **MOP** is a **php query handling and manipulation** library providing easy and reliable way to manipulate query and get result in a fastest way.

## Recomended Requirement
- PHP >= 7

## Supported Query Handler
- MYSQLI
- PDO
## Upgrading

* [from version 1 to version 2 or after](https://github.com/hazeezet/mysql/wiki/upgrade#V1-to-V2)


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
## Tags and Version
 * tags with **aiv** means an update for app inventor eg 2.0.0-aiv
 * tags with nothing means an update for website eg 2.0.0
## Documentation
 * [Documentation](https://github.com/hazeezet/mysql/wiki)
 * [Reference](https://github.com/hazeezet/mysql/wiki/reference)
 * [Security](https://github.com/hazeezet/mysql/wiki/security)
 * [Video](https://youtube.com/playlist?list=PLJPXjarj_PAq1zGQpT8gOYqedDLsrjq9C)
 
## App Inventor Documentation
  * [Kodular](https://community.kodular.io/t/mop-mysql-freedom-is-here-injection-stop-and-bind-parameter-available/143704?u=azeez_ibrahim)
  * [Video](https://www.youtube.com/playlist?list=PLJPXjarj_PAqZYeVEkCaIayxQTWxCoXKw)

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
   
   MOP is licensed under the [MIT License](http://opensource.org/licenses/MIT).

   Copyright 2021 hazeezet
