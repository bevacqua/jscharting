<?php
// Open the connection to the Database
// Function to connect to the DB with the database name option
function ConnectToMySql( $dbName="" ) {
    // These parameters must be set based on your MySQL settings
    $hostdb = 'localhost';   // MySQl host
    $userdb = 'root';    // MySQL username
    $passdb = 'pass';    // MySQL password
    $namedb =  $dbName ? $dbName : 'jscharting'; // MySQL database name

  	
    $link = mysqli_connect($hostdb, $userdb, $passdb,$namedb);
	
    if(mysqli_connect_errno()){
            die('Could not connect: ' . mysqli_connect_error());
    }
   
    return $link;
}
?>