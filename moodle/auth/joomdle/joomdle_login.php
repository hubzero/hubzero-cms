<?php
require_once('../../config.php');

// NORMAL LOGIN TO MOODLE
if ($_GET['login'] == 'moodle') {
?>
<html>
<head>
<title>Joomdle - Moodle login</title>
</head>
<body>
<h3>Joomdle - Moodle Login</h3>
<FORM action="<?php echo $CFG->wwwroot; ?>/login/index.php" method="POST">
Username: <input type=text name="username">
<br>
Password: <input type=password name="password">
<br>
<INPUT type="SUBMIT" value="Login">
</FORM>
</body>
</html>
<?php
} else {
	//REDIRECT TO JOOMLA
    $url = get_config('', 'joomla_url');
    header ("Location: $url");
}

?>
