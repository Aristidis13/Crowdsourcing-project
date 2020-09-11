<!DOCTYPE html> <!-- Document Type Declaration - Defines what HTML specification will be used for intrepretation of this document-->

<html>
<!-- Subqueries 1,3-->
<?php	include('adminHeader.php'); ?>
		<main>
			<h3> Hello <?php echo $username; ?> </h3>
			<h3> Θα εμφανίζονται</h3>
<?php
	// Admin DB queries start from here
		
		// 1a query
        $sql = "SELECT type,count(*) as 'Frequency' FROM userlocations GROUP BY type;";
        $result = mysqli_query($conn, $sql) OR DIE("Data has failed to load.<br/>"); // Get results from database
        $record = mysqli_fetch_all($result,MYSQLI_ASSOC);
       	print_r($record);
       	
       	if(!empty($record))
       	{
			//create the chart pie for every activity for the query


    	    // 1b query
    	    $sql = "SELECT COUNT(DISTINCT `uploaderId`) AS `FrequencyOfDiffUsers`,
    	           	CASE when COUNT(`recordId`) between 0 and 8000 then ' less than 8000'
    	           	when COUNT(`recordId`) between 8001 and 16000 then ' 8001-16000'
    	           	when COUNT(`recordId`) between 16001 and 24000 then '16001-24000'
    	           	when COUNT(`recordId`) between 24001 and 32000 then ' 24001-32000'
    	           	when COUNT(`recordId`) between 32001 and 40000 then ' 32001-40000'
    	           	when COUNT(`recordId`) between 40001 and 48000 then '40001-48000'
    	 			when COUNT(`recordId`) between 48001 and 56000 then '48001-56000'
    	 			when COUNT(`recordId`) between 56001 and 64000 then '56001-64000'
    		 		when COUNT(`recordId`)> 64000 then 'more than 64000' end as `range`
    		   		FROM `userlocations` GROUP BY `uploaderId`;";
    		    $result = mysqli_query($conn, $sql); // Get results from database
				$record = mysqli_fetch_all($result,MYSQLI_ASSOC);
	
			//create the histogram for every activity for the query


			// 1c query
        		$sql = "SELECT MONTHNAME(`dates`) AS `Months`, COUNT(`uploaderId`) AS `FrequnecyOfRecords` FROM `userlocations` GROUP BY `Months` ORDER BY 	MONTH(`dates`) ASC;";
        		$result = mysqli_query($conn, $sql); // Get results from database
				$record = mysqli_fetch_all($result,MYSQLI_ASSOC);
	
			//create the histogram for every activity for the query

				//1d query
				$sql = "SELECT CASE WHEN WEEKDAY(`dates`)=0 THEN 'Sunday'
						WHEN WEEKDAY(`dates`)=1 THEN 'Monday' WHEN WEEKDAY(`dates`)=2 THEN 'Tuesday'
						WHEN WEEKDAY(`dates`)=3 THEN 'Wednesday' WHEN WEEKDAY(`dates`)=4 THEN 'Thursday'
						WHEN WEEKDAY(`dates`)=5 THEN 'Friday' WHEN WEEKDAY(`dates`)=6 THEN 'Saturday' END AS `weekday`,
						count(`recordId`) AS `recordsPerWeekday`
						FROM `userlocations` GROUP BY `weekday` ORDER BY WEEKDAY(`dates`);";
				$result = mysqli_query($conn, $sql); // Get results from database
				$record = mysqli_fetch_all($result,MYSQLI_ASSOC);

				//1e query
				$sql = "SELECT CONCAT(HOUR(`dates`), ':00-', HOUR(`dates`)+1, ':00') AS `Hours`,
						COUNT(`recordId`) AS `usage` FROM `userlocations` GROUP BY HOUR(`dates`);";
				$result = mysqli_query($conn, $sql); // Get results from database
				$record = mysqli_fetch_all($result,MYSQLI_ASSOC);

				//1f query
				$sql = "SELECT YEAR(`dates`) AS `Years`, COUNT(`uploaderId`) AS `FrequnecyOfRecords` FROM `userlocations` GROUP BY `Years` ORDER BY YEAR(`dates`) ASC;";


       	}
       	else echo "No record has being added to the database";


?>			
			β) Η κατανομή του πλήθους εγγραφών του χρήστη. </br>
			γ) Την κατανομή του πλήθους εγγραφών ανά μέρα της εβδομάδας </br>
			δ) Την κατανομή του πλήθους εγγραφών ανά ώρα </br>
			ε) Την κατανομή του πλήθους εγγραφών ανά έτος. </br>



			<br/> <hr/> <br/>
			<h2> Select Elements</h2>
			<form action="/action_page.php">
				<div id="Date">
					<h3> Date Range </h3>
					<label for="StartingDate"> Start Date:</label>
					<input type="date" id="StartingDate" name="birthday">
					<label for="EndingDate">End Date:</label>
					<input type="date" id="birthday" name="birthday"> <br/><br/>
					<i>*If no date is selected all dates will be selected</i>
				</div>
				<div id="Hour">
					<h3> Hour Range </h3>
					<label for="startHour">From:</label>
					<input type="time" id="startHour" name="startHour" value="12:00:00">
					<label for="endHour"> to: </label>
					<input type="time" id="endHour"name="endHour" value="12:00:00"><br/> <br/>
					<i>*If no hour is selected all hours will be selected</i>
				</div>
				<div id="Activity">
					<h3> Activity </h3>
					<input type="checkbox" id="activity1" name="activity1" value="WALKING">
					<label for="activity1"> Walking </label><br/>
					<input type="checkbox" id="activity2" name="activity2" value="CAR">
					<label for="activity2"> Car </label><br/>
					<input type="checkbox" id="activity3" name="activity3" value="STILL">
					<label for="activity3"> Still(Ακίνητος) </label><br/>
					<input type="checkbox" id="activity4" name="activity4" value="Train">
					<label for="activity4"> Train </label><br/>
					<input type="checkbox" id="activity5" name="activity5" value="Fly">
					<label for="activity5"> Flying </label><br/>
				</div>
				<br/>
				<input type="submit" name="showElementsInMap" value="Search">
			</form>
			<br/><hr/><br/>
				<h3> Delete All Collected Data</h3>
				<button onclick="dataDeletionConfirmation()"> Delete All Data </button>
				<p id="confirmationMessage"></p>
			</form>
			<br/>
		</main>
		<?php include('adminFooter.php');?>
</html>

