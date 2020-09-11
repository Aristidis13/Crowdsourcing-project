<!DOCTYPE html> <!-- Document Type Declaration - Defines what HTML specification will be used for intrepretation of this document-->

<html>
<!-- Subqueries 1,2 a little bit of 3 and 4-->
		<div>
			<span>
				<form action="logout.php" style="text-align: right;">
					<input type="submit" name="logout" value="Log Out">
				</form>
			</span>
			<script
			  src="https://code.jquery.com/jquery-3.5.1.min.js"
			  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
			  crossorigin="anonymous">
			</script>
			
			<?php	include('header.php');?>
			<nav>
				<a href="uploadData.php">Upload your Data</a> |
				<a href="userMain.php"> main page</a> |
				<a href="userAnalysis.php"> Analyse Data </a>
			</nav>
			<main>
				<h3> Hello <?php echo $username; ?> </h3>

				Να κάνω τα γραφήματα μόλις μάθω chart.js
				<br/>
			</main>
		</div>
<?php include('footer.php');?>
</html>