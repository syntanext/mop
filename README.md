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
    //connect
    $firstconnection = new mysql/osql($DB_ADDRESS,$DB_USER,$DB_PASS,$DB_NAME);
    
    // OR
    $firstconnection = new mysql/osql($DB_ADDRESS,$DB_USER,$DB_PASS,$DB_NAME,'pdo');
    
    //OR
    $firstconnection = new mysql/osql($DB_ADDRESS,$DB_USER,$DB_PASS,$DB_NAME,'mysqli');
    
    //run query
    $query = "SELECT ....";
    $firstconnection->query($query);
    
    // run query with parameter
    $query = "SELECT .... WHERE name = ?";
    $firstconnection->query($query);
    
    // OR when connection is made by pdo
    $query
    $firstconnection->query($query)
  ```
  **Table of contents**

  * [Installation](#installation)
  * [Configuration](#configuration)
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

open **osqlconfig.php** in the directory you place the both file you download.

Three main settings are there which are
* [Log Warning](#log_warning) (Boolean) to log mysql warning if there is.
* [Display error](#display_error) (Boolean) to display any error if there is. 
