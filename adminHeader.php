<?php
	session_start();
	$id = $_SESSION['id'] ?? 'No Id';
	$username = $_SESSION['username'] ?? 'Guest';
	include('config\dbconnect.php');
?>

	<head>
		<script defer src="script.js"></script>
		<link href="config\stylesheet.css" rel="stylesheet">
		<meta charset="utf-8"> <!-- Defining the character set encoding-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">  <!-- Responive design meta tag -->
		<title> Patras Crowdfunding Site </title>
	</head>
	<body style="background-color: #fefeee !important; margin:0;">
		<nav style="text-align: right;">
			<form action="logout.php" style="text-align: right;">
				<input type="submit" name="logout" value="Log Out">
			</form>
		</nav>
		<header  id="adminHeader"> 
			<h1 style="font-size: 2vw; color:white;"><a href="adminMain.php"> Patras CrowdSourcing <br/>Admin Page </a></h1>
		</header>