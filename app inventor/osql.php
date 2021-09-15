<?php
/*
 * Written By: ShivalWolf
 * Date: 2011/06/03
 * Contact: Shivalwolf@domwolf.net
 *
 * UPDATE 2011/04/05
 * The code now returns a real error message on a bad query with the mysql error number and its error message
 * checks for magic_quotes being enabled and strips slashes if it is. Its best to disable magic quotes still.
 * Checks to make sure the submitted form is a x-www-form-urlencode just so people dont screw with a browser access or atleast try to
 * Forces the output filename to be JSON to conform with standards
 *
 * UPDATE 2011/06/03
 * Code updated to use the Web Module instead of tinywebdb
 *
 * UPDATE 2013/12/26 and 2014/02/18
 * minor modifications by Taifun, puravidaapps.com
 *
 * UPDATE 2014/07/11
 * mysql API (deprecated) replaced by mysqli by Taifun
 *
 * UPDATE 2015/04/30
 * SELECT logic adjusted (result stored in temp. file removed) by Taifun
 *
 * UPDATE 2016/02/21
 * Bugfix Undefined variable: csv
 *
 * UPDATE 2021/03/20
 * Reject mysql injection query BY Hazeezet... contact (hazeezet@gmail.com)
 * This updated script must be use with mysql function for better result and for more security
 */

/************************************CONFIG****************************************/
//DATABSE DETAILS//
$DB_ADDRESS="";
$DB_USER="";
$DB_PASS="";
$DB_NAME="";

//SETTINGS//
//This code is something you set in the APP so random people cant use it.
$SQLKEY="";

//DEFUALT INJECTION VALUES
$DefaultInjection = array("null","*","create","drop","truncate","1,1","https","http","top 0 ","top 1 ","benchmark","union","root","delay","true","false","getRequestString","schema","syscolums","sysobjects","dump","sleep","ascii","extractvalue","database","null","version","shutdown","declare","begin","end","not in","not exist","isnull","load","admin","convert","pytW"," 1 ","%","||"," 0 ","injectx");


//YOUR INJECTION VALUES eg tables name and columns name
$YourInjection = array(/*insert your tables, database and colums  name */ );

//MASTER KEY
//This key must come with your query if your $YourInjection is in the query
$Masterkey = "";


/************************************CONFIG****************************************/

//these are just in case setting headers forcing it to always expire
header('Cache-Control: no-cache, must-revalidate');

if(isset($_POST['query']) && isset($_POST['key'])){         //checks if the tag post is there and if its been a proper form post
      //set content type to CSV (to be set here to be able to access this page also with a browser)
    header('Content-type: text/csv');
    if($_POST['key']==$SQLKEY){
        $query=urldecode($_POST['query']);
        if(get_magic_quotes_gpc()){     //check if the worthless pile of crap magic quotes is enabled and if it is, strip the slashes from the query
            $query=stripslashes($query);
        }
        $conn = new mysqli($DB_ADDRESS,$DB_USER,$DB_PASS,$DB_NAME);    //connect
        try {
          $pdo = new PDO("mysql:host=$DB_ADDRESS;dbname=$DB_NAME", $DB_USER, $DB_PASS);     //connect PDO
        }catch (PDOException $e) {
            die("PDO Connection error:" . $e->getMessage());
         }



        if($conn->connect_error){                                                           //checks connection
            header("HTTP/1.0 400 Bad Request");
            echo "ERROR Database Connection Failed: " . $conn->connect_error, E_USER_ERROR;   //reports a DB connection failure
        }else {
            //STATEMENT CONFIGURATION... leave it as true...
            $YInjection = true;
            $DInjection = true;

            //IF YOUR QUERY CONTAIN ONE OF THOSE BELOW THEN MYSQL IS  GOING TO GIVE YOU AN ERROR.... please don't rearrange the variables......
            $Rquery0 = str_replace("="," = ",$query);  // It Rearrange the query
            $Rquery1 = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $Rquery0))); //It Rearrange the query
            $Rquery2 = str_replace( array( "' '","' '","''","'-'","'_'","'&'","'^'","'*'","'x'","1__","1--"," i ","--","|","?","'= 1'","1 = 1","#","' = '","top 1","x = x","1 = 0","x = y", ),'',$Rquery1 ); // It Rearrange the query
            $Rquery3 = trim(preg_replace('/" = "|""|= "|"|<!--.*?--> <!--.*?-->|"&"|"^"|"*"|"x"|[0-9]+ = [0-9]+|top 1 /', ' ', $Rquery2)); // Rearrange the query
            $Rquery4 = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $Rquery3))); // It Rearrange the query


            /****************YOUR INJECTION CHECKER START HERE********************/
            if (strlen(strstr($Rquery4,$Masterkey)) == 0){         // checks if your master key is in the query so you can run your exceptions (case sentitive)
                if (!empty($YourInjection)){                     // checks if your $YourInjection is empty
                    foreach ($YourInjection as $Injection) {
                        if (strlen(stristr($Rquery4,$Injection)) > 0) {
                            $YInjection = true;
                            break;
                        }else {
                            if ($Injection == end($YourInjection)){ // Its make sure all your injection is not in the query
                              $YInjection = false;

                            }
                        }
                    }
                }else{
                    $YInjection = false;
                }
            }else{
                    $YInjection = false;
            }

            /****************YOUR INJECTION CHECKER ENDS HERE********************/

            /****************DEFAULT INJECTION CHECKER START HERE********************/
            foreach ($DefaultInjection as $Injection) {          //checks if the query contain default injection
                if (strlen(stristr($Rquery4,$Injection)) > 0) {
                    $DInjection = true;
                    break;
                }else {
                    if ($Injection == end($DefaultInjection)){      // Its make sure all default injection is not in the query
                        $DInjection = false;
                    }
                }
            }
                /****************DEFAULT INJECTION CHECKER ENDS HERE********************/
            if ($YInjection){            //check if the query contain your injection Before running the query
                echo "Access denied";
            }elseif ($DInjection){       //check if the query contain default injection Before running the query
                echo "Access denied";
            }else {
                if (strlen(stristr($Rquery4,"RETURN2"))>0 && strlen(stristr($Rquery4,"SELECT"))>0){ //check if your procedure have a out parameter
                    $Rquery5 = str_replace(array($Masterkey,'RETURN2'),"",$Rquery4);
                    $splits = explode(";", $Rquery5);
                    $Fquery = $splits[0];
                    $Squery = $splits[1];
                    $stmt = $pdo->prepare($Fquery);
                    $stmt->execute();
                    $stmt->closeCursor();
                    $getrows = $pdo->query($Squery)->fetch(PDO::FETCH_ASSOC);
                    echo $getrows['row'];
                }else{
                    $Rquery5 = str_replace(array($Masterkey,'RETURN1'),"",$Rquery4);           //remove your masterkey from the query
                    $result=$conn->query($Rquery5);                                                     //runs the posted query
                    if($result === false){
                        echo "Wrong SQLs: " . $Rquery5 . " Error: " . $conn->error, E_USER_ERROR;          //errors if the query is bad and spits the error back to the client
                    }else{
                        if (strlen(stristr($Rquery4,"SELECT"))>0 || strlen(stristr($Rquery4,"RETURN1"))>0) {
                            $csv = '';                                                                    // bug fix Undefined variable: csv
                            while ($fieldinfo = $result->fetch_field()) {
                                $csv .= $fieldinfo->name.",";
                            }
                            $csv = rtrim($csv, ",")."\n";
                            echo $csv;                                                                    //prints header row
                            $csv = '';
                            $result->data_seek(0);
                            while($row = $result->fetch_assoc()){
                                foreach ($row as $key => $value) {
                                    $csv .= $value.",";
                                }
                                $csv = rtrim($csv, ",")."\n";
                            }
                            echo $csv;                                                                    //prints all data rows
                        }else {
                            header("HTTP/1.0 201 Rows");
                            echo "AFFECTED ROWS: " . $conn->affected_rows;
                        }
                    }
                        $conn->close();                                          //closes the DB
                }
            }
        }
    }else {
        echo "Bad Request";                                       //reports if the secret key was bad
    }
}else {
    echo "Bad Request";
 }
?>
