<?php
	$conn=mysqli_connect("localhost","root","");
	$db=mysqli_select_db($conn,'addressdb');

    mysqli_set_charset($conn,'utf8');
    $sql = "select * from category";
	$result = mysqli_query( $conn, $sql);
?>
