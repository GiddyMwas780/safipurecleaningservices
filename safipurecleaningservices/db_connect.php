<?php

$db_host = 'localhost';      
$db_name = 'safipure_db';    
$db_user = 'root';           
$db_pass = '';               

//  connection
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
   
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
   
    error_log("Connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}
?>