<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Eagle Sighting Tracker | CIT-261 Team 4</title>
        <meta name="author" content="Eurico Costa">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="This website provides a high level overview of my understanding of the topics learned in CIT-261 Fall Semester 2015.">
        
        <link rel="stylesheet" type="text/css" media="screen" href="css/master.css" />
        
        <style>
        //-----------------------------------------------------
        #display {
    border-radius: 30px;
    width: 0px;
    height: 0px;
    position: absolute;
    background-color: #AF802E;
    color: white;
    opacity:0;
    top: 100px;
    left: 100px;
    transition-property: opacity, top, left, width, height;
	  transition: all 2s 9999999s;
}
#mark{
    width: 40px;
    height: 40px; 
    top: 40px;
    left: 40px;
    position: absolute; 
}
#description{
    width:100%;
    height: 50%;
    margin: 3%;

    /* Firefox */
    display:-moz-box;
    -moz-box-pack:center;
    -moz-box-align:center;

    /* Safari and Chrome */
    display:-webkit-box;
    -webkit-box-pack:center;
    -webkit-box-align:center;

    /* W3C */
    display:box;
    box-pack:center;
    box-align:center;
}
#date{
  float: left;
  margin: 0% 0% 0% 10%;
}
#location{
  float: right;
  margin: 0% 10% 0% 0%;
}
//------------------------------------------------------
          html, body {
            height: 100%;
            margin: 0;
            padding: 0;
          }
          #map {
            height: 100%;
          }
        </style>
        
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBjmSgcVOnH9Vk5O2ovkZRJayNGX2uFD70"></script>
        <script>
            //Location Coordinates and google maps Global Variables
            /*********************************/
            var sLatitude, sLongitude;
            var currentMap;
            var currentPosition;
            var markersArray;
            /*********************************/
            
            window.addEventListener('load', function() {
                //toggleOverlay();

                // getting current location by geocoder
                //var getGeoLocation = new google.maps.Geocoder();

                if(navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        sLatitude = position.coords.latitude;
                        sLongitude = position.coords.longitude;
                        currentPosition = new google.maps.LatLng(position.coords.latitude, position.coords.longitude); 
                        initialize(currentPosition);
                    }, function() {
                        handleError(100);
                    });
                }
                else {
                    handleError(200);
                }
                 
                google.maps.event.trigger(currentMap, 'resize');
                currentMap.setCenter(currentPosition);
                //toggleOverlay();
            });
            
            function handleError(error){
            }
            
            //used to create an overlay while ajax is running
            function toggleOverlay(){
                var overlay = document.getElementById('overlay');
                //var specialBox = document.getElementById('specialBox');
                overlay.style.opacity = .6;
                
                if(overlay.style.display == "block"){
                    overlay.style.display = "none";
                } else {
                    overlay.style.display = "block";
                }
            }
            
            //function to initialize the map
            function initialize(location) {
                var mapOptions = {
                    zoom : 13,
                    center : location,
                    disableDefaultUI : true, // remove UI
                    scaleControl : true,
                    zoomControl : true,
                    panControl : true,
                    mapTypeId : google.maps.MapTypeId.ROADMAP
                };
                currentMap = new google.maps.Map(document.getElementById("map"), mapOptions);
                
                // current position marker
                var mapMarker = new google.maps.Marker({
                    position : location,
                    icon : "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
                    animation: google.maps.Animation.DROP,
                    map : currentMap,
                    draggable : true,
                    title : "Current Position",
                    zIndex : 255,
                });
                
                //load existing sightings
                loadSightingsToMap();
                
                //zoom to bounds
                var bounds = new google.maps.LatLngBounds();
                    
                for(i=0;i<markersArray.length;i++) {
                    bounds.extend(markersArray[i].getPosition());
                }

                currentMap.fitBounds(bounds);
            }
            
            //map initialize event listener
            google.maps.event.addDomListener(window, 'load', initialize);
            
            //map resize event listener
            google.maps.event.addDomListener(window, "resize", function() {
                var center = currentMap.getCenter();
                google.maps.event.trigger(currentMap, "resize");
                currentMap.setCenter(center); 
            });
            
            function getLocalTimestamp(){
                //set default timestamp - accounts for timezone offset.
                var currentTimestamp = new Date();
                // Find the current time zone's offset in milliseconds.
                var timezoneOffset = currentTimestamp.getTimezoneOffset() * 60 * 1000;

                // Subtract the time zone offset from the current UTC date, and pass
                //  that into the Date constructor to get a date whose UTC date/time is
                //  adjusted by timezoneOffset for display purposes.
                var localTimestamp = new Date(currentTimestamp.getTime() - timezoneOffset);
                var currentISOTimestampString = localTimestamp.toISOString().replace('Z', '');
                
                return currentISOTimestampString;
            }
            
            //load all the sightings onto the map
            function loadSightingsToMap(){
                var json;
                var marker;
                var oLatLong;
                
                markersArray = new Array();
                
                for(var i in localStorage){
                    var str = i.split("|");
                    
                    if(str[0] == "Sighting"){
                        json = JSON.parse(localStorage.getItem(i));
                        oLatLong = new google.maps.LatLng(json.Latitude, json.Longitude);
                        
                        marker = new google.maps.Marker({
                            position: oLatLong,
                            icon : "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
                            animation: google.maps.Animation.DROP,
                            map : currentMap,
                            zIndex : 255,
                            draggable : false,
                            sighting : "Sighted: " + json.WildlifeSighted + " on " + json.Date,
                            timeStamp : json.Date,
                            title : "Sighted: " + json.WildlifeSighted + " on " + json.Date,
                            id : i,
                        })
                        
                        marker.addListener('click', function() {
                          setTransition(true);
                        });
                        
                        markersArray.push(marker);
                    }
                }
            }
            
            //---------------------------------
            
            setData('Sample text Sample text', '01/01/2015', 'Rexburg, ID');
  
            function setData(descripion, date, location) {
              document.getElementById("description").innerHTML = 'Sample text Sample text';
              document.getElementById("date").innerHTML = '01/01/2015';
              document.getElementById("location").innerHTML = 'Rexburg, ID';
            }
            
            function setTransition(isHide) {
              if(isHide)
              {
                document.getElementById("display").style.WebkitTransform = "translate(-45px, -45px)";
                document.getElementById("display").style.opacity = 0;
                document.getElementById("description").style.opacity = 0;
                document.getElementById("date").style.opacity = 0;
                document.getElementById("location").style.opacity = 0;
                document.getElementById("display").style.width = "0px";
                document.getElementById("display").style.height = "0px";
                document.getElementById("display").style.transition = "all 2s"; 
                document.getElementById("description").style.transition = "all 1s"; 
                document.getElementById("date").style.transition = "all 1s"; 
                document.getElementById("location").style.transition = "all 1s"; 
              }
              else
              {
                document.getElementById("display").style.WebkitTransform = "translate(45px, 45px)";
                document.getElementById("display").style.opacity = 1;
                document.getElementById("display").style.width = "250px";
                document.getElementById("display").style.height = "100px";
                document.getElementById("display").style.transition = "all 2s"; 
                
                document.getElementById("description").style.opacity = 1;
                document.getElementById("date").style.opacity = 1;
                document.getElementById("location").style.opacity = 1;
                document.getElementById("description").style.transition = "all 6s"; 
                document.getElementById("date").style.transition = "all 6s"; 
                document.getElementById("location").style.transition = "all 6s"; 
              }
            }
            
            //---------------------------------
            
            //Saves a new sighting on the browser's local storage        
            function saveSighting(){
                var sWildlifeSighted = document.getElementById("wildlifeSighted").value;
                var sLocation = document.getElementById("sightingLocation").value;
                var dDate = document.getElementById("when").value;
                
                var sJson = JSON.stringify({ 
                        WildlifeSighted : sWildlifeSighted, 
                        Location : sLocation, 
                        Date : dDate, 
                        Latitude: sLatitude,
                        Longitude: sLongitude
                });
                    
                //save to local storage
                var key = 'Sighting' + '|' + sWildlifeSighted + '|' + dDate;
                localStorage.setItem(key, sJson);
                window.alert("Your sighting was successfuly saved.");
                
                //Add sighting to map
                // current position
                var mapMarker = new google.maps.Marker({
                    position : currentPosition,
                    map : currentMap,
                    zIndex : 255
                });
                
                //hide the form
                divNewSighting.style.display = 'none';
            }
        </script>
    </head>
    <body>
        <div id="header">
            <header role="banner" id="page-header"> <!-- ARIA roles -->
                    <?php include $_SERVER['DOCUMENT_ROOT'].'/modules/header.php'; ?>
            </header>
        </div>
        <div id="map"></div>
        
        <!-- Start Overlay -->
        <div id="overlay">
            <img src="http://soatheory.com/CIT-261-PersonalPortfolio/images/ajax-loader.gif" alt="" />
        </div>
        <!-- End Overlay -->
        
        
        
        
        
        <a href="javascript:setTransition(false)">
          <img id="mark" src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxAPEA8PDxINDhAQDw8QEA8ODA8PDw8RFBEYGBkRFBQYHCggGBolGxYVIjEhKSkrLi4vFx8zOjMsOCgtLisBCgoKDg0OFxAQGywlHiQtLC8sLCwrLCwsLCw3LC8sLC0sLCw2LS0sLCwsLCwsMCw3LywsLCwwLCwtLCwsLC03NP/AABEIAMwAzAMBIgACEQEDEQH/xAAbAAEAAgMBAQAAAAAAAAAAAAAAAgQDBQYBB//EAD4QAAIBAgMDBwoFBAEFAAAAAAECAAMRBBIhBTFhEyJBUXGBkQYjMkJSYnKCkqFDorHB0RQzY7JTc8LT4fH/xAAaAQEAAgMBAAAAAAAAAAAAAAAAAQQCAwYF/8QAKxEBAAICAQIEBQQDAAAAAAAAAAECAxEEITEFEkFRE2GBodEUscHwMlJx/9oADAMBAAIRAxEAPwD7jERAREQEREBETHXrLTUu5CqouWJ0EDJK2Kx1OkQHbnEXCKC1Rh1hRrbjNTjdqO+i5qSeFZ//ABjxb4Zz2Lx9NC1PVnJ51KiM7s3+Rid/xG8i1orG7TqDbosV5Q20RVHGo+v0IDfvImtqeUFY6BnY/wCOklMeDZz95quRxLDMwo4RPfPKVPFtPBZiejS3VMTiKvWEZlQ9w0+0p5PEMVPeft++mE3iG0O08Ydy4k9zfskgdu4pPSXEjtyj/amZpmweCO+m7cWCHv1mSnh8MNU5eketGKn8pmiPFcf+v3Y/FhvMP5XsDzsjDqem1M/Wpb/QTd4Pyho1Bc3QDe1w6LxLL6I4kCcWcOW9Gtn92uqv4ltfvK1TDshDFXosN1WgWZPo9Je4t2S1j52C/rr/AL+ezKLxL6kjhgGUhgRcEEEEdYMlPm2A2zVw9muDTYnztKzIzdOdNAT1jmtxnabI23TrhQSqu17Wa6ORvCk9PTlNj+st6ZtrERICIiAiIgIiICIiAiIgIieEwMeKxC0lLvuFtALsxOgVR0knQCaDF1yb1qxVMgLBSwyUFHrE9L9bdG4dZzvW5U8uxApqDyIOgCW1rNxIvbqXtM5zE11xHn6txhUbzFHc2KcfiN7gO7+d2rNmrir5pRM6ePWqYgFgxwuFBsaxuK1bgg9Uffs3SsuNSiOTwqCku7lCAajceEw4zFPWbM24aKo0VB1ATCFnM8jm3y23E/35e37/ADVrXmR2LG7Esesm5noWSCyQWVNMUQsmFkgskFmUVEQszU6jLuOnUdRPAskFmysaSi1BWJZPNVCLHQFKg6nU6MO3utKQD0mJpjK1r1KBJKOq656Z3kDf7S9Z3zZBZ7UphwA1wQQVcGzIw3EGejxOZfFOp6x7M62mHReTm31rKqu2pOVWYjNm/wCN/e0Nj61ug6ToZ8pcNRc1ABmUeeReatSmWHnVtuBNr29FrdYn0Dye2oK9MAnMwUEMbAum4MR7Q3HiOoie9FotEWr2lviW2iIkpIiICIiAiIgIiICa7arZyuHG5wWq2/4gdV+Y6dmabGaMYlVFfEubKczX9mjSBsOzR2+cwNZ5QVhVf+lvlpKgq4tl6Kd+bQHFiN3VbrmgxmJNVsxAUABUQbkUblEzYiq2SzaVKzf1NfrDOOZT7FTL4ysFnM+IcicuSax2hWyW3KAWTCyQWTCyjFWCIWSCyQWTCzOKpRCyQWSCyQWZxURCyYWSAkgJnFUohZILJASYEziqVfFUSwBUAulyl9zAixptwI0/+SpsPFnD1lyZijXqUQfS10ak3HQqR7SL1TagTT7VolS2XQg/1FMjeNQtQDsJpt3sZ6vh+XU/DntLZSfR9MpVA6q6kFWUMpG4gi4MnNJ5K4wVKNtNLMAPVD6lewNnA4ATdz020iIgIiICIiAiIgVdqVzTo1XX0lptlHW9rKPG05zbKgUqeHvzXqUqTH/FT5zn6UPjN9to+bQddeh4LVVv+2cvtqpzqQ9yu3fZV/R2mrPby47W+SLTqGrrOXZnO9iT2cJ4FkgskFnJ631lVRCyQWSCyYWZxURCyQWStaV6uOpr05j7v8yZ1XulYAnoE1dTah9VQO3WV32jUPrW7ABMfj0hHmhvgJICcy2Mqe23jPP6t/bb6jH6mvsjzOpAkgJyy4+qNzt43lintmqN5VviX+JnXlU9YlPnh0oErbQpgimeqoFPFagNMjs51+6UKG3l9dCOKm/2lrGY2nUoVsjKWFNmCk2a6i+49kt4M9PNE1lnW0LfkTXKvkPXUpkcbZ1/1rTt5wOwzlxlS27llYdhqFB9qs76dFPdYIiJAREQEREBERA122/Qp/8AWT73H6mclthvOUR106w/NTM6/bf9lm6KbU6p+GnUVm/KDOR20lmon2aj0z8yn91E0cqN4Lx8mN+0qwWTCyQWRrVVQXbuHSZzWojrKu9tKlfHgaLzj19H/uVMRimfgOofvKzGV75/SrGZZK1dm9Ik8OjwlcmGMgxleZ33YhaRLTyTVIQhPcpmdacyCnBpUyRklzkp4acJ0p2Mx4huY/wN+kutTmJsPnKp7bonczAfvM8dfNese8wRHV1WyFti24NTBHEV6f8AB8J3s4nYKZ8Rm9qvfuC1GJ+rk/GdtO6leIiJAREQEREBERAx16QdHRtQ6spHAixnE7UpM1FhvqIFcDrq0m1HeVI753U5vbGHKVGI9F/OLwbQOvZord7xqJjUjnauLUIHGuYAqOsEb5qatQsbtqZPG0eSqsmuU8+n8BOqj4TcW6iOuYpxvMi1Ms459P7tTt0nTwzGxkmMxMZUYosZDfPWMlTWShKmksJTimks00kJQWnMopTMlOZlpyEqvJTw0pd5OeGnCdNe1OeYOnesp6KatVPb6Kj6mv8AKZbqJYEnQDUk9EzbNw/NzNdeUIqNpzlpAc0W68pJt1vPU8JwfEzxae1ev4Z467l0PkphucX9lCB21CCfypT8Z0spbIwxp0hmFnbnsPZJ3J8osvyy7OqWSIiAiIgIiICIiAlXaWF5VCBYOpzUydwYdB4EXB4Ey1ED5/tfA8qvNGV1JNPN6rDRqbfoe4zn1a/QQQSGU71YbwZ9I2zs+96qAm9uUVRcm26oo6WA6N5AtvAnH7W2aWPKU7cpYXANlrKN2u4N1HuPDzfEeD+or56f5R94aslN9mkaYmMnmvfeCDYqRZlPUR0TG05aazWdT3VpRG+WaSyvTlykJBDPSWW6aTDSEuUlmKU0SZQs9QTMizKtdsmLLPCstCnKxDVWNOlpbSpV3rT91fafh0dPQDvxca+W0VpHVMRvsrCjyrlfw0INU9DHeKQ/U8NOmdBsnBGpUu3oqQ1Q9Z3rT/Rj2KOkzDhMHbJRpDW1xfnBBfWrUPSSevVjwuR0uEw60kCLew1JJuzMdSzHpJOs63i8avHxxSv1n3lYrXUM0REsMiIiAiIgIiICIiAiIgJp9p7Kvd6QvfVqVwLn2kJ0Vj0jceB1m4iB8+2jspK121RxzeUVbMPcqKf0PdOexuCq0fTW69FRLsh7elT2+Jn1fGbPSrqbo9rColg1uo30YcCCJpMbgmpEZspDZsrJcAkAkqVO45QTvO47pU5PCxcjraNT7wwtSLPnlE31GvZLlKS2zhxTxBygBaqioABYBhzWH2B+YyNIzleTgnDltjn0VZr5Z0u0pcpSlSMuUjKyYWkmQ4hFIBN2O5FUu57FGswhgBc6AC5PCbfYiClhuWZfOVb1WsAHOc8ynfguRe2en4dxP1Fp3OohspXzK9PA1av9y9Cmfw1YGs46mYaIOAJPES5h6F7UqCqAmhIHmqXD3m90d5HTfTZ71P7zBV6aVInXg1TQkcBbvmxp01UBVAVQLBVAAA4CdPiw0xRqkN8REdmHB4RaS2W5JN3dvSdvaP8AG4bhYSxETYkiIgIiICIiAiIgIiICIiAiIgJS2xRL0Xy6slqicWQ5svfYjsJl2IHzXylpA01qDXk3Bv08m9h+6nuM1NIzq9pYQecotuBekeKMLofoYDtQzjqJI0O9SVbtU2P3E8DxvD1rlj16T/CvmjtLY0mlyk01tNpbpvPBaluovKZKI31nWn8u9z3IGnWBc9SjTHoqTWYdGWnoq/WVPyTm9gpnrM/RSTIPjfU9+UD6p1OyRdqtTrbkl7KejfnLjuE6vwnD8PjxM97dfws441DaRIZ4zz0mxOJDNPc0CUTy89gIiICIiAiIgIiICIiAiIgIiIGg8o6HODj10K/NTuw/KanhPn+1qeSux6KoFQfFuYfYH5p9S2xRz0WtqyWqLxKG+XvFx2Ez515R0eaHH4bj6H0v4lT4ypz8PxePaPWOsfRhkjdWvpvLC1La9U16taZ6S8oyUh+I4X5d7H6Q05HHjnJetI9Z0q16zp1uwwaWHDkc9g1Zh7zegvhkWdBhDydNE9lQDxPSfG81G8006C2cj3ae4fWUPyS+HncVrFYisdoXYXeWkhUlMPJhpItipJh5UDSYaBbDyYeVA0mGgWg0lK4aTDQMsTwGewEREBERAREQEREBERAhU3ThNqYUecpHcM9E/Da6H6GUdqmd6wnMeUWGs2b20/NTu1voLn5ZMD52gNtd4uD2g2P3E2nk5RvVeofw0yr8T7/sPzSrjqeWq/U4FQcDazfcX+abnYC8nSDneQ9dv1VfAIO2eDwuJ5OZffavb69vsr0pq8tzhzd3boW1JeOT0j9ZYfKJbDSlhhlVV6QNeJ6T43mYNPdWFoNJhpWDSatAshpkDSspmRYFgNJhpgUTKimBlVplUyCU5nRIEkmSeAT2AiIgIiICIiAiIgIiICUds0M9FiBdktUXiVNyO8XX5jL0QPlW2MGWIVfVfKT/AIm3nwsZtqI5qjoZgT8FOxA+vIflnm2KHJ1GX2WNPuABQ/QyDtUydIWv7tqY4kaufrLD5BJ8sRuY7yjSwGk1aYqaky7QwxMhKKC8t0qJMs4fBy/Sw4ECimGmZcNLwQSVoFVcPMq0pltPYEQslEQEREBERAREQEREBERAREQEREDmPKWkBUWodxW4956d2CDiVLfTMFDAEKqneBrxO8nxvOjxuFSqFDgNldKi8GU3BhKQga7DYCbGjhgJYRRJwIqlpKIgIiICIiAiIgIiIH//2Q==">
        </a>
        <div id="display" onclick="setTransition(true)">
          <span id="description"></span><br>
          <span id="date"></span>
          <span id="location"></span>
        </div>
        
        
        
        
        
        
    </body>
</html>
