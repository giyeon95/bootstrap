<?php

    $conn=mysqli_connect("localhost","root","");
    $db=mysqli_select_db($conn,'addressdb');
    
    mysqli_set_charset($conn,'utf8');    
?>  