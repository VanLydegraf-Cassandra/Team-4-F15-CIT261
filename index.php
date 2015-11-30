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
            /*********************************/
            
            window.addEventListener('load', function() {
                toggleOverlay();
                // get json data - going to call AJAX
                //loadData();

                // getting current location by geocoder
                var getGeoLocation = new google.maps.Geocoder();
                var currentPosition;

                if(navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        currentPosition = new google.maps.LatLng(position.coords.latitude, position.coords.longitude); 
                        initialize(currentPosition);
                    }, function() {
                        handleError(100);
                    });
                }
                else {
                    handleError(200);
                }
                // other tasks omitted
                 
                // display content
                //document.getElementById("startup").style.display = "none";
                //document.getElementById("siteContent").style.display = "inline";
                toggleOverlay();
                google.maps.event.trigger(currentMap, 'resize');
                currentMap.setCenter(location);
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
                // current position
                var mapMarker = new google.maps.Marker({
                    position : location,
                    map : currentMap,
                    zIndex : 255
                });
            }
            
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
            
            //loads the list of sightings in the local storage
            //TODO: Needs to changed to use the google maps javascript
            //API to set the sighting points on the map (Eurico).
            function loadSightingsList(){
                var key;
                var description = "<p>List of Sightings</p>";
                var countSightings = 0;
                var table = document.getElementById("sightingsTable");
                var json;
                
                //clear table
                clearAllRows();
                
                for(var i in localStorage){
                    var str = i.split("|");
                    
                    if(str[0] == "Sighting"){
                        json = JSON.parse(localStorage.getItem(i));
                        
                        var newRow = table.insertRow(-1);

                        // Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:
                        var cell1 = newRow.insertCell(0);
                        var cell2 = newRow.insertCell(1);
                        var cell3 = newRow.insertCell(2);

                        // Add some text to the new cells:
                        cell1.innerHTML = json.Date;
                        cell2.innerHTML = json.WildlifeSighted;
                        cell3.innerHTML = json.Location; 
                        countSightings++;
                    }
                }
                
                if(countSightings == 0){
                    var newRow = table.insertRow(-1);

                    // Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:
                    var cell1 = newRow.insertCell(0);
                    var cell2 = newRow.insertCell(1);
                    var cell3 = newRow.insertCell(2);

                    // Add some text to the new cells:
                    cell1.innerHTML = "No data to display";
                }
            }
            
            //Saves a new sighting on the browser's local storage        
            function saveSighting(){
                var sWildlifeSighted = document.getElementById("wildlifeSighted").value;
                var sLocation = document.getElementById("sightingLocation").value;
                var dDate = document.getElementById("when").value;
                
                var sJson = JSON.stringify({ WildlifeSighted : sWildlifeSighted, 
                    Location : sLocation, 
                    Date : dDate, 
                    Latitude: sLatitude, 
                    Longitude: sLongitude
                });
                    
                //save to local storage
                var key = 'Sighting' + '|' + sWildlifeSighted + '|' + dDate;
                localStorage.setItem(key, sJson);
                window.alert("Your sighting was successfuly saved.");
                
                //Add row to table
                var table = document.getElementById("sightingsTable");
                var newRow = table.insertRow(-1);

                // Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:
                var cell1 = newRow.insertCell(0);
                var cell2 = newRow.insertCell(1);
                var cell3 = newRow.insertCell(2);

                // Add some text to the new cells:
                cell1.innerHTML = dDate;
                cell2.innerHTML = sWildlifeSighted;
                cell3.innerHTML = sLocation; 
                
                divNewSighting.style.display = 'none';
            }
            
            //Add method to delete a selected sighting from the map (Eurico)
            function deleteSighting(){
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
    </body>
</html>