<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

if ($msg = App::get('request')->getString('msg'))
{
	$errors[] = base64_decode($msg);
}
$config  = isset($config) ?: App::get('config');

foreach ($config->getLoader()->getErrors() as $err)
{
	$errors[] = $err;
}
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr" class="nojs">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>HUBzero : Database Configuration</title>
		<style>
		* {
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
			margin: 0;
			padding: 0;
			-webkit-box-sizing: border-box;
			   -moz-box-sizing: border-box;
			    -ms-box-sizing: border-box;
			     -o-box-sizing: border-box;
			        box-sizing: border-box;
		}

		html {
			height: 100%;
			border: 0;
			font-size: 100%;
			font: inherit;
			vertical-align: baseline;
			-webkit-text-size-adjust: 100%;
			    -ms-text-size-adjust: 100%;
		}
		body {
			height: 100%;
			margin: 0;
			padding: 0;
			background: #fff;
			color: #222;
			font-size: 0.85em;
			line-height: 1.7;
			font-weight: normal;
			text-rendering: optimizeLegibility;
		}
		code {
			display: inline-block;
			font-size: 0.85em;
			font-family: Monaco, Consolas, "Lucida Console", monospace;
			background-color: #eee;
			color: #555;
			padding: 0em 0.6em;
			margin: 0 0.2em;
			border: none;
		}
		h1 {
			width: 150px;
		}
		.container {
			margin: 2em auto;
			max-width: 65em;
			text-align: left;
			padding: 0 2em;
		}
		.input-wrap {
			margin: 1em 0;
		}
		.input-wrap input,
		.input-wrap select {
			width: 100%;
		}
		.input-submit {
			text-align: center;
		}

		fieldset {
			margin: 1em 0;
			padding: 4em 2em 1em 2em;
			position: relative;
			border: none;
			background-color: #f0f0f0;
			-webkit-border-radius: .25em;
			-moz-border-radius: .25em;
			border-radius: .25em;
		}
		fieldset>.input-wrap:last-child {
			margin-bottom: 0;
		}
		fieldset>legend {
			margin: 0;
			padding: 1em 2em;
			background: #222;
			color: #fff;
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			-webkit-border-radius: .25em .25em 0 0;
			-moz-border-radius: .25em .25em 0 0;
			border-radius: .25em .25em 0 0;
		}
		input[type=text],
		input[type=number],
		input[type=password],
		input[type=email],
		input[type=date],
		input[type=url],
		input[type=color],
		input[type=search],
		input[type=tel],
		input[type=month],
		input[type=week],
		input[type=time],
		textarea {
			font-size: 1em;
			line-height: 1.61803399em;
			width: 100%;
			max-width: 100%;
			padding: 0.6045084975em;
			outline: none;
			background-color: #fff;
			border: 1px solid #d9d9d9;
			border-color: rgba(0, 0, 0, 0.15);
			-webkit-border-radius: .25em;
			-moz-border-radius: .25em;
			border-radius: .25em;
			-webkit-transition: all .1s ease-out;
			-moz-transition: all .1s ease-out;
			-o-transition: all .1s ease-out;
			transition: all .1s ease-out;
			-webkit-background-clip: padding-box;
			-moz-background-clip: padding-box;
			background-clip: padding-box;
			margin-bottom: 1em;
		}
		input[type=text]:hover,
		input[type=number]:hover,
		input[type=password]:hover,
		input[type=email]:hover,
		input[type=date]:hover,
		input[type=url]:hover,
		input[type=color]:hover,
		input[type=search]:hover,
		input[type=tel]:hover,
		input[type=month]:hover,
		input[type=week]:hover,
		input[type=time]:hover,
		textarea:hover,
		input[type=text]:focus,
		input[type=number]:focus,
		input[type=password]:focus,
		input[type=email]:focus,
		input[type=date]:focus,
		input[type=url]:focus,
		input[type=color]:focus,
		input[type=search]:focus,
		input[type=tel]:focus,
		input[type=month]:focus,
		input[type=week]:focus,
		input[type=time]:focus,
		textarea:focus {
			border-color: #222;
		}
		input[type=submit] {
			display: inline-block;
			*display: inline;
			color: #fff;
			text-align: center;
			vertical-align: middle;
			cursor: pointer;
			position: relative;
			outline: 0;
			font-size: 1em;
			font-weight: 400;
			white-space: nowrap;
			word-wrap: normal;
			border: none;
			padding: .7em .91em;
			-webkit-border-radius: .25em;
			-moz-border-radius: .25em;
			border-radius: .25em;
			background-color: #a3ca60;
			*zoom: 1;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
			-webkit-background-clip: padding-box;
			-moz-background-clip: padding-box;
			background-clip: padding-box;
			-webkit-appearance: none;
			--webkit-appearance: none
		}
		select:not([multiple]):not([size]) {
			font-family: @sansFontFamily;
			font-size: 1em;
			line-height: 1.61803399em;
			max-width: 100%;
			padding: 0.6045084975em;
			outline: none;
			border: 1px solid #D9D9D9;
			border: 1px solid rgba(0, 0, 0, 0.15);
			-webkit-border-radius: .25em;
			-moz-border-radius: .25em;
			border-radius: .25em;
			background-color: #fff;
			-webkit-background-clip: padding-box;
			-moz-background-clip: padding-box;
			background-clip: padding-box;
			-webkit-appearance: none;
			-moz-appearance: none;
			-webkit-transition: all .1s ease-out;
			-moz-transition: all .1s ease-out;
			-o-transition: all .1s ease-out;
			transition: all .1s ease-out;
			padding-right: 20px;
			background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%2224%22%20height%3D%2216%22%20viewBox%3D%220%200%2024%2016%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%0A%20%20%20%20%3Cpolygon%20fill%3D%22%23666%22%20points%3D%2212%201%209%206%2015%206%22%20%2F%3E%0A%20%20%20%20%3Cpolygon%20fill%3D%22%23666%22%20points%3D%2212%2013%209%208%2015%208%22%20%2F%3E%0A%3C%2Fsvg%3E%0A");
			background-repeat: no-repeat;
			background-position: 100% 50%
		}
		select:not([multiple]):not([size]):hover {
			border-color: #222;
		}
		select:not([multiple]):not([size])::-ms-expand {
			display: none;
		}
		select:not([multiple]):not([size]):disabled {
			background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%2224%22%20height%3D%2216%22%20viewBox%3D%220%200%2024%2016%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%0A%20%20%20%20%3Cpolygon%20fill%3D%22%23999%22%20points%3D%2212%201%209%206%2015%206%22%20%2F%3E%0A%20%20%20%20%3Cpolygon%20fill%3D%22%23999%22%20points%3D%2212%2013%209%208%2015%208%22%20%2F%3E%0A%3C%2Fsvg%3E%0A")
		}
		.error {
			background-color: #f9e4e4;
			color: #dd5555;
			margin: 1em 0;
			padding: 1em;
			vertical-align: bottom;
			-webkit-border-radius: 0.25em;
			-moze-border-radius: 0.25em;
			border-radius: 0.25em;
		}
		input[type=submit]:hover {
			background-color: #008000;
		}
		</style>
	</head>
	<body>
		<div class="container">
			<header>
				<h1>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 294.6 62.9">
						<path d="M87.3 15.7v28.9c0 10.2-7.7 18.3-20.4 18.3-9.7 0-19.7-5.5-19.7-19.9V17L58 15.7v25.7c0 4.9.9 11 9.4 11 7.6 0 9-5.2 9-11.9V17.1l10.9-1.4zM177.2 17.5h-28l-4.1 5.1H167l-31.4 39h40.1l1.4-5.1h-31.4M234 26.3h.2c3.2-7.6 8.2-9.9 16.4-9.9l-2.2 5.2c-.5-.1-1.3-.1-2-.1-5.5 0-12.3 6.9-12.3 17.9v22.2l-5.1 1.1V17.5l5.1-1.1v9.9zM294.6 39.5c0 11.9-9.6 23.1-22.6 23.1-13 0-22.6-11.3-22.6-23.1 0-11.9 9.6-23.1 22.6-23.1 13 0 22.6 11.3 22.6 23.1zm-40.1 0c0 9.4 7.6 18 17.5 18s17.5-8.6 17.5-18-7.6-18-17.5-18c-10 0-17.5 8.6-17.5 18zM215.1 48.9c-3 5.1-8.4 8.7-14.9 8.7-9 0-16.1-7.1-17.3-15.5h39.7c.1-.8.2-1.7.2-2.5 0-11.9-9.6-23.1-22.6-23.1s-22.6 11.3-22.6 23.1c0 11.9 9.6 23.1 22.6 23.1 9.5 0 17-6 20.5-13.8h-5.6zm-14.9-27.4c9 0 16.1 7.1 17.3 15.4h-34.6c1.3-8.3 8.3-15.4 17.3-15.4zM24.5 15.5c-6.2 0-9.9 1.4-13.1 5.6h-.2V.1L0 3.3v59l11.2-.7V36.2c0-5.6 3.3-11 10.3-11 5 0 7.2 4 7.2 8.7v28.4l11.2-.7V30.9c-.1-9.5-6.2-15.4-15.4-15.4zM141.2 31.1c-3.3-9.2-11.8-15.5-21.4-15.5-5.5 0-9.2 1.6-12.9 5.2V0L95.6 3.2v35.4c0 13.5 9.2 23.1 19.9 24.3l9-11.1c-1.4.7-3 1.1-4.5 1.1-7.5 0-13.2-6.2-13.2-13.5s5.7-13.5 13.2-13.5c6.3 0 12 6.2 12 13.5 0 1.3-.2 2.6-.5 3.8l9.7-12.1z"></path>
					</svg>
				</h1>
			</header>
			<main class="content">
				<?php if (!empty($errors)) { ?>
					<p class="error"><?php echo implode('<br />', $errors); ?></p>
				<?php } ?>
				<form action="<?php echo App::get('request')->root() . 'install'; ?>" method="post">
					<fieldset>
						<legend>Database Configuration</legend>

						<div class="input-wrap">
							<label for="db-driver">Driver</label>
							<select name="database[dbtype]" id="db-driver">
								<?php
								$available = \Hubzero\Database\Driver::getConnectors();
								$available = array_map('strtolower', $available);

								foreach ($available as $support)
								{
									?>
									<option value="<?php echo $support; ?>"<?php if ($config->get('dbtype', 'mysql') == $support) { echo ' selected="selected"'; } ?>><?php echo ucfirst($support); ?></option>
									<?php
								}
								?>
							</select>
						</div>

						<div class="input-wrap">
							<label for="db-host">Host</label>
							<input type="text" name="database[host]" id="db-host" value="<?php echo htmlentities($config->get('host', 'localhost')); ?>" placeholder="" />
						</div>

						<div class="input-wrap">
							<label for="db-db">Database Name</label>
							<input type="text" name="database[db]" id="db-db" value="<?php echo htmlentities($config->get('db', 'hub')); ?>" placeholder="" />
						</div>

						<div class="input-wrap">
							<label for="db-user">Username</label>
							<input type="text" name="database[user]" id="db-user" value="<?php echo htmlentities($config->get('user', 'hub')); ?>" placeholder="" />
						</div>

						<div class="input-wrap">
							<label for="db-pass">Password</label>
							<input type="password" name="database[password]" id="db-pass" value="<?php echo htmlentities($config->get('password')); ?>" placeholder="" />
						</div>
					</fieldset>

					<div class="input-submit">
						<input type="submit" value="Save" />
					</div>
				</form>
			</main>
		</div>
	</body>
</html>