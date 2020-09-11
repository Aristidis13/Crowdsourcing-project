<?php
	include('header.php');
	$errors = ['username' =>'', 'password' =>''];
	$submitIsPressed = !empty($_POST['submit']); // Boolean that checks if Submit button has been pressed
	$username = $password = '';
	if($submitIsPressed)
	{
		// Variable Assignments
		$username = htmlspecialchars($_POST['username']);// Content of username form
		$password = htmlspecialchars($_POST['password']);// Content of password form

		if(!empty($username)&& !empty($password))
		{
			$username = mysqli_real_escape_string($conn,$username);
			$password = mysqli_real_escape_string($conn,$password);
			$sql = "SELECT * FROM user WHERE username='$username';"; //MySQL query

			$result = mysqli_query($conn, $sql); // Get results from database
			$record = mysqli_fetch_all($result,MYSQLI_ASSOC);

			if(!empty($record))
			{
				$extractedUsername = $record[0]["username"];
				$extractedPassword = $record[0]["password"];
				$type = $record[0]["type"];
				if($extractedUsername===$username && $password===$extractedPassword && $type==='admin')
				{
					session_start();
					$_SESSION['username'] = $extractedUsername;
					$_SESSION['id'] = $record[0]["id"];
					header('Location: adminMain.php');
				}
				elseif($extractedUsername===$username && password_verify($password, $extractedPassword) && $type=='user')
				{
					session_start();
					$_SESSION['username'] = $extractedUsername;
					$_SESSION['id'] = $record[0]["id"];
					header('Location: userMain.php');
				}
				else $errors['password'] = "<i>*Password is not correct. Try again!</i>";
			}
			else $errors['username'] = "<i>*Username does not exist. Try again.</i>";
		}
	}
?>

<!DOCTYPE html> <!-- Document Type Declaration - Defines what HTML specification will be used for intrepretation of this document-->

<html>
	<main>
	<h1 style="color: black; text-align: center; font-size: 3vw; "> Login To Your Account </h1>
		<div class="dateRangeSelect" id="LoginForm">
			<form action="index.php" id="form" onsubmit="return checkInputs()" method="POST">
				<label style="color: black; font-size: 3vw;"> Username: </label><br/>
				<input type="text" id="username" name="username" style="font-size: 2vw;" value="<?php echo htmlspecialchars($username); ?>"><br/>
				<span class="errorMessage" id="usernameLoginErrorMessage"><?php echo $errors['username']; ?></span>
				<br/>
				<label style="color: black;font-size: 3vw;"> Password:</label><br/>
				<input  type="Password" id="password" name="password" style="font-size: 2vw;" value="<?php echo htmlspecialchars($password); ?>"> <br/>
				<span class="errorMessage" id="passwordLoginErrorMessage"><?php echo $errors['password']; ?></span>
					<br/>
				<input name="submit" id="submit" type="submit" style="font-size: 2vw;" value="Login">
			</form>
			<h5 style="font-size: 2vw;"> No account? Sign up <a href="signUp.php" style="color:#89C746"> <b> here </b></a></h5>
		</div>
	</main>
	<br/>
	<?php include('footer.php');?>
</html>

<script>
function checkInputs()
{
	var username = document.forms['form']['username'].value;
	var password = document.forms['form']['password'].value;
	var submit = document.forms['form']['submit'];

	//Error Classes
	var usernameLoginErrorMessage = document.getElementById("usernameLoginErrorMessage");
	var passwordLoginErrorMessage = document.getElementById("passwordLoginErrorMessage");
	
	//Booleans
	if(username =='' && password =='')
	{
		usernameLoginErrorMessage.innerHTML ="*<i>Username is required<i>";
		passwordLoginErrorMessage.innerHTML ="*<i>Password is required</i>";
		return false;
	}
	else if(password =='')
	{
		passwordLoginErrorMessage.innerHTML ="*<i>Password is required</i>";
		usernameLoginErrorMessage.innerHTML ="";
		return false;
	}
	else if(username =='')
	{
		usernameLoginErrorMessage.innerHTML ="*<i>Username is required<i>";
		passwordLoginErrorMessage.innerHTML ="";
		return false;
	}
	else if(username != '' && password != '')
	{
		return true;
	}
}
</script>