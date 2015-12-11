<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Eagle Sighting Tracker | CIT-261 Team 4</title>
        <meta name="author" content="Eurico Costa">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="This website provides a high level overview of my understanding of the topics learned in CIT-261 Fall Semester 2015.">
        
        <link rel="stylesheet" type="text/css" media="screen" href="css/master.css" />
        
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
                        
                        markersArray.push(marker);
                    }
                }
            }
            
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
    </body>
</html>