<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Eagle Sighting Tracker | CIT-261 Team 4</title>
        <meta name="author" content="Eurico Costa">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="This website provides a high level overview of my understanding of the topics learned in CIT-261 Fall Semester 2015.">
        
        <link rel="stylesheet" type="text/css" media="screen" href="css/master.css" />
        
        <script>
            //Location Coordinates Global Variables
            /*********************************/
            var sLatitude, sLongitude;
            /*********************************/
            
            
            //Used to initiate the embedded google map. 
            //TODO: This will have to be changed to using the google map javascript 
            //API instead since this solution is going to more complex than just placing one single 
            //point on the map (Eurico).
            function initMap(lat,long) {
                var zoom = 13; //will need to validate this zoom value because now there's the potential to have a wider map since all the sightings are going to be shown on the map.
                var divMap = document.getElementById("map");
                
                //dev goople maps api url (no key information)
                //var gmUrl = 'https://www.google.com/maps/embed/v1/place?q=' + lat + ',' + long + '&zoom=' + zoom + '&maptype=roadmap';
                //prod goople maps api url (with api key information)
                var gmUrl = 'https://www.google.com/maps/embed/v1/place?key=AIzaSyBjmSgcVOnH9Vk5O2ovkZRJayNGX2uFD70&q=' + lat + ',' + long + '&zoom=' + zoom + '&maptype=roadmap';

                divMap.innerHTML = '<iframe id="map_frame" '
                                  + 'width="100%" height="400px" frameborder="0" border="1" scrolling="no" marginheight="0" marginwidth="0" '
                                  + 'src="' + gmUrl + '"></iframe>';
            }
            
            function enableManualLocation(){
                document.getElementById("sightingLocation").style.display = "block";
                document.getElementById("lblLocation").style.display = "block";
            }
            
            
            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(GetLocation);
                } else {
                    document.getElementById("locationDetails").style.color = "red";
                    document.getElementById("locationDetails").innerHTML = "Geolocation is not supported by this browser. You may enter your location manually in the location field.";
                    enableManualLocation();
                    //hide the map
                    document.getElementById("map").style.display = "none";
                }
            }
            
            
function setDescription(description) {
  document.getElementById("description").innerHTML = description;
}        
function setDate(date) {
  document.getElementById("date").innerHTML = date;
}        
function setLocation(location) {
  document.getElementById("location").innerHTML = location;
}

function setTransition(isHide) {
  if(isHide)
  {
    document.getElementById("display").style.WebkitTransform = "translateY(55px)";
    document.getElementById("display").style.opacity = 0;
    document.getElementById("description").style.opacity = 0;
    document.getElementById("date").style.opacity = 0;
    document.getElementById("location").style.opacity = 0;
    document.getElementById("display").style.width = "0px";
    document.getElementById("display").style.height = "0px";
    document.getElementById("display").style.transition = "all 2s"; 
    document.getElementById("description").style.transition = "all 2s"; 
    document.getElementById("date").style.transition = "all 2s"; 
    document.getElementById("location").style.transition = "all 2s"; 
  }
  else
  {
    document.getElementById("display").style.WebkitTransform = "translateY(-55px)";
    document.getElementById("display").style.opacity = 1;
    document.getElementById("display").style.width = "150px";
    document.getElementById("display").style.height = "50px";
    document.getElementById("display").style.transition = "all 2s"; 
    
    document.getElementById("description").style.opacity = 1;
    document.getElementById("date").style.opacity = 1;
    document.getElementById("location").style.opacity = 1;
    document.getElementById("description").style.transition = "all 2s"; 
    document.getElementById("date").style.transition = "all 2s"; 
    document.getElementById("location").style.transition = "all 2s"; 
  }
}
            
            
            function GetLocation(location) {
                /*Do your ajax calls, sorting or laoding, etc.*/
                toggleOverlay();
                
                document.getElementById("locationDetails").innerHTML = 
                        "Latitude: " + location.coords.latitude + 
                        "<br>Logitude: " + location.coords.longitude +
                        "<br>Accuracy: " + location.coords.accuracy + " (if >= 5000, the location and map will not be shown).<br>";
                
                //set global variables
                sLatitude = location.coords.latitude;
                sLongitude = location.coords.longitude;
                
                
                if(location.coords.accuracy >= 500000){
                    document.getElementById("locationDetails").style.color = "red";
                    document.getElementById("locationDetails").innerHTML += "<br>Current location accuracy is not sufficient to determine your location. Please enter your location manually in the location field below.";
                    enableManualLocation();
                    //hide the map
                    document.getElementById("map").style.display = "none";
                }
                else{
                    initMap(location.coords.latitude, location.coords.longitude);
                    //AJAX call to geolocation service to get location name from coordinates      
                    var url = "http://api.wunderground.com/api/22b4347c464f868e/geolookup/q/" + location.coords.latitude + "," + location.coords.longitude + ".json";
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (xhttp.readyState == 4 && xhttp.status == 200) {
                            //process location
                            try{
                                var json = JSON.parse(xhttp.responseText);
                                var city = json.location.city;
                                var state = json.location.state;
                                var country = json.location.country;
                                var location = city + ", " + state + ", " + country;
                                document.getElementById("sightingLocation").value = location;
                                document.getElementById("locationDetails").innerHTML += "<br>Location: " + location;
                                enableManualLocation();
                            }
                            catch(e){
                                //invalid JSON
                            }  
                        }
                    };

                    xhttp.open("GET", url, true);
                    xhttp.send(); 
                } 
                toggleOverlay();
            }
            
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
            
            //Sets the sighting entry form to visible, sets default values
            //and kicks the call for coordinates.
            function setNewSighting(){
                //show sighting form
                var sd = document.getElementById("divNewSighting");
                sd.style.display = "block";
                //get coordinates
                getLocation();
 
                //set sighting default timestamp
                document.getElementById('when').value = getLocalTimestamp();
            }
            
            //Clears all rows from the sightings table
            function clearAllRows(){
                var table = document.getElementById('sightingsTable');
                var tableRows = table.getElementsByTagName('tr');
                var rowCount = tableRows.length;

                for (var x=rowCount-1; x>0; x--) {
                   table.removeChild(tableRows[x]);
                }
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
                
                setLocation(sLocation);
                setDate(dDate);
                setDescription(sWildlifeSighted);
                
                divNewSighting.style.display = 'none';
            }
            
            //Add method to delete a selected sighting from the map (Eurico)
            function deleteSighting(){
            }
            
            //used to create an overlay while ajax is running
            function toggleOverlay(){
                var overlay = document.getElementById('overlay');
                var specialBox = document.getElementById('specialBox');
                overlay.style.opacity = .8;
                if(overlay.style.display == "block"){
                    overlay.style.display = "none";
                } else {
                    overlay.style.display = "block";
                }
            }
            
            //If mobile, touch events will be used instead, so, using DOM to 
            //remove the onclick standar event and leave the ontouchstart even
            //in place instead.
            function removeMobileOnclick() {
                if(isMobile()) {
                    document.getElementById('btnNewSighting').onclick = '';
                    document.getElementById('btnSaveSighting').onclick = '';
                }
            }

            //Used to check if the browser version is mobile or desktop
            //so it can be determined if touch specific events can be used
            //or not.
            function isMobile() {
                if (navigator.userAgent.match(/Android/i)
                        || navigator.userAgent.match(/iPhone/i)
                        || navigator.userAgent.match(/iPad/i)
                        || navigator.userAgent.match(/iPod/i)
                        || navigator.userAgent.match(/BlackBerry/i)
                        || navigator.userAgent.match(/Windows Phone/i)
                        || navigator.userAgent.match(/Opera Mini/i)
                        || navigator.userAgent.match(/IEMobile/i)
                        ) {
                    return true;
                }
            }
            
            window.onload = function(){
                removeMobileOnclick();
            }
        </script>
        
        
    </head>
    <body id="body" onload="loadSightingsList();">
        <div>
            <header role="banner" id="page-header"> <!-- ARIA roles -->
                <?php include $_SERVER['DOCUMENT_ROOT'].'/modules/header.php'; ?>
            </header>

            <main role="main" id="page-main"> <!-- there can only be one main element in the page -->
                <article id="page-article">
                    <h1>Eagle Sighting Tracker</h1>
                    
                    <p>Note: This app is not supported on the desktop version of Safari due to incompatibilities with the geolocation APIs, but it supported 
                        on all other browsers, including safari mobile.</p>

                    <div>
                        <h2>List of Sightings</h2>
                        <div id="divSightingsTable" class="sightingsTable">
                            <table id="sightingsTable">
                                <tr>
                                    <td style="width:210px">When Sighted</td>
                                    <td style="width:220px">Wildlife Sighted</td>
                                    <td style="width:230px">Where Sighted</td>
                                </tr>
                            </table>
                        </div>
                        <br>
                        <button type="button" id="btnNewSighting" class="btn btn-6 btn-6b" ontouchstart="setNewSighting();" onclick="setNewSighting();setTransition(false);">Create New Sighting</button>
                    </div>

                    <div id="divNewSighting" style="display: none">
                        <h2>New Sighting</h2>
                        <hr>
                        <p>Note: This application will attempt to determine your location, but this determination will be much more 
                            accurate if your device has a GPS enabled, such a tablet or a smart phone.</p>

                        <p><strong>Location Details</strong></p>
                        <div id="locationDetails" class="coordinatestext"></div>
                        <br>
                        <div id="map"></div>
                        <div id="display" onclick="setTransition(true)">
                            <span id="description"></span>
                            <span id="date"></span>
                            <span id="location"></span>
                        </div>
                        <br>
                        
                        <p><strong>Sighting Details</strong></p>
                        <br>
                        <table width="100%">
                        <tr>
                            <td><label for="wildlifeSighted">Sighted Wildlife:</label></td>
                            <td><input id="wildlifeSighted" type="text" size="35px"></td>
                        </tr>
                         <tr>
                            <td><label id="lblLocation" for="sightingLocation" style="display:none;">Location:</label></td>
                            <td><input id="sightingLocation" type="text" style="display:none;" size="35"></td>
                        </tr>
                        <tr>
                            <td><label for="when">When:</label></td>
                            <td><input id="when" type="datetime-local" size="20"><br></td>
                        </tr>
                        <tr colspan='2'>
                            <td>&nbsp;</td>
                            <td><button id="btnSaveSighting" class="btn btn-6 btn-6b" type="button" ontouchstart="saveSighting();" onclick="saveSighting();">Save Sighting</button></td>
                        </tr>
                    </table>
                    </div>
                </article>
                <aside role="complementary" id="page-aside">
                    <?php include $_SERVER['DOCUMENT_ROOT'].'/modules/quicklinks.php'; ?>
                </aside>
            </main>

            <footer role="contentinfo" id="page-footer">
                <?php include $_SERVER['DOCUMENT_ROOT'].'/modules/footer.php'; ?>
            </footer>
        </div>
        <!-- Start Overlay -->
        <div id="overlay">
            <img src="http://soatheory.com/CIT-261-PersonalPortfolio/images/ajax-loader.gif" alt="" />
        </div>
        <!-- End Overlay -->
    </body>
</html>
