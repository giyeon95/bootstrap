<?php
	include "./dbConnect.php";
	
    $sql = "select * from category";
	$result = mysqli_query( $conn, $sql);
?>
