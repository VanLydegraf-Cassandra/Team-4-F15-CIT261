/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global google */

//Location Coordinates and google maps Global Variables
/*********************************/
var sLatitude, sLongitude;
var currentMap;
var currentPosition;
var markersArray;
/*********************************/

window.addEventListener('load', function() {
    markersArray = new Array();
    modal_init();

    // getting current location by geocoder
    //var getGeoLocation = new google.maps.Geocoder();
    try{
        if(navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
              sLatitude = position.coords.latitude;
              sLongitude = position.coords.longitude;
              currentPosition = new google.maps.LatLng(position.coords.latitude, position.coords.longitude); 
              
              //ajax call to get current location name
              //AJAX call to geolocation service to get location name from coordinates      
                    var url = "http://api.wunderground.com/api/22b4347c464f868e/geolookup/q/" + sLatitude + "," + sLongitude + ".json";
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
                            }
                            catch(e){
                                //invalid JSON
                            }  
                        }
                    }

                    xhttp.open("GET", url, true);
                    xhttp.send();
              //end ajax call
              
              initialize(currentPosition);
              google.maps.event.trigger(currentMap, 'resize');
              currentMap.setCenter(currentPosition);
              //load existing sightings
              markersArray.push(currentPosition);
              loadSightingsToMap();
              resetBounds();
          }, function() {
              handleError(100);
          });
      }
      else {
          handleError(200);
      }
    }
    catch(e){
        console.log(e.message);
    }
});

function handleError(error){
    document.getElementById("saveMessage").textContent = "The Map could not be initialized due to error code: " + error;
}

function resetBounds(){
     //zoom to bounds
    var bounds = new google.maps.LatLngBounds();

    for(i=0;i<markersArray.length;i++) {
        bounds.extend(markersArray[i].getPosition());
    }

    currentMap.fitBounds(bounds);
}

//function to initialize the map
function initialize(myLocation) {
    var mapOptions = {
        zoom: 17,
        center : myLocation,
        zoomControl: false,
        scaleControl: true,
        mapTypeId : google.maps.MapTypeId.TERRAIN
    };
    currentMap = new google.maps.Map(document.getElementById("map"), mapOptions);

    // current position marker
    var mapMarker = new google.maps.Marker({
        position : myLocation,
        icon : "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
        animation: google.maps.Animation.DROP,
        map : currentMap,
        draggable : true,
        title : "Current Position",
        zIndex : 255,
    });
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
    var content;
    var infowindow = new google.maps.InfoWindow();

    for(var i in localStorage){
        var str = i.split("|");

        if(str[0] == "Sighting"){
            json = JSON.parse(localStorage.getItem(i));
            oLatLong = new google.maps.LatLng(json.Latitude, json.Longitude);

            marker = new google.maps.Marker({
                position: oLatLong,
                icon : "/images/animals.png",
                animation: google.maps.Animation.DROP,
                map : currentMap,
                zIndex : -99,
                draggable : false,
                sighting : "Sighted: " + json.WildlifeSighted,
                timeStamp : json.Date,
                title : "Sighted: " + json.WildlifeSighted + " on " + json.Date,
                observationNotes : json.Notes,
                location: json.Location,
                id : i,
            })
            
            content = "<strong>" + json.WildlifeSighted + ", on " + json.Date + "</strong><br><br>" 
                           + "Location: " + json.Location + " (lat: " + json.Latitude + ", long: " + json.Longitude + "<br><br>"
                           + "Observation Notes: " + json.Notes;
  
            google.maps.event.addListener(marker, 'click', (function(marker,content){
                    return function() {
                        infowindow.setContent(content);
                        infowindow.open(currentMap, marker);
                    };
                })(marker,content));
                
            markersArray.push(marker);
        }
    }
}

//because the markers can't stay on top of each other otherwise they will not be seens
//we need to apply a small offset to the location to allow them to be seen
//min and max limits for multiplier, for random numbers
//keep the range pretty small, so markers are kept close by
var min = .999999;
var max = 1.000001;

//Saves a new sighting on the browser's local storage        
function saveSighting(){
    var sWildlifeSighted = wildlifeSighted.value;
    var sLocation = document.getElementById("sightingLocation").value;
    var dDate = document.getElementById("when").value;
    var sNotes = document.getElementById("observationNotes").value;

    var sJson = JSON.stringify({ 
            WildlifeSighted : sWildlifeSighted, 
            Location : sLocation, 
            Date : dDate, 
            Notes : sNotes,
            Latitude: sLatitude * (Math.random() * (max - min) + min),
            Longitude: sLongitude * (Math.random() * (max - min) + min)
    });

    //save to local storage
    var key = 'Sighting' + '|' + sWildlifeSighted + '|' + dDate;
    localStorage.setItem(key, sJson);
    document.getElementById("saveMessage").textContent = "Your sighting was successfully saved.";

    //Add sighting to map
    // current position
    var mapMarker = new google.maps.Marker({
        position : currentPosition,
        icon : "/images/animals.png",
        map : currentMap,
        zIndex : 255
    });
}

//Infobox
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
    if(isHide){
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
    else{
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

//handling of the modal form sighting recording
//Adapter from original code from Chirp Internet: www.chirp.com.au
var modal_init = function() {

    var modalWrapper = document.getElementById("modal_wrapper");
    var modalWindow  = document.getElementById("modal_window");

    var openModal = function(e)
    {
        //set defaults:
        document.getElementById('when').value = getLocalTimestamp();
        
        
      modalWrapper.className = "overlay";
      var overflow = modalWindow.offsetHeight - document.documentElement.clientHeight;
      if(overflow > 0) {
        modalWindow.style.maxHeight = (parseInt(window.getComputedStyle(modalWindow).height) - overflow) + "px";
      }
      modalWindow.style.marginTop = (-modalWindow.offsetHeight)/2 + "px";
      modalWindow.style.marginLeft = (-modalWindow.offsetWidth)/2 + "px";
      e.preventDefault ? e.preventDefault() : e.returnValue = false;
    };

    var closeModal = function(e)
    {
      saveSighting();
      modalWrapper.className = "";
      e.preventDefault ? e.preventDefault() : e.returnValue = false;
    };

    var keyHandler = function(e) {
      if(e.keyCode == 27) closeModal(e);
    };

    if(document.addEventListener) {
      document.getElementById("modal_open").addEventListener("click", openModal, false);
      document.getElementById("btnSaveSighting").addEventListener("click", closeModal, false);
      document.getElementById("btnSaveSighting").addEventListener("ontouchstart", closeModal, false);
      document.addEventListener("keydown", keyHandler, false);
    } else {
      document.getElementById("modal_open").attachEvent("onclick", openModal);
      document.getElementById("btnSaveSighting").attachEvent("onclick", closeModal);
      document.attachEvent("onkeydown", keyHandler);
    }
};