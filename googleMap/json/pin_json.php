<?php
           include "../dbConnect.php";
           $x1 = $_POST['x1'];
           $x2 = $_POST['x2'];
           $y1 = $_POST['y1'];
           $y2 = $_POST['y2'];
             $sql = "select * from addinfo where '$x1' < latitude and latitude < '$x2' and '$y1' < longitude and longitude < '$y2'";
             $result = mysqli_query($conn, $sql);

             $count = 0;
             $obj = new stdClass();

             while($row = mysqli_fetch_object($result)){
                $obj->$count = $row;
                // $col = $row['col'];
                // $name = $row['displayName'];
                // $category = $row['category'];
                // $lat = $row['latitude'];
                // $lng = $row['longitude'];
                // $fulladdress = $row['fullAddress'];
                // echo $row."이래요.<br>";
                $count++;
            }
            mysqli_close($conn);

            $myJSON = json_encode($obj); 
            

           header("Content-Type: application/json");
echo $myJSON;
//echo $x1." ".$x2." ".$y1." ".$y2;
    ?>