<!DOCTYPE html> <!-- Document Type Declaration - Defines what HTML specification will be used for intrepretation of this document-->

<html>
<!-- Subquery 4-->
	<div>
		<span>
			<form action="logout.php" style="text-align: right;"> <input type="submit" name="logout" value="Log Out"> </form>
		</span>
		<?php include('header.php'); ?>
	</div>
	<main>
		<h3> Hello <?php echo $username; ?> </h3>
		<div> <h3 style="text-align: center"> Upload your data as a JSON file </h3> </div>
		<div align="center" style="padding: 10px;" >
			<form action="uploadData.php" method="POST" enctype="multipart/form-data">
				<input type="file" id="myFile" name="file"> <br/><br/>
				<input type="submit" name="upload" value="Upload">
				<!--PHP script that updates the database and the user and admin's tables-->
				<?php
					$errors=['data' => ''];
					$uploadPressed = !empty($_POST['upload']);
					$timestampAsdate =0;
					$timestamp=0;

					if ($uploadPressed)
					{
						$file = $_FILES['file']; // I get the file
						$filename = htmlspecialchars($file['name']);
						$ext = pathinfo($filename, PATHINFO_EXTENSION);
						if(file_exists($file['tmp_name']) && ($ext==='JSON' ||$ext==='json')) // If file exists
						{
							date_default_timezone_set('Europe/Athens');
							$uploadDir = getcwd().'/uploads/'.$file['name'] ; // This is the directory that uploads will be stored 
							move_uploaded_file($file['tmp_name'], $uploadDir );
							$json = file_get_contents('uploads/'.$filename);
							$data = json_decode($json, true);
							$remRescounter=0;  // Counter that keeps track of removed results because they are out of Patras
							if(isset($data))
							{
								foreach ($data as $location) // 1 epanalhpsh = h json_decode epistrefei 1 pinaka pou periexei pinakes
								{
									foreach ($location as $field)
									{
										$latitudeE7 = $field['latitudeE7'] * 0.0000001;
										$longitudeE7 = $field['longitudeE7'] * 0.0000001;
										$distFromPatras = calculateDistanceFromPatras($latitudeE7,$longitudeE7);
										if($distFromPatras <= 10.0000000)
										{
		
											$accuracy = $field['accuracy'];
											if(isset($field['velocity'])) $velocity = $field['velocity'];
											else $velocity=0;
											if(isset($field['heading'])) $heading = $field['heading'];
											else $heading = 0;
											if(isset($field['altitude'])) $altitude = $field['altitude'];
											else $altitude = 0;
											if(isset($field['verticalAccuracy'])) $verticalAccuracy = $field['verticalAccuracy'];
											else $verticalAccuracy = 0;
											if(isset($field['activity'])) // If activity field exists
											{
												foreach ($field['activity'] as $activityField) 
												{
													$timestamp = (int) $activityField['timestampMs'];
													$timestamp = $timestamp/1000; // According to Google Maps date is timestamp is in milliseconds not in seconds
													$timestampAsdate = date('Y-m-d H:i:s',$timestamp); //timestamp as date
													if(isset($activityField['activity']))
													{
														$biggestConfidence = -1;
														// Locate the activity with the biggest confidence
														foreach ($activityField['activity'] as $nestedActivityField)
															if($biggestConfidence<= $nestedActivityField['confidence'])
																$biggestConfidence = $nestedActivityField['confidence'];

														foreach ($activityField['activity'] as $nestedActivityField)
														{
															if ($nestedActivityField['confidence'] === $biggestConfidence)
															{
																$type = $nestedActivityField['type'];
																$confidence = $nestedActivityField['confidence'];
																$sql = "INSERT INTO userlocations(latitudeE7,longitudeE7,accuracy,velocity,heading,altitude,verticalAccuracy,type,confidence,uploaderId,dates)
																VALUES ('$latitudeE7','$longitudeE7','$accuracy','$velocity','$heading','$altitude','$verticalAccuracy','$type','$confidence','$id','$timestampAsdate');";
																if(!mysqli_query($conn, $sql)) '<br/>Error: '. mysqli_error($conn);
															}
														}
													}
												}
											}
											else
											{
												$timestamp = (int) $field['timestampMs'];
												$timestamp = $timestamp/1000; // According to Google Maps date is timestamp is in milliseconds not in seconds
												$timestampAsdate = date('Y-m-d H:i:s',$timestamp); //timestamp as date

												$type = "UNKNOWN";
												$confidence = 0;
												$sql = "INSERT INTO userlocations(latitudeE7,longitudeE7,accuracy,velocity,heading,altitude,verticalAccuracy,type,confidence,uploaderId,dates)
												VALUES ('$latitudeE7','$longitudeE7','$accuracy','$velocity','$heading','$altitude','$verticalAccuracy','$type','$confidence','$id','$timestampAsdate');";
												if(!mysqli_query($conn, $sql)) '<br/>Error: '. mysqli_error($conn);
											}
											
										}
										else ++$remRescounter;
									}
								}

							}
							echo "<br/>Upload was successful with $remRescounter \"out of Patras\" results removed<br>";

						//If you have records for the last year create two tables that for each month contain the body activity and the activity of the user.
							for($i=0;$i<12;$i++)
							{
								$bodyactivitypermonth[$i] = 0;
								$validactivitypermonth[$i] = 0;
								$ecoscorepermonth[$i] = 0;
							}
						// Start calculating ecoscore for the user per month
						// First get  activity types for this year grouped by month and activity type
							$sql = "SELECT type,count(`type`) AS 'Number of Times', MONTH(`dates`) AS 'Month' FROM `userlocations` WHERE `dates`>= (CURRENT_DATE- INTERVAL 6 YEAR) AND `uploaderId`='$id' GROUP BY MONTH(`dates`),`type`;";
							$result = mysqli_query($conn, $sql) OR DIE('Connection failed.\nUnable to fetch last years data'); // Get results from database
							$returnedData = mysqli_fetch_all($result,MYSQLI_ASSOC);

							if(!empty($returnedData))
							{
								foreach ($returnedData as $numberOfTimesForActivityForAMonth)
								{
								// This conditional statement must be updated according to the site
								//https://developers.google.com/android/reference/com/google/android/gms/location/DetectedActivity#constant-summary
									$month = $numberOfTimesForActivityForAMonth['Month'];
							        if (($numberOfTimesForActivityForAMonth['type'] === 'ON_BICYCLE') || ($numberOfTimesForActivityForAMonth['type'] === 'ON_FOOT') || ($numberOfTimesForActivityForAMonth['type'] ===  'WALKING') || ($numberOfTimesForActivityForAMonth['type'] ===  'RUNNING'))
							        	$bodyactivitypermonth[$month-1] += $numberOfTimesForActivityForAMonth['Number of Times'];  
							// Month can have values 1-12 but the array 0-11 so arrayIndex = month-1
							// To regain Month value: Month = arrayIndex +1
						        //The condition is activities that do not indicate body activity so they are left out of the calculations for ecoscore
        							if(!($numberOfTimesForActivityForAMonth['type'] === 'TILTING') && !($numberOfTimesForActivityForAMonth['type'] === 'STILL') && !($numberOfTimesForActivityForAMonth['type'] === 'UNKNOWN'))
               							$validactivitypermonth[$month-1] += $numberOfTimesForActivityForAMonth['Number of Times'];
								}
								for ($i=0; $i<12; $i++)
									if($validactivitypermonth[$i]>0) $ecoscorepermonth[$i] = $bodyactivitypermonth[$i] / $validactivitypermonth[$i];

					//Insert Values to bodyactivitypermonth
								$sql = "SELECT * FROM `bodyactivitypermonth` WHERE `userId` = '$id';";
								$result = mysqli_query($conn, $sql) OR DIE('Connection failed.\nUnable to connect to bodyactivitypermonth.'); // Get results from database
								$returnedData = mysqli_fetch_all($result,MYSQLI_ASSOC);
								
								if(!empty($returnedData))
								{
									$sql = "UPDATE `bodyactivitypermonth` 
									SET `previousMonth0`='$bodyactivitypermonth[0]', `previousMonth1`='$bodyactivitypermonth[1]',`previousMonth2`='$bodyactivitypermonth[2]',`previousMonth3`='$bodyactivitypermonth[3]',
									`previousMonth4`='$bodyactivitypermonth[4]',`previousMonth5`='$bodyactivitypermonth[5]',`previousMonth6`='$bodyactivitypermonth[6]',`previousMonth7`='$bodyactivitypermonth[7]',`previousMonth8`='$bodyactivitypermonth[8]',
									`previousMonth9`='$bodyactivitypermonth[9]',`previousMonth10`='$bodyactivitypermonth[10]',`previousMonth11`='$bodyactivitypermonth[11]' WHERE `userId`='$id';";
									if(!mysqli_query($conn, $sql)) '<br/>Error: '. mysqli_error($conn);
								}
								elseif(empty($returnedData))
								{
									$sql = "INSERT INTO `bodyactivitypermonth`(`previousMonth0`,`previousMonth1`,`previousMonth2`,`previousMonth3`,`previousMonth4`,`previousMonth5`,`previousMonth6`,`previousMonth7`,`previousMonth8`,`previousMonth9`,`previousMonth10`,`previousMonth11`,`userId`)
											VALUES ('$bodyactivitypermonth[0]','$bodyactivitypermonth[1]','$bodyactivitypermonth[2]','$bodyactivitypermonth[3]','$bodyactivitypermonth[4]','$bodyactivitypermonth[5]','$bodyactivitypermonth[6]','$bodyactivitypermonth[7]','$bodyactivitypermonth[8]','$bodyactivitypermonth[9]','$bodyactivitypermonth[10]','$bodyactivitypermonth[11]','$id');";
									if(!mysqli_query($conn, $sql)) '<br/>Error: '. mysqli_error($conn);
								}

							//Insert Values to validactivitypermonth
								$sql = "SELECT * FROM `validactivitypermonth` WHERE `userId` = '$id';";
								$result = mysqli_query($conn, $sql) OR DIE('Connection failed.\nUnable to connect to validactivitypermonth.'); // Get results from database
								$returnedData = mysqli_fetch_all($result,MYSQLI_ASSOC);

								if(!empty($returnedData))
								{
									// Update records
									$sql = "UPDATE `validactivitypermonth`
									SET `previousMonth0`='$validactivitypermonth[0]',`previousMonth1`='$validactivitypermonth[1]',`previousMonth2`='$validactivitypermonth[2]',
									`previousMonth3`='$validactivitypermonth[3]',`previousMonth4`='$validactivitypermonth[4]',`previousMonth5`='$validactivitypermonth[5]',
									`previousMonth6`='$validactivitypermonth[6]',`previousMonth7`='$validactivitypermonth[7]',`previousMonth8`='$validactivitypermonth[8]',
									`previousMonth9`='$validactivitypermonth[9]',`previousMonth10`='$validactivitypermonth[10]',`previousMonth11`='$validactivitypermonth[11]' WHERE `userId`='$id'"; 
									if(!mysqli_query($conn, $sql)) '<br/>Error: '. mysqli_error($conn); //And if everything goes smoothly insert the new values to each month
								}
								elseif(empty($returnedData))
								{
									$sql = "INSERT INTO `validactivitypermonth`(`previousMonth0`,`previousMonth1`,`previousMonth2`,`previousMonth3`,`previousMonth4`,`previousMonth5`,`previousMonth6`,`previousMonth7`,`previousMonth8`,`previousMonth9`,`previousMonth10`,`previousMonth11`,`userId`)
											VALUES ('$validactivitypermonth[0]','$validactivitypermonth[1]','$validactivitypermonth[2]','$validactivitypermonth[3]','$validactivitypermonth[4]','$validactivitypermonth[5]','$validactivitypermonth[6]','$validactivitypermonth[7]','$validactivitypermonth[8]','$validactivitypermonth[9]','$validactivitypermonth[10]','validactivitypermonthh[11]','$id');";
										if(!mysqli_query($conn, $sql)) '<br/>Error: '. mysqli_error($conn);
								}

							//Insert Values to ecoscorepermonth
								$sql = "SELECT * FROM `ecoscorepermonth` WHERE `userId` = '$id';";
								$result = mysqli_query($conn, $sql) OR DIE('Connection failed.\nUnable to connect to ecoscorepermonth.'); // Get results from database
								$returnedData = mysqli_fetch_all($result,MYSQLI_ASSOC);
								
								if(!empty($returnedData))
								{
									// Update previous records
									$sql = "UPDATE `ecoscorepermonth`
									SET `previousMonth0`='$ecoscorepermonth[0]',`previousMonth1`='$ecoscorepermonth[1]',`previousMonth2`='$ecoscorepermonth[2]',
									`previousMonth3`='$ecoscorepermonth[3]',`previousMonth4`='$ecoscorepermonth[4]',`previousMonth5`='$ecoscorepermonth[5]',
									`previousMonth6`='$ecoscorepermonth[6]',`previousMonth7`='$ecoscorepermonth[7]',`previousMonth8`='$ecoscorepermonth[8]',
									`previousMonth9`='$ecoscorepermonth[9]',`previousMonth10`='$ecoscorepermonth[10]',`previousMonth11`='$ecoscorepermonth[0]'";
									if(!mysqli_query($conn, $sql)) '<br/>Error: '. mysqli_error($conn);
								}
								elseif(empty($returnedData))
								{
									$sql = "INSERT INTO `ecoscorepermonth`(`previousMonth0`,`previousMonth1`,`previousMonth2`,`previousMonth3`,`previousMonth4`,`previousMonth5`,`previousMonth6`,`previousMonth7`,`previousMonth8`,`previousMonth9`,`previousMonth10`,`previousMonth11`,`userId`)
											VALUES ('$ecoscorepermonth[0]','$ecoscorepermonth[1]','$ecoscorepermonth[2]','$ecoscorepermonth[3]','$ecoscorepermonth[4]','$ecoscorepermonth[5]','$ecoscorepermonth[6]','$ecoscorepermonth[7]','$ecoscorepermonth[8]','$ecoscorepermonth[9]','$ecoscorepermonth[10]','$ecoscorepermonth[11]','$id');";
									if(!mysqli_query($conn, $sql)) '<br/>Error: '. mysqli_error($conn);
								}
								echo"<br/>ecoscorepermonth is:<br/>";
								print_r($ecoscorepermonth);
								echo"<br/>validactivitypermonth is:<br/>";
								print_r($validactivitypermonth);
								echo"<br/>bodyactivitypermonth is:<br/>";
								print_r($bodyactivitypermonth);
							}

						//Calculation of the earliest Date
							$sql = "SELECT `dates` FROM `userlocations` WHERE `uploaderId`='$id' ORDER BY `dates` ASC LIMIT 1;";
							$result = mysqli_query($conn, $sql); 
							$record = mysqli_fetch_all($result,MYSQLI_ASSOC);
							if(!empty($record)) $startPoint = $record[0]['dates']; // This is the earliest date
						//Calculation of the latest Date
							$sql = "SELECT `dates` FROM `userlocations` WHERE `uploaderId`='$id' ORDER BY `dates` DESC LIMIT 1;";
							$result = mysqli_query($conn, $sql); 
							$record = mysqli_fetch_all($result,MYSQLI_ASSOC);
							if(!empty($record)) $endPoint = $record[0]['dates']; //This is the latest date

						// Check in the DB if there is a previous uploaded data for this user
							$sql = "SELECT * FROM `userresults` WHERE `userId`='$id'";
							$result = mysqli_query($conn, $sql); // Get results from database
							$record = mysqli_fetch_all($result,MYSQLI_ASSOC);
						// If old data exists update it with the new calculated data
							if(!empty($record))
							{
								$sql = "UPDATE `userresults`
								SET `startPoint`='$startPoint',`endPoint`='$endPoint',`lastUpload`=CURRENT_TIMESTAMP() WHERE `userId`='$id'";
								if(!mysqli_query($conn, $sql)) '<br/>Error: '. mysqli_error($conn);
								else echo "<br/>Your locations updated successfully.<br/>";
							}
							else
							{
								$sql = "INSERT INTO `userresults`(`userId`,`startPoint`,`endPoint`) VALUES ('$id','$startPoint','$endPoint');";
								if(!mysqli_query($conn, $sql)) '<br/>Error: '. mysqli_error($conn);
								echo "<br/> Record added successfully.Welcome!<br/>";
							}
/// username and score grouped by score. Rank is found afterwards
							$sql = "SELECT username, case when month(curdate())= 1 then previousMonth0
														  when month(curdate())= 2 then previousMonth1
														  when month(curdate())= 3 then previousMonth2
														  when month(curdate())= 4 then previousMonth3
														  when month(curdate())= 5 then previousMonth4
														  when month(curdate())= 6 then previousMonth5
														  when month(curdate())= 7 then previousMonth6
														  when month(curdate())= 8 then previousMonth7
														  when month(curdate())= 9 then previousMonth8
														  when month(curdate())=10 then previousMonth9
														  when month(curdate())=11 then previousMonth10
														  when month(curdate())=12 then previousMonth11
														  END AS 'Months' FROM `user` INNER JOIN `ecoscorepermonth`
														  ON `user`.`id`=`ecoscorepermonth`.`userId` WHERE `id`='$id'
									ORDER BY 'Months' DESC;";
	    					$results = mysqli_query($conn, $sql); // Get results from database
    						$leaders = mysqli_fetch_all($results,MYSQLI_ASSOC);
                            $userFound = false;
                            //Find the top 3 users from the results of the DB
                            $i=0; 
                            while($i<3 && $i<count($leaders) )
                            {
                        //Calculate the substring you want for every user
                                $uname = $leaders[$i]['username'];
                                if(preg_match('/^[a-zA-Z]+$/',$uname))
                                {
                                    $uname = ucfirst($uname); //The first word is converted to uppercase
                                }
                                else if (preg_match('/^[a-zA-Z]+\_?[a-zA-Z]+$/', $uname))
                                {
                                    $indexOf_ = strpos($uname, "_"); // Locate the index of the first occurence of _
                                    $uname = array(substr($uname, 0,$indexOf_), $uname[$indexOf_+1] . '.');
                                    $uname =  implode(' ', $uname);
                                    $uname = ucwords($uname); // First letter of every Word is converted to Uppercase
                                }
                                $ranking[$i] = array("rank"=> $i+1, "username" => $uname, "currentMonth" => $leaders[$i]['Months']);
                                if($leaders[$i]['username']===$username) $userFound = true;
                                ++$i;
                            }
                            $rankOfUser=3;
                        // If user is not belong in the top 3 users with ecoscore then locate him from the result and update ranking
                            while($rankOfUser<count($leaders) && !$userFound)
                            {
                                if($leaders[$rankOfUser]['username'] === $username)
                                {
                                    $ranking[3] = array("rank"=>$rankOfUser+1, "username" => "Your rank:", "currentMonth" => $leaders[$rankOfUser]['previousMonth0']);
                                    $userFound = true;
                                }
                                $rankOfUser++;
                            }
//                            print_r($ranking);

						}
						else echo '<br/><i style="color:red">*Please upload a file in json format.</i>';
					}
?>
				<span class="errorMessage"><?php echo $errors['data']; ?> </span>
			</form>



<?php
		$sql = "SELECT ";
?>
		</div>
	</main>
<?php include('footer.php');?>
</html>