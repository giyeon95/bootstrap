
<?php
    include "./category.php";
?>


<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        #map {
            width:100%;
            height:400px;
            background-color:#ccc;
        }
        a {
            background-color:#000;
            color:#fff;
            border-radius:10px;
            padding:8px;
            text-decoration:none;
            font-weight:700;
        }
        
        .category-school { color:Navy; }
        .category-park { color:green; }
        .category-restaurant { color:goldenrod; }
        .category-publicTransportation { color:purple; }
        .category-shoppingCenter {color : blue;}
    </style>
    
 
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

</head>
<body>
    <h3>GoogleMap Project</h3> <hr>

    
<div id="map">My map will go here</div>

<div>
    <h3>Search Address in Map</h3>
    Address : <input class="searchMap" type="text" hint="insert Address">
    <a href="#" class="searchButton">Search</a>
</div>

<div>
   <h3>Marker Add and Insert Database</h3>
   
   Address : <input class="textaddress" type="text" hint="insert Address">
   Category : 
   <select name="category" class="categoryList">
    <?php	
        while($row = mysqli_fetch_array($result)){
            echo '<option>'.$row['name'].'</option>';    
        }  
    mysqli_close($conn);
    ?>
    </select>

   <a href="#" class="addButton">Add Marker</a>
   <form method=post id="myForm" name=form action="./insertAddress.php">
   <INPUT TYPE ='hidden' name='displayName' value='' id='displayName'>
   <INPUT TYPE ='hidden' name='saveName' value=''  id='saveName'>
   <INPUT TYPE ='hidden' name='category' value=''  id='category'>
   <INPUT TYPE ='hidden' name='latitude' value=''  id='latitude'>
   <INPUT TYPE ='hidden' name='longitude' value=''  id='longitude'>
   <INPUT TYPE ='hidden' name='fullAddress' value=''  id='fullAddress'>
  
   </form>
</div>

<h3 class="category-school">School</h3>
<div id="insertSchool"></div>
<h3 class="category-park">Park</h3>
<div id="insertPark"></div>

<h3 class="category-restaurant">restaurant</h3>
<div id="restaurant"></div>

<h3 class="category-publicTransportation">publicTransportation</h3>
<div id="publicTransportation"></div>

<h3 class="category-shoppingCenter">shoppingCenter</h3>
<div id="shoppingCenter"></div>



  
<!--GoogleMap 연동 부문-->

<script>

    var displayName = [];
    var markers = [];
    var markerArray = [];
    var map;
    var iterator = 0;
    const homeMarkerPath = "./img/homeMarker.png";
    const schoolMarkerPath = "./img/schoolMarker.png";
    const parkMarkerPath = "./img/parkMarker.png";
    const restaurantsMarkerPath = "./img/restaurantsMarker.png";
    const publicMarkerPath = "./img/publicMarker.png";
    const shoppingMarkerPath = "./img/shoppingMarker.png";

    function initMap() {
            var loadPosition = {lat: 49.1444590, lng: -122.8202910}; //basic lat, lng
            map = new google.maps.Map(
                document.getElementById('map'), {zoom: 15, center: loadPosition}); // 새로운 지도객체 생성 및 속성 추가
               
              
                   var marker = new google.maps.Marker({position: loadPosition, map : map, icon : homeMarkerPath});
                
                //var marker = new google.maps.Marker({position: loadPosition, map: map}); // 새로운 마커 추가
        } // 지도 초기화 및 추가 함수 포함

    
    

    function addMarker(categoryIcon) {
        var icon;
        
        if(categoryIcon == "school") {
            icon = schoolMarkerPath; 
        }
        if(categoryIcon == "park") {
            icon = parkMarkerPath;
        }
        if(categoryIcon == "restaurants") {
            icon = restaurantsMarkerPath; 
        }
        if(categoryIcon == "publicTransportation") {
            icon = publicMarkerPath;
        }
        if(categoryIcon == "shoppingCenter") {
            icon = shoppingMarkerPath; 
        }
        
        var marker = new google.maps.Marker({
            position:markerArray[iterator],
            map:map,
            draggable:false,
            icon:icon
        });
        markers.push(marker);
        iterator++;
    }

    function searchAddress(addr, find) {
    
        var geocoder = new google.maps.Geocoder();
      
        geocoder.geocode({
            
            address:addr
        }, function(results, status) {
            if(status == google.maps.GeocoderStatus.OK) {
                var latlng  = results[0].geometry.location; // reference LatLng value
                var fullAddress = results[0].formatted_address;
                console.log("위도 경도 : "+latlng +" fullAddress :  "+fullAddress);

                map.setCenter(latlng);

                var marker = new google.maps.Marker({position: latlng, map : map});
                if(find) {
                    map.setCenter(latlng);
                } else {
                    var insert = valueChange(latlng, addr);
                    var inaddr = addr.toString();
                    var lat = Number(insert.latValue.toString());
                    var lng = Number(insert.lngValue.toString());
                    var saveAddress = insert.saveAddress.toString();
                    var categoryCheck =  $('.categoryList').val().toString();
                    var infulladdr = fullAddress.toString();
                    //보내야 되는 변수 : displayName, saveName,      category,              latitude, longitude, fullAddress
                    //Javascript 변수 :   address,  saveAddress , $('.categoryList').val() ,   lat,         lng,     fullAddress;
                    
                    $('#displayName').val(inaddr);  
                    $('#saveName').val(saveAddress);
                    $('input[name="category"]').val(categoryCheck);
                    $('input[name="latitude"]').val(lat);
                    $('input[name="longitude"]').val(lng);
                    $('input[name="fullAddress"]').val(infulladdr); 
                    alert(infulladdr);
                    $('#myForm').submit();
                    
                }
            } else {
                alert("error");
            }
        });
    }

    function valueChange(latlng, str) {
 
        let lookup = latlng.toString().slice(1,-1).split(',');
        let latValue = Number(lookup[0]);
        let lngValue = Number(lookup[1].trim());
        let saveAddress = str.replace(/(^ *)|( *$)/g, "").replace(/ +/g, " ").replace(/\s/g, "-").toLowerCase().replace(/\./g,"").replace(/\'/g, "");
        return {
            latValue : latValue,
            lngValue : lngValue,
            saveAddress : saveAddress
        };
    }


    $(document).ready(function () {
        $.getJSON('./json/data2.json', function(data) {
            var schoolHtml = ['<div class='+'category-school'+'>'];
            var parkHtml = ['<div class='+'category-park'+'>'];
            var restaurantHtml = ['<div class='+'category-restaurant'+'>'];
            var publicTransportationHtml = ['<div class='+'category-publicTransportation'+'>'];
            var shoppingCenterHtml = ['<div class='+'category-shoppingCenter'+'>'];

            var loadposition;
            var scNum = 1;
            var paNum = 1;
            var reNum = 1;
            var trNum = 1;
            var shNum = 1;
            var iterator =0;

            $.each(data, function(i,item) {
                if(item.category=="school") {
                    schoolHtml.push('<p>'+scNum+' : '+item.displayName+'</p>');
                    schoolHtml.push('<p>');
                    schoolHtml.push('savename : '+item.saveName+" category : "+item.category + " 위도 : "+item.latitude + " 경도 : "+item.longitude + " Full Address : "+item.fullAddress);
                    schoolHtml.push('</p>');
                    loadposition = {lat: item.latitude, lng: item.longitude};
                    scNum++;
                }
                if(item.category=="park") {
                    parkHtml.push('<p>'+paNum+' : '+item.displayName+'</p>');
                    parkHtml.push('<p>');
                    parkHtml.push('savename : '+item.saveName+" category : "+item.category + " 위도 : "+item.latitude + " 경도 : "+item.longitude + " Full Address : "+item.fullAddress);
                    parkHtml.push('</p>');
                    loadposition = {lat: item.latitude, lng: item.longitude};
                    paNum++;
                }
                if(item.category=="restaurants") {
                    restaurantHtml.push('<p>'+reNum+' : '+item.displayName+'</p>');
                    restaurantHtml.push('<p>');
                    restaurantHtml.push('savename : '+item.saveName+" category : "+item.category + " 위도 : "+item.latitude + " 경도 : "+item.longitude + " Full Address : "+item.fullAddress);
                    restaurantHtml.push('</p>');
                    loadposition = {lat: item.latitude, lng: item.longitude};
                    reNum++;
                }
                if(item.category=="publicTransportation") {
                    publicTransportationHtml.push('<p>'+trNum+' : '+item.displayName+'</p>');
                    publicTransportationHtml.push('<p>');
                    publicTransportationHtml.push('savename : '+item.saveName+" category : "+item.category + " 위도 : "+item.latitude + " 경도 : "+item.longitude + " Full Address : "+item.fullAddress);
                    publicTransportationHtml.push('</p>');
                    loadposition = {lat: item.latitude, lng: item.longitude};
                    trNum++;
                }
                if(item.category=="shoppingCenter") {
                    shoppingCenterHtml.push('<p>'+shNum+' : '+item.displayName+'</p>');
                    shoppingCenterHtml.push('<p>');
                    shoppingCenterHtml.push('savename : '+item.saveName+" category : "+item.category + " 위도 : "+item.latitude + " 경도 : "+item.longitude + " Full Address : "+item.fullAddress);
                    shoppingCenterHtml.push('</p>');
                    loadposition = {lat: item.latitude, lng: item.longitude};
                    shNum++;
                }
             
                displayName[i] = item.displayName;
                markerArray[i] = new google.maps.LatLng(item.latitude,item.longitude);
                addMarker(item.category);
            });
           
            schoolHtml.push('</div>');
            parkHtml.push('</div>');
            restaurantHtml.push('</div>');
            publicTransportationHtml.push('</div>');
            shoppingCenterHtml.push('</div>');
            $('#insertSchool').html(schoolHtml.join(''));
            $('#insertPark').html(parkHtml.join(''));
            $('#restaurant').html(restaurantHtml.join(''));
            $('#publicTransportation').html(publicTransportationHtml.join(''));
            $('#shoppingCenter').html(shoppingCenterHtml.join(''));
        });
    });
   
</script>

<!--DB 연동문-->
<script>
    $('.searchButton').click(function () {
        let address = $('.searchMap').val();
        if(!address) {
            alert("text 값을 입력해주세요.");
        } else {
            searchAddress(address,true);
        }
    });

    $('.addButton').click(function () {
        var address = $('.textaddress').val();
        if(!address) {
            alert("text 값을 입력해주세요.");
        } else {
           let check = confirm("요청하신 주소는 : "+address+"입니다.");
            if(check) {
                searchAddress(address,false);
            } else {
                alert("취소!");
            }
        }
    });
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBo1pWZUjkz8HmsvfUGyV69YsRHtwZwe7U&callback=initMap&language=en" type="text/javascript"></script>
</body>
</html>