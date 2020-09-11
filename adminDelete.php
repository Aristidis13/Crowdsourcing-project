<?php
	include_once('config\dbconnect.php');
	$sql2 = "DELETE FROM `bodyactivitypermonth`;";
	$query2 = mysqli_query($conn, $sql2) OR die($sql2);
	$sql3 = "DELETE FROM `ecoscorepermonth`;";
	$query3 = mysqli_query($conn, $sql3) OR die($sql3);
	$sql = "DELETE FROM `userlocations`;";
	$query = mysqli_query($conn, $sql) OR die($sql);
	$sql1 = "DELETE FROM `userresults`;";
	$query1 = mysqli_query($conn, $sql1) OR die($sql1);
	$sql4 = "DELETE FROM `validactivitypermonth`;";
	$query4 = mysqli_query($conn, $sql4) OR die($sql4);

	if($query2 && $query3 && $query && $query1)
	{
		header('Location: adminMain.php');
	}
?>