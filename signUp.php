<?php include('header.php');

	$errors = ['username' =>'', 'password' =>'', 'email' => ''];
	$username = $password = $email ='';
	$submitIsPressed = !empty($_POST['submit']);

	if($submitIsPressed)
	{
		$username = htmlspecialchars($_POST['username']);
		$password = htmlspecialchars($_POST['password']);
		$email = htmlspecialchars($_POST['email']);

		// Username validation - Username must start with letter
		if(empty($username))
		{
			$errors['username'] = "<i>*Username field must be submitted.</i><br/>";
		}
		elseif(!preg_match('/^[a-zA-Z]+\_?[a-zA-Z]+$/', $username))
			$errors['username'] = "<i>*Username can only contain characters and one _ to distinguish firstname from lastname.<br/>
			Register by using your firstname_lastname or use any length of characters.</i><br/>";

		//Password validation
		if(empty($password))
		{
			$errors['password'] = "<i>*password field must be submitted.</i><br/>";
		}
		elseif(!preg_match('/^(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,20}$/', $password))
			$errors['password'] = "<i>*Password should contain at least one Upper case letter, one number and a special character and its length must be at least 8 and less than 20 characters.</i><br/>";

		// Email validation
		if(empty($email))
		{
			$errors['email'] = "<i>*Email field must be submitted.</i><br/>";
		}
		elseif(!preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-\.]+$/', $email))
			$errors['email'] = "<i>*Email is not valid.</i><br/>";


		if(!array_filter($errors))
		{
//2-Way encryption for the data and creation of the unique id. Cipher Encryption Method is aes-192-ctr. The length of id during testing was 28
			$id = openssl_encrypt($email, "aes-192-ctr", $password, 0, "1357975398765432"); 

//pashword_hash is the safest way to hash a password in PHP 5.5 and PHP 7. The length of hashed password is 60.
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);

//In a real application the file that contains the information for the connection to the database should be stored in some other server that is 
//secure. In this case it is stored somewhere outside of the htdocs file that is specified by the path below
			
			//Then all variables are cleared once again to reduce the chance of any malicious script to harm the database
			$username = mysqli_real_escape_string($conn,$username);
			$password = mysqli_real_escape_string($conn,$hashed_password);
			$id = mysqli_real_escape_string($conn,$id);			
			$email = mysqli_real_escape_string($conn,$email);

			//Then the MySQL query have to be created to enter the record to database
			$MYSQLquery = "INSERT INTO user VALUES ('$id','$username','$password','$email', 'user');";
			//If data is successfully stored redirect else show error message
			if(mysqli_query($conn, $MYSQLquery))
			{
				mysqli_close($conn); // Close the connection to database
				header('Location: index.php');
			}
			else
			{
				$errors['email'] = 'Error: '. mysqli_error($conn);
				mysqli_close($conn); // Close the connection to database
			}
		}
	}
?>
<!DOCTYPE html> <!-- Login Page for user and admin-->
<html>
	<main id="SignUpForm">
		<h2 style="font-size: 2.5vw;"> <b > Create Your Account in a simple step!</b></h2>
		<div id="signUpFormValidation">
			<form id="form" action="signUp.php" method="POST" onsubmit="return signUpInputs()"> <!--onsubmit="signUpCheck()">-->
				<p>
				<!--Username-->
					<b class="signUpLabel"> Username: </b>
					<input class="signUpInput" id="username" name="username" type="text" placeholder="e.g Aris Barlos" value='<?php echo htmlspecialchars($username); ?>'> <br/>
				<span class="errorMessage" id="nameSignUpErrorMessage"><?php echo $errors['username']; ?> </span>
				</p>
				<p>
				<!-- Password-->
					<b class="signUpLabel"> Password: </b>
					<input class="signUpInput" id="password" name="password" type="Password" placeholder="e.g. @Passphrase1@" minlength="8" maxlength="30" value="<?php echo htmlspecialchars($password); ?>"> <br/>
				<span class="errorMessage" id="passwordSignUpErrorMessage"><?php echo $errors['password']; ?></b>
				</p>
				<p>
				<!-- Email Sign Up -->
					<b class="signUpLabel"> Email: </b>
					<input class="signUpInput" id="email" name="email" type="Email" placeholder="e.g. aris@hotmail.com" value="<?php echo htmlspecialchars($email); ?>"> <br/>
				<span class="errorMessage" id="emailSignUpErrorMessage"><?php echo $errors['email']; ?></b>
				</p>
				<input  id="submitButton" type="submit" name='submit' value="Sign Up" style="font-size: 2vw;">		
			</form>
		</div>
	</main>
	<?php include('footer.php');?>
</html>