<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Eagle Sighting Tracker | CIT-261 Team 4</title>
        <meta name="author" content="Eurico Costa">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=1"/>
        <meta name="description" content="This website provides a high level overview of my understanding of the topics learned in CIT-261 Fall Semester 2015.">
        
        <link rel="stylesheet" type="text/css" media="screen" href="css/master.css" />
    </head>
    <body>
        <div id="header">
            <header role="banner" id="page-header"> <!-- ARIA roles -->
                    <?php include $_SERVER['DOCUMENT_ROOT'].'/modules/header.php'; ?>
					<div id="saveMessage">&nbsp;</div>

            </header>
        </div>
		
        <!-- #modal_wrapper -->

        <div id="modal_wrapper">
            <div id="modal_window">
                <div style="text-align: right;"><a id="modal_close" href="#">close <b>X</b></a></div>

                <p>Complete the form below to record a new sighting:</p>
                <hr>
                <p>
                    <label>Animal<br>
                        <input type="text" autofocus required size="48" id="wildlifeSighted" name="wildlifeSighted">
                    </label>
                </p>
                <p>
                    <label>Location<br>
                        <input type="text" required title="Your location may be computed automatically for you." size="48" id="sightingLocation" name="sightingLocation">
                    </label>
                </p>
                <p>
                    <label>When<br>
                        <input type="datetime-local" size="48" name="when" id="when">
                    </label>
                </p>
                <p>
                    <label>Observation Notes<strong>*</strong><br>
                        <textarea required id="observationNotes" name="observationNotes"  cols="48" rows="8"></textarea>
                    </label>
                </p>
                <p>
                    <button id="btnSaveSighting" class="btn btn-6 btn-6b" type="button" ontouchstart="" onclick="">Save Sighting</button>
                </p>
            </div> <!-- #modal_window -->
        </div> 
        <!-- #modal_wrapper -->
        <div id="map"></div>
        <div id="display" onclick="setTransition(true)">
            <span id="description"></span>
            <span id="date"></span>
            <span id="location"></span>
        </div>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBjmSgcVOnH9Vk5O2ovkZRJayNGX2uFD70"></script>
        <script src="scripts/baldEagleTracker.js" type="text/javascript"></script>
    </body>
</html>