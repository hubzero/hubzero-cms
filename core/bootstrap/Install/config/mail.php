<?php
return array(
	/*
	|--------------------------------------------------------------------------
	| Email Driver
	|--------------------------------------------------------------------------
	|
	| Select which mailer for the delivery of site email.
	|    "mail" = PHP mail
	|    "smtp"
	|    "sendmail"
	*/
	'mailer' => 'mail',

	/*
	|--------------------------------------------------------------------------
	| Email FROM Address
	|--------------------------------------------------------------------------
	|
	| The email address that will be used to send site email.
	|
	*/
	'mailfrom' => 'you@yourhub.org',

	/*
	|--------------------------------------------------------------------------
	| Email FROM Name
	|--------------------------------------------------------------------------
	|
	| Text displayed in the header "From:" field when sending a site email.
	| Usually the site name.
	|
	*/
	'fromname' => 'myhub',

	/*
	|--------------------------------------------------------------------------
	| SMTP Authentication Required
	|--------------------------------------------------------------------------
	|
	| If your SMTP Host requires SMTP Authentication.
	|    0 = no
	|    1 = yes
	|
	*/
	'smtpauth' => '0',

	/*
	|--------------------------------------------------------------------------
	| SMTP Host
	|--------------------------------------------------------------------------
	|
	| Enter the name of the SMTP host.
	|
	*/
	'smtphost' => 'localhost',

	/*
	|--------------------------------------------------------------------------
	| SMTP Port
	|--------------------------------------------------------------------------
	|
	| Enter the port number of your SMTP server. Use 25 for most unsecure
	| servers and 465 for most secure servers.
	|
	*/
	'smtpport' => '25',

	/*
	|--------------------------------------------------------------------------
	| SMTP Username
	|--------------------------------------------------------------------------
	|
	| Enter the username for access to the SMTP host.
	|
	*/
	'smtpuser' => '',

	/*
	|--------------------------------------------------------------------------
	| SMTP Password
	|--------------------------------------------------------------------------
	|
	| Enter the password for the SMTP host.
	|
	*/
	'smtppass' => '',

	/*
	|--------------------------------------------------------------------------
	| SMTP Security Model
	|--------------------------------------------------------------------------
	|
	| Select the security model that your SMTP server uses.
	|
	*/
	'smtpsecure' => 'none',

	/*
	|--------------------------------------------------------------------------
	| Sendmail Path
	|--------------------------------------------------------------------------
	|
	| Enter the path to the sendmail program directory on the host server.
	|
	*/
	'sendmail' => '/usr/sbin/sendmail',
);
