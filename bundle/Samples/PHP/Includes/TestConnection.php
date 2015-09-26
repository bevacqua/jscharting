<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text-html; charset=utf-8"/>
<?php
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
    else
    {
        echo 'Connected to DB: ' . $namedb;        
    }
   
    return $link;
}
$linkDB = ConnectToMySql();
$stmtDB = mysqli_stmt_init($linkDB);
$sqlStatement ='SELECT * FROM AreaData';
mysqli_stmt_prepare($stmtDB, $sqlStatement);
mysqli_stmt_execute($stmtDB);
$resultDB = mysqli_stmt_get_result($stmtDB) or die($stmtDB->error) ;
$rowCount = mysqli_num_rows($resultDB);
if($rowCount < 1){
    echo '<br/>No records.';
}
 else   
 {
     echo '<br/>Total records in AreaData: ' . $rowCount;
 }
?>
</head>
<body>	

</body>
</html>