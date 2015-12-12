/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Location Coordinates and google maps Global Variables
/*********************************/
var sLatitude, sLongitude;
var currentMap;
var currentPosition;
var markersArray;
/*********************************/

window.addEventListener('load', function() {
    markersArray = new Array();

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
    window.attachEvent("onload", modal_init);
});

function handleError(error){
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
        zoom: 13,
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
        draggable : false,
        title : "Current Position",
        zIndex : 255,
    });

    //load existing sightings
    markersArray.push(currentPosition);
    loadSightingsToMap();
    resetBounds();
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
      modalWrapper.className = "";
      e.preventDefault ? e.preventDefault() : e.returnValue = false;
    };

    var clickHandler = function(e) {
      if(!e.target) e.target = e.srcElement;
      if(e.target.tagName == "DIV") {
        if(e.target.id != "modal_window") closeModal(e);
      }
    };

    var keyHandler = function(e) {
      if(e.keyCode == 27) closeModal(e);
    };

    if(document.addEventListener) {
      document.getElementById("modal_open").addEventListener("click", openModal, false);
      document.getElementById("modal_close").addEventListener("click", closeModal, false);
      document.addEventListener("click", clickHandler, false);
      document.addEventListener("keydown", keyHandler, false);
    } else {
      document.getElementById("modal_open").attachEvent("onclick", openModal);
      document.getElementById("modal_close").attachEvent("onclick", closeModal);
      document.attachEvent("onclick", clickHandler);
      document.attachEvent("onkeydown", keyHandler);
    }
};


