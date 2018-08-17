
<?php
    include "./dbConnect.php";
    include "./category.php";
?>


<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GoogleMap Project</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/myCss.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

</head>
<body>
    <h3>GoogleMap Project</h3> <hr>

<div id="map" style="float: left; width: 50%; height: 100%; ">My map will go here</div>


<div style="padding-left: 50px; overflow-y: scroll; height: 100%;">
    <div>
        <h3>Check Category</h3>
        <div>
            <input type="checkbox" name="category" value="school" id="scSchool" data-name="category-school" checked> School
            <input type="checkbox" name="category" value="park" id="scPark" data-name="category-park" checked> Park
            <input type="checkbox" name="category" value="restaurant" id="scRest" data-name="category-restaurant" checked> Restaurant
            <input type="checkbox" name="category" value="public" id="scPublic" data-name="category-publicTransportation" checked> PublicTransportation
            <input type="checkbox" name="category" value="shopping" id="scShopping" data-name="category-shoppingCenter" checked>ShoppingCenter
        </div>
    </div>

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

    <h3 id="headerSchool" class="categoryHeader">School</h3>
    <div id="insertSchool" class="categoryDiv"></div>

    <h3 id="headerPark" class="categoryHeader">Park</h3>
    <div id="insertPark"  class="categoryDiv"></div>

    <h3 id="headerRestaurant" class="categoryHeader">restaurant</h3>
    <div id="restaurant"  class="categoryDiv"></div>

    <h3 id="headerPublicTransportation" class="categoryHeader">publicTransportation</h3>
    <div id="publicTransportation"  class="categoryDiv"></div>

    <h3 id="headerSchoolShoppingCenter" class="categoryHeader">shoppingCenter</h3>
    <div id="shoppingCenter"  class="categoryDiv"></div>

</div>

<script>
    var homeMarker = [];
    var mMarker = [];
    var map;
    var startbool = false;

    const homeMarkerPath = "./img/homeMarker.png";
    const schoolMarkerPath = "./img/schoolMarker.png";
    const parkMarkerPath = "./img/parkMarker.png";
    const restaurantsMarkerPath = "./img/restaurantsMarker.png";
    const publicMarkerPath = "./img/publicMarker.png";
    const shoppingMarkerPath = "./img/shoppingMarker.png";

    $('.searchButton').click(function () {
        let address = $('.searchMap').val();

        if(!address) {
            alert("Please Insert Text Value");
        } else {            
            searchAddress(address,true,callDatabase);
        }
    });

    $('.addButton').click(function () {
        let address = $('.textaddress').val();  
        
        if(!address) {
            alert("Please Insert Text Value");
        } else {
            let check = confirm("Your requested address is : "+address);
            
            if(check) {
                searchAddress(address,false);
            } else {
                alert("Cancel");
            }
        } 
    });
 
    $('[type="checkbox"]').on('change',function () {
        
        let dataName = $(this).data('name');
        let pDiv = $(this);
        
        switch (dataName) {
            case 'category-school' : chooseDataName(0, dataName, pDiv); break;
            case 'category-park' : chooseDataName(1, dataName, pDiv); break;
            case 'category-restaurant' : chooseDataName(2, dataName, pDiv); break;
            case 'category-publicTransportation' : chooseDataName(3, dataName, pDiv); break;
            case 'category-shoppingCenter' : chooseDataName(4, dataName, pDiv); break;
            default : console.log('Check Box error!'); break;
        }
    });

    function chooseDataName(j, dataName, pDiv) {

         if(!(pDiv.prop('checked'))) { //check false 일때
                    $('#'+dataName+' > p').each(function (i, item) {
                        $(this).addClass('gray');
                    });
                        categoryMarkerHidden(j, false);
                }
                else {
                    $('#'+dataName+' > p').each(function (i, item) {
                        $(this).removeClass('gray');
                    });
                        categoryMarkerHidden(j, true);
                }
    }

    $(document).on({
        click : function () {
            let pTag = $(this);
            let dataVal = $(this).data("mapFull");
            markerHidden(dataVal, pTag);
        },
        mouseover : function() {
            let dataVal = $(this).data("mapFull");
            animation(dataVal, true);
        },
        mouseout : function() {
            let dataVal = $(this).data("mapFull");
            animation(dataVal, false);
        }
    },'div > p');

    function initMap() {
            let loadPosition = {lat: 49.1444590, lng: -122.8202910}; //basic lat, lng
            map = new google.maps.Map(
                document.getElementById('map'), {zoom: 15, center: loadPosition}); // 새로운 지도객체 생성 및 속성 추가
               
                   let marker = new google.maps.Marker({position: loadPosition, map : map, icon : homeMarkerPath});
                    homeMarker.marker = marker;
                      
            google.maps.event.addListener(map, 'dragend', function() {
            callDatabase();
        });
    }

    function addMarker() {
       
       $.each(mMarker, function(i,item) {
           
            let category = item.category;
            let displayName = item.displayName;
            let fullAddress = item.fullAddress;
            let number = item.num;
        
            if(category == "school") { icon = schoolMarkerPath; }
            if(category == "park") { icon = parkMarkerPath; }
            if(category == "restaurants") { icon = restaurantsMarkerPath; }
            if(category == "publicTransportation") { icon = publicMarkerPath; }
            if(category == "shoppingCenter") { icon = shoppingMarkerPath; }
        
            let marker = new google.maps.Marker({
                
                position : mMarker[i].latlng,
                map:map,
                draggable:false,
               
                title:mMarker[i].fullAddress,
                icon:icon,
                label:number.toString()
            });
            item.markers = marker;

            marker.addListener('mouseover', function() {
                let dataVal = marker.getTitle();
                let mappingText = $('p[data-map-full="'+dataVal+'"]');
                mappingText.addClass('hilight');
            });

             marker.addListener('mouseout', function() {
                let dataVal = marker.getTitle();
                let mappingText = $('p[data-map-full="'+dataVal+'"]');
                let parentId = mappingText.parent().attr('id');
                mappingText.removeClass('hilight');            
                mappingText.removeClass('gray');  
                $('[data-name="'+parentId+'"]').prop('checked',true);

            });

            marker.addListener('click',function() {
                let dataVal = marker.getTitle();
                let mappingText = $('p[data-map-full="'+dataVal+'"]');
                let parentId = mappingText.parent().attr('id');
                let countList = $("#"+parentId+" > p").length;
                let count = 0;
                $.each(mMarker, function(i, item) {
                    if(dataVal == item.fullAddress) {
                        
                        if(mappingText.hasClass('gray')) {
                            item.markers.setVisible(false);
                            mappingText.removeClass('hilight');

                        } else{
                            mappingText.addClass('gray');
                        }   
                    }           
                });

                $.each($("#"+parentId+" > p"), function(i, item) {
                    if($(this).hasClass('gray')) {
                        count ++;  
                    }
                }); 
                if(countList == count) { $('[data-name="'+parentId+'"]').prop('checked',false);}
               
            });

        });
    }
    
    function addView() {

        let schoolHtml = ['<div id='+'category-school'+'>'];
        let parkHtml = ['<div id='+'category-park'+'>'];
        let restaurantHtml = ['<div id='+'category-restaurant'+'>'];
        let publicTransportationHtml = ['<div id='+'category-publicTransportation'+'>'];
        let shoppingCenterHtml = ['<div id='+'category-shoppingCenter'+'>'];
        
        $.each(mMarker, function(i, item) {
            if(item.category=="school") { schoolHtml.push('<p data-map-num="'+i+'" data-map-full="'+item.fullAddress+'">'+i+' : '+ item.displayName +'</p>'); }
            if(item.category=="park") { parkHtml.push('<p data-map-num="'+i+'" data-map-full="'+item.fullAddress+'">'+i+' : '+ item.displayName +'</p>'); }
            if(item.category=="restaurants") { restaurantHtml.push('<p data-map-num="'+i+'" data-map-full="'+item.fullAddress+'">'+i+' : '+ item.displayName +'</p>'); }
            if(item.category=="publicTransportation") { publicTransportationHtml.push('<p data-map-num="'+i+'" data-map-full="'+item.fullAddress+'">'+i+' : '+ item.displayName +'</p>'); }
            if(item.category=="shoppingCenter") { shoppingCenterHtml.push('<p data-map-num="'+i+'" data-map-full="'+item.fullAddress+'">'+i+' : '+ item.displayName +'</p>'); }
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
    }
    
    function searchAddress(addr, find, callback) {
    
        let geocoder = new google.maps.Geocoder();
      
        geocoder.geocode({          
            address:addr
        }, function(results, status) {
            if(status == google.maps.GeocoderStatus.OK) {
                let latlng  = results[0].geometry.location; // reference LatLng value
                let fullAddress = results[0].formatted_address;
                console.log(results[0]);
                map.setCenter(latlng);
                homeMarker.marker.setMap(null);
                let marker = new google.maps.Marker({position: latlng, map : map, icon : homeMarkerPath});
                homeMarker.marker = marker;
                if(find) {
                    map.setCenter(latlng);
                   
                    callback();
                } else {
                    let insert = valueChange(latlng, addr);
                    let inaddr = addr.toString();
                    let lat = Number(insert.latValue.toString());
                    let lng = Number(insert.lngValue.toString());
                    let saveAddress = insert.saveAddress.toString();
                    let categoryCheck =  $('.categoryList').val().toString();
                    let infulladdr = fullAddress.toString();
                  
                    $('#displayName').val(inaddr);  
                    $('#saveName').val(saveAddress);
                    $('input[name="category"]').val(categoryCheck);
                    $('input[name="latitude"]').val(lat);
                    $('input[name="longitude"]').val(lng);
                    $('input[name="fullAddress"]').val(infulladdr); 
                   
                    $('#myForm').submit();
                }
            } else {
                alert("Wrong Address, Please Check Address return");
            }
        });
    }

    function categoryMarkerHidden(i, boolType) {

        switch(i) {
            case 0 : 
                $.each(mMarker,function(i, item) {
                    if(item.category == 'school') { item.markers.setVisible(boolType); }
                });
                break;        
            case 1 : 
                $.each(mMarker,function(i, item) {
                    if(item.category == 'park') { item.markers.setVisible(boolType); }
                });
                break;
            case 2 :
                $.each(mMarker,function(i, item) {
                    if(item.category == 'restaurants') { item.markers.setVisible(boolType); }
                });
                break;
            case 3 :
                $.each(mMarker,function(i, item) {
                    if(item.category == 'publicTransportation') { item.markers.setVisible(boolType); }
                });
                break;
            case 4 :
                $.each(mMarker,function(i, item) {
                    if(item.category == 'shoppingCenter') { item.markers.setVisible(boolType); }
                });
                break;
            default :
                console.log("switch Error!");
                break;
        }
    }
    
    function callDatabase() {
        
        let x1 = map.getBounds().getSouthWest().lat();
        let y1 = map.getBounds().getSouthWest().lng();
        let x2 = map.getBounds().getNorthEast().lat();
        let y2 = map.getBounds().getNorthEast().lng();
        let addNum = 0;
       
        $.each(mMarker, function(i,item) {
            mMarker[i].markers.setMap(null);
        });

        mMarker=[];
       
        $.ajax({
            method: "POST",
            url: "./json/pin_json.php",
            data: { 
                x1: x1,
                x2: x2,
                y1: y1,
                y2: y2
            },
            success: function(data) { 
                $.each(data, function(i,item) {            
                        let obj = new Object();
                        obj.num = addNum;
                        obj.displayName = item.displayName;
                        obj.latlng = new google.maps.LatLng(item.latitude,item.longitude);
                        obj.fullAddress = item.fullAddress;
                        obj.category = item.category;
                        mMarker[addNum] = obj;
                        addNum++;
                });
                categoryEmpty();
                addMarker();
                addView();
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

    function animation(dataVal,bool) {
        $.each(mMarker, function(i, item) {
            if(dataVal == item.fullAddress) {
                if(!bool) {
                    item.markers.setAnimation(null);
                } else {
                    item.markers.setAnimation(google.maps.Animation.BOUNCE);
                }           
            }         
        });
    }

   

    function markerHidden(dataVal, pTag) {
       
        let parentId = pTag.parent().attr('id');
        let count = 0;
        let boolCheckbox = false;
        let childCount = $("#"+parentId+" > p").length;

        $("#"+parentId+" > p").each(function (i, item) {
            if($(this).hasClass('gray')) {
                boolCheckbox = true;
                count++;
            }
        });

        $.each(mMarker, function(i, item) {
            if(dataVal == item.fullAddress) {
                if(pTag.hasClass('gray')) {
                    pTag.removeClass('gray');
                    item.markers.setVisible(true);
                    count--;
                } else{
                    pTag.addClass('gray');
                    item.markers.setVisible(false);
                    count++;
                }
                
            }           
        });

        if(boolCheckbox) { $('[data-name="'+parentId+'"]').prop('checked',true); } 
        if(childCount == count) { $('[data-name="'+parentId+'"]').prop('checked',false); }

        startbool = true;
    }

    function categoryEmpty() {
        $('.categoryDiv').each(function (i, item) {
            $(this).empty();
        });
    }

</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBo1pWZUjkz8HmsvfUGyV69YsRHtwZwe7U&callback=initMap&language=en" type="text/javascript"></script>
</body>
</html>