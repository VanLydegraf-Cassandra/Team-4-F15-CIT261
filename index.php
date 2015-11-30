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