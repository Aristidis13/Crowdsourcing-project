
<!DOCTYPE html> <!-- Document Type Declaration - Defines what HTML specification will be used for intrepretation of this document-->
<!-- API Key = AIzaSyAX0rc5tbX6_3BjR6ZTE9k_jmapCIAPMcQ      -->
<html>
<!-- Subqueries 1,2 a little bit of 3 and 4-->
<!--	Leaflet Heatmap C:\xampp\htdocs\NewWebsite\heatmap.js-master\build\heatmap.js	-->
<!--	Leaflet Location http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css-->
	<head>
		<link href="config\stylesheet.css" rel="stylesheet">
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
		<script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
		<meta charset="utf-8"> <!-- Defining the character set encoding-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">  <!-- Responive design meta tag -->
		<title> Patras Crowdfunding Site </title>
	</head>
	<body>
			<div>
				<nav>
					<li style="display: inline;"> <a href="userMain.php"> Main Page </a> </li>
					<li style="float: right; display: inline;"> <a href="index.php"> Log out </a></li>
				</nav>
			</div>
			<header  class="header"> <!--First styled -->
				<h1 style="font-size: 3vw;"> Patras CrowdSourcing Site Analytics </h1>
			</header>
			<main>
				<h1> Να εμφανίζονται </h1>
			Το ποσοστό εγγραφών ανά είδος δραστηριότητας <br/>
			Την ώρα της ημέρας με τις περισσότερες εγγραφές ανά είδος δραστηριότητας<br/>
			Την ημέρα της εβδομάδας με τις περισσότερες εγγραφές ανά είδος δραστηριότητας <br/>
			Heatmap <br/>
			Χρειάζεται να διαβάσω JavaScript & γραφήματα.
			<form action="action_page.php">
				<div>
					<div class="dateRangeSelect">
						<h2> Analyze Location History Data </h2>
						<h3> Date Range </h3>
						<label for="StartingDate"> From:</label>
						<input type="date" id="StartingDate" name="birthday" value='<?php echo date('Y-m-d');?>'>
						<label for="EndingDate">to:</label>
						<input type="date" id="birthday" name="birthday"  value='<?php echo date('Y-m-d');?>'> <br/><br/>
						<i>*If no date is selected all dates will be selected</i>
						<br/>
						<div id="Hour">
							<h3> Hour Range </h3>
							<label for="startHour">From:</label>
							<input type="time" id="startHour" name="startHour" value="12:00:00">
							<label for="endHour"> to: </label>
							<input type="time" id="endHour"name="endHour" value="12:00:00"><br/> <br/>
							<i>*If no hour is selected all hours will be selected</i>
						</div>
						<br/>
						<div><input type="submit" value="Analyse"> </div>
					</div>
				</div>
				<h1 style="text-align: center;">Map</h1> <div>
				<div id="map_canvas"> </div>
				<script src="https://cdn.jsdelivr.net/npm/heatmapjs@2.0.2/heatmap.js"></script>
				<script>

					var cfg =
    				{
						"radius": 2,
						"maxOpacity": .8,
						// scales the radius based on map zoom
						"scaleRadius": true,
  						// if set to false the heatmap uses the global maximum for colorization
  						// if activated: uses the data maximum within the current map boundaries
						// (there will always be a red spot with useLocalExtremas true)
						"useLocalExtrema": true,
						// which field name in your data represents the latitude - default "lat"
						latField: 'lat',
						// which field name in your data represents the longitude - default "lng"
						lngField: 'lng',
						// which field name in your data represents the data value - default "value"
						valueField: 'count'
					};


   					var map = L.map("map_canvas",{center:[38.2304620 , 21.7531500],zoom:12, maxzoom:18});
    				L.tileLayer('https://api.maptiler.com/maps/basic/{z}/{x}/{y}.png?key=unHremW0CXKWA2oweyRz',{
    				attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a>',}).addTo(map);
    /*		var PatrasMarker =L.marker([38.2304620, 21.7531500]).addTo(map);*/

					heatmapLayer.setData(data); // Data is the representable data that I will have to represent
				</script>
			</div>
		</form>
	</main>
	<script src="https://raw.githubusercontent.com/pa7/heatmap.js/develop/plugins/leaflet-heatmap/leaflet-heatmap.js"></script>
</body>
<!--<?php include('footer.php');?>-->
</html>