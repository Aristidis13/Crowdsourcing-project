<?php
	session_start();
	$id = $_SESSION['id'] ?? 'No Id';
	$username = $_SESSION['username'] ?? 'Guest';
	include('config\dbconnect.php');

	function calculateDistanceFromPatras($lat1, $long1)
	{
		$theta = $long1 - 21.7531500;
		$miles = (sin(deg2rad($lat1))) * sin(deg2rad(38.2304620)) + (cos(deg2rad($lat1)) * cos(deg2rad(38.2304620)) * cos(deg2rad($theta)));
		$miles = acos($miles);
		$miles = rad2deg($miles);
		$result = $miles * 60 * 1.1515*1.609344;
		return $result;
	}
?>

<head>
	<link href="stylesheet.css" rel="stylesheet">
	<meta charset="utf-8"> <!-- Defining the character set encoding-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">  <!-- Responive design meta tag -->
	<title> Patras Crowdfunding Site</title>
</script>
</head>
<body style="background-color: #fefeee !important; margin:0;" >
	<header  id="header"> <!--First styled -->
		<h1 style="font-size: 3vw; color:white;"><a href="userMain.php"> Patras CrowdSourcing Site </a></h1>
	</header>
