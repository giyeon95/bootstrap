
<?php
error_reporting(E_ALL); 
ini_set('display_errors',1);
ini_set('max_execution_time', 300);

    $link=mysqli_connect("localhost","root","","school_db"); 
    if (!$link)  
    { 
    echo "MySQL ENTER ERROR !";
    echo mysqli_connect_error();
    exit();
    }  
    mysqli_set_charset($link,"utf8");  

    $sql = "SELECT p.SCHOOL_YEAR,p.SCHOOL_NUMBER,p.GRADE_STRING 
            FROM `schooltable` AS p 
            WHERE SCHOOL_YEAR=(
                SELECT MAX(SCHOOL_YEAR) 
                FROM `schooltable` 
                WHERE SCHOOL_NUMBER = p.SCHOOL_NUMBER)";

    $result=mysqli_query($link,$sql);  
    $array = array();
   
    $count = 0;
    $obj = new stdClass();

    while($row = mysqli_fetch_array($result)) {
    
        // $array['SCHOOL_YEAR'] = $row['SCHOOL_YEAR'];
        // $array['PUBLIC_OR_INDEPENDENT'] = $row['PUBLIC_OR_INDEPENDENT'];
        // $array['DISTRICT_NUMBER'] = $row['DISTRICT_NUMBER'];
        // $array['SCHOOL_NUMBER'] = $row['SCHOOL_NUMBER']; //primary Number
        // $array['SCHOOL_NAME'] = $row['SCHOOL_NAME'];
        // $array['SCHOOL_FACILITY_TYPE'] = $row['SCHOOL_FACILITY_TYPE'];
        // $array['SCHOOL_PHYSICAL_ADDRESS'] = $row['SCHOOL_PHYSICAL_ADDRESS'];
        // $array['SCHOOL_PROVINCE'] = $row['SCHOOL_PROVINCE'];
        // $array['SCHOOL_POSTAL_CODE'] = $row['SCHOOL_POSTAL_CODE'];
        // $array['SCHOOL_PHONE_NUMBER'] = $row['SCHOOL_PHONE_NUMBER'];
        // $array['SCHOOL_FAX_NUMBER'] = $row['SCHOOL_FAX_NUMBER'];
        // $array['SCHOOL_LATITUDE'] = $row['SCHOOL_LATITUDE'];
        // $array['SCHOOL_LONGITUDE'] = $row['SCHOOL_LONGITUDE'];
        // $array['ORGANIZATION_EDUCATION_LEVEL'] = $row['ORGANIZATION_EDUCATION_LEVEL'];
        // $array['GRADE_STRING'] = $row['GRADE_STRING'];
        // $array['HAS_ELEMENTARY_GRADES_FLAG'] = $row['HAS_ELEMENTARY_GRADES_FLAG'];
        // $array['HAS_SECONDARY_GRADES_FLAG'] = $row['HAS_SECONDARY_GRADES_FLAG'];

        $obj->$count = $row;

        $count++;
    }

    $myJSON = json_encode($obj, JSON_PRETTY_PRINT); 
    echo $myJSON;

    mysqli_close($link);

    

?>