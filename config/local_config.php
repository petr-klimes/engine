<?php
/***********************************
 basic
 ***********************************/
$config["language"] = 1;
$config["version"] = 1;

/***********************************
 route
 ***********************************/
$config["route"]["ladenka"] = 1;
$config["route"]["smarty"] = 0;

/***********************************
 debug
 ***********************************/
$config["debug"]["ladenka"] = 1;
$config["debug"]["smarty"] = 0;

/***********************************
  db
 ***********************************/
$config["db"]["host"] = "localhost";
$config["db"]["username"] = "root";
$config["db"]["password"] = "";
$config["db"]["dbname"] = "engines";

/***********************************
 vendor
 ***********************************/
$config["vendor"]["dibi"]["use"] = 1;
$config["vendor"]["dibi"]["source"] = "dibi/dibi.min.php";
$config["vendor"]["smarty"]["use"] = 1;
$config["vendor"]["smarty"]["source"] = "smarty/Smarty.class.php";;
$config["vendor"]["nette"]["use"] = 0;
$config["vendor"]["nette"]["source"] = "nette/nette.min.php";
?>
