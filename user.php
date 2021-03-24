<?php
require("user_config.php");

if(isset($_GET['logout']) AND $_GET['logout']=='y'){ //get the logout variable from user.php?logout=y
	global $salt,$sess_time,$dbu;
	if(isLoggedIn()){
		$user = isLoggedIn();
		$user_id = $user['id'];
		$dbnu = new PDO('sqlite:'.$dbu);
		$dbnu->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $dbnu->prepare('DELETE FROM active_users WHERE user_id = :user_id');
		$stmt->bindValue(":user_id",$user_id, PDO::PARAM_INT);
		$stmt->execute();
	}
}
if(isLoggedIn()){ // if is logged in, redirect to a script of choice and stop the script.
	$user=isLoggedIn();
	updateExpire($user['id']);
	header('location:'.$header_redirect);
	exit();
}

if(isset($_POST['submitButton'])){
	if (empty($_POST['email'])) {
		die('Error: email is required.');
			}
	elseif (empty($_POST['password'])) {
		die('Error: password is required.');
		}
	$password=hash("sha512",$_POST['password']);
	//echo $_POST['email'].'<br>'.$password.'<br>';
	$dbnu = new PDO('sqlite:'.$dbu);
	$dbnu->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $dbnu->prepare('SELECT * FROM users WHERE email = :email AND password = :password LIMIT 1');
	$stmt->bindParam(":email",$_POST['email'], PDO::PARAM_STR);
	$stmt->bindParam(":password",$password, PDO::PARAM_STR);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if(!empty($row)) {
	$sessID = SQLite3::escapeString(session_id());
	$hash= SQLite3::escapeString(hash("sha512",$sessID.$salt.$_SERVER['HTTP_USER_AGENT']));
	$expires = time()+$sess_time;
	//$dbnu->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $dbnu->prepare('INSERT INTO active_users (user_id,session_id,hash,expires) VALUES (:user_id,:session_id, :hash, :expires)');
	$stmt->bindParam(":user_id",$row['id'], PDO::PARAM_INT);
	$stmt->bindParam(":session_id",$sessID, PDO::PARAM_STR);
	$stmt->bindParam(":hash",$hash, PDO::PARAM_STR);
	$stmt->bindParam(":expires",$expires, PDO::PARAM_INT);
	$stmt->execute();
	header('Location:'.$_SERVER["PHP_SELF"]);
	exit(); 
	}
	else {echo '<h1>Error:Your login credentials are wrong</h1>';}
}
if (!isLoggedIn()) { //show login form if not logged in
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Simple Php/Sqlite login</title>
<meta name="Generator" content="BBedit">
<meta name="Author" content="Zoria Media">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Keywords" content="simple php/sqlite login">
<meta name="Owner" content="Rocky">
<meta name="Description" content="Simple Php/Sqlite login">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style type="text/css">
body {text-align:center;margin-left:auto;margin-right:auto;height:100%;color:#f96302;}
html {height:100%;}
#id {max-width:30em;margin:0 auto;font-size:1.25em;}
form input {margin:.25em 0.5em;}
form label {display:inline-block;font-weight:bold;width:9em;text-align:right;}
form input[type='submit'] {font-size:1.1em;padding:.2em 1.8em;background:#f96302;color:#fff;border:1px solid #66ccff;border-radius:9px 9px;}
</style>
</head>
<body>
<div id="login">
<h1>Please login</h1>
<form id="login" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<label for="email">Email</label>:<input type="text" name="email" id="email"><br>
<label for="password">Password</label>:<input type="password" name="password" id="password"><br>
<input type="submit" name="submitButton" id="submitButton" value="LOGIN">
</form>
</div>
</body>
</html>

<?php } ?>