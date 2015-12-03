<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>AJAX Example: Wildlife Tracker | CIT-261 Understanding Portfolio - Eurico Costa</title>
        <meta name="author" content="Eurico Costa">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="This website provides a high level overview of my understanding of the topics learned in CIT-261 Fall Semester 2015.">
        
        <link rel="stylesheet" type="text/css" media="screen" href="css/soatheory.css" />
        
        <script>
            //Location Coordinates and google maps Global Variables
            /*********************************/
            var sLatitude, sLongitude;
            var currentMap;
            var currentPosition;
            var markersArray;
            /*********************************/
            
            
            function initMap(lat,long) {
                var zoom = 13;
                var divMap = document.getElementById("map");
                var myUrl = 'https://www.google.com/maps/embed/v1/place?key=AIzaSyBjmSgcVOnH9Vk5O2ovkZRJayNGX2uFD70&q=' + lat + ',' + long + '&zoom=' + zoom + '&maptype=roadmap';
                var myiFrame = '<iframe id="map_frame" '
                                  + 'width="100%" height="400px" frameborder="0" border="1" scrolling="no" marginheight="0" marginwidth="0" '
                                  + 'src="' + myUrl + '"></iframe>';

                divMap.innerHTML = myiFrame;
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
            
            function GetLocation(location) {
                /*Do your ajax calls, sorting or laoding, etc.*/
                toggleOverlay();
                
                document.getElementById("locationDetails").innerHTML = 
                        "Latitude: " + location.coords.latitude + 
                        "<br>Logitude: " + location.coords.longitude +
                        "<br>Accuracy: " + location.coords.accuracy + " (if >= 5000, the location and map will not be shown).<br>";
                
                sLatitude = location.coords.latitude;
                sLongitude = location.coords.longitude;
                
                
                if(location.coords.accuracy >= 500000){
                    document.getElementById("locationDetails").style.color = "red";
                    document.getElementById("locationDetails").innerHTML += "<br>Current location accuracy is not sufficient to determine your location. Please enter your location manually in the location field below."
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
                                var country = json.location.country
                                var location = city + ", " + state + ", " + country;
                                document.getElementById("sightingLocation").value = location;
                                document.getElementById("locationDetails").innerHTML += "<br>Location: " + location;
                                enableManualLocation();
                            }
                            catch(e){
                                //invalid JSON
                            }  
                        }
                    }

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
            
            function setNewSighting(){
                //show sighting form
                var sd = document.getElementById("divNewSighting");
                sd.style.display = "block";
                //get coordinates
                getLocation();
 
                //set sighting default timestamp
                document.getElementById('when').value = getLocalTimestamp();
            }
            
            function clearAllRows(){
                var table = document.getElementById('sightingsTable');
                var tableRows = table.getElementsByTagName('tr');
                var rowCount = tableRows.length;

                for (var x=rowCount-1; x>0; x--) {
                   table.removeChild(tableRows[x]);
                }
            }
            
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
            
            function removeMobileOnclick() {
                if(isMobile()) {
                    document.getElementById('btnNewSighting').onclick = '';
                    document.getElementById('btnSaveSighting').onclick = '';
                }
            }

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
            //window.addEventListener('load', removeMobileOnclick);
            window.onload = function(){
                removeMobileOnclick();
            }
        </script>
        
        
    </head>
    <body id="body" onload="loadSightingsList();">
        <div>
            <header role="banner" id="page-header"> <!-- ARIA roles -->
                <?php include $_SERVER['DOCUMENT_ROOT'].'/CIT-261-PersonalPortfolio/modules/header.php'; ?>
            </header>


            <ol class="breadcrumb">
                <li><a href="/CIT-261-PersonalPortfolio/">Home</a></li>
                <li><a href="/CIT-261-PersonalPortfolio/ajaxinteractions.php">AJAX Interactions</a></li>
                <li class="current">AJAX Example: Wildlife Tracker</li>
            </ol>
            <hr>

            <main role="main" id="page-main"> <!-- there can only be one main element in the page -->
                <article id="page-article">
                    <h1>Wildlife Sightings Tracking Application (Example)</h1>
                    
                    <p>Note: This example is not supported on the desktop version of Safari due to incompatibilities with the geolocation APIs, but it supported 
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
                        <button type="button" id="btnNewSighting" class="btn btn-6 btn-6b" ontouchstart="setNewSighting();" onclick="setNewSighting();">Create New Sighting</button>
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
                    <?php include $_SERVER['DOCUMENT_ROOT'].'/CIT-261-PersonalPortfolio/modules/quicklinks.php'; ?>
                </aside>
            </main>

            <footer role="contentinfo" id="page-footer">
                <?php include $_SERVER['DOCUMENT_ROOT'].'/CIT-261-PersonalPortfolio/modules/footer.php'; ?>
            </footer>
        </div>
        <!-- Start Overlay -->
        <div id="overlay">
            <img src="http://soatheory.com/CIT-261-PersonalPortfolio/images/ajax-loader.gif" alt="" />
        </div>
        <!-- End Overlay -->
    </body>
</html>