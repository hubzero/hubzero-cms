<?php
return array(
	/*
	|--------------------------------------------------------------------------
	| Secret
	|--------------------------------------------------------------------------
	|
	| This is an auto-generated, unique alphanumeric code for every
	| installation. It is used for security functions.
	|
	*/
	'secret' => 'youshouldreallychangethis',

	/*
	|--------------------------------------------------------------------------
	| Application Environment
	|--------------------------------------------------------------------------
	|
	| This can affect some functionality of the site. For example, LESS
	| parsing occurs on every page load when in "development" mode but will
	| always prefer cached files when set to "production".
	|
	| Options: "development", "testing", "staging", "production"
	|
	*/
	'application_env' => 'development',

	/*
	|--------------------------------------------------------------------------
	| Error Reporting
	|--------------------------------------------------------------------------
	|
	| Select the appropriate level of reporting from the drop down list. See
	| the Help Screen for full details.
	|
	*/
	'error_reporting' => 'default',

	/*
	|--------------------------------------------------------------------------
	| Debug System
	|--------------------------------------------------------------------------
	|
	| If enabled, diagnostic information, language translation, and SQL errors
	| (if present) will be displayed. The information will be displayed at the
	| foot of every page you view within the Joomla backend and frontend. It is
	| not advisable to leave the debug mode activated when running a live site.
	|
	*/
	'debug' => '1',

	/*
	|--------------------------------------------------------------------------
	| Debug Language
	|--------------------------------------------------------------------------
	|
	| Select whether the debugging indicators (<bold>**...**</bold>) or
	| (<bold>??...??</bold>) for the Joomla! Language files will be displayed.
	| Debug Language will work without Debug System being activated, but you
	| will not get the additional detailed references that will help you
	| correct any errors.
	|
	*/
	'debug_lang' => '0',

	/*
	|--------------------------------------------------------------------------
	| Profile System
	|--------------------------------------------------------------------------
	|
	| If enabled (or 'debug' is enabled), profiling data will be recorded for
	| performance analysis. This can be left on when running a live Web site.
	|
	*/
	'profile' => '0',

	/*
	|--------------------------------------------------------------------------
	| Default Editor
	|--------------------------------------------------------------------------
	|
	| Select the default text editor for your site. Registered Users will be
	| able to change their preference in their personal details if you allow
	| that option.
	|
	*/
	'editor' => 'ckeditor',

	/*
	|--------------------------------------------------------------------------
	| Default List Limit
	|--------------------------------------------------------------------------
	|
	| Sets the default number of items to be displayed when paginating
	| lists of results.
	|
	*/
	'list_limit' => '25',

	/*
	|--------------------------------------------------------------------------
	| Feed Limit
	|--------------------------------------------------------------------------
	|
	| Select the number of content items to show in the feed(s).
	|
	*/
	'feed_limit' => '10',

	/*
	|--------------------------------------------------------------------------
	| Feed Email
	|--------------------------------------------------------------------------
	|
	| The RSS and Atom newsfeeds include the author's email address. Select
	| Author Email to use each author's email (from the User Manager) in the
	| news feed. Select Site Email to include the site 'Mail from' email
	| address for each article.
	|
	*/
	'feed_email' => 'author',

	/*
	|--------------------------------------------------------------------------
	| Gzip Page Compression
	|--------------------------------------------------------------------------
	|
	| Compress buffered output if supported.
	|
	*/
	'gzip' => '0',

	/*
	|--------------------------------------------------------------------------
	| Path to Log Folder
	|--------------------------------------------------------------------------
	|
	| This should be the full (not relative) path to a folder. Default is in
	| the hub's app directory.
	|
	*/
	'log_path' => '/var/www/myhub/app/logs',

	/*
	|--------------------------------------------------------------------------
	| Path to Temp Folder
	|--------------------------------------------------------------------------
	|
	| This should be the full (not relative) path to a folder. Default is in
	| the hub's app directory.
	|
	*/
	'tmp_path' => '/var/www/myhub/app/tmp',

	/*
	|--------------------------------------------------------------------------
	| Force SSL
	|--------------------------------------------------------------------------
	|
	| Force site access to always occur under SSL (https) for selected areas.
	| You will not be able to access selected areas under non-ssl. Note, you
	| must have SSL enabled on your server to utilise this option.
	|
	*/
	'force_ssl' => '0',

	/*
	|--------------------------------------------------------------------------
	| Timezone
	|--------------------------------------------------------------------------
	|
	| Choose a city in the list to configure the date and time for display.
	|
	*/
	'offset' => 'America/Indiana/Indianapolis',

	/*
	|--------------------------------------------------------------------------
	| Site Name
	|--------------------------------------------------------------------------
	|
	| Enter the name of your Web site. This will be used in various locations.
	|
	*/
	'sitename' => 'Hubzilla',

	/*
	|--------------------------------------------------------------------------
	| Include Site Name in Page Titles
	|--------------------------------------------------------------------------
	|
	| Begin or end all Page Titles with the site name. Example:
	|    0 = No
	|    1 = Before: My Site Name - My Article Name
	|    2 = After:  My Article Name - My Site Name
	|
	*/
	//'sitename_pagetitles' => '0',

	/*
	|--------------------------------------------------------------------------
	| Default Captcha
	|--------------------------------------------------------------------------
	|
	| Select the default captcha for your site. You may need to enter required
	| information for your captcha plugin in the Plugin Manager.
	|
	*/
	'captcha' => 'image',

	/*
	|--------------------------------------------------------------------------
	| Default Access Level
	|--------------------------------------------------------------------------
	|
	| Select the default access level for new content, menu items, and other
	| items created on your site.
	|
	*/
	'access' => '1',

	/*
	|--------------------------------------------------------------------------
	| Unicode Aliases
	|--------------------------------------------------------------------------
	|
	| Choose between transliteration and unicode aliases. Transliteration is
	| default.
	|    0 = No
	|    1 = Yes
	|
	*/
	//'unicodeslugs' => '0',

	'log_post_data' => '0',
	'live_site' => '',
	'xmlrpc_server' => '0',
	'helpurl' => 'English (GB) - HUBzero help',
	//'sef' => '1',
	//'sef_rewrite' => '1',
	//'sef_suffix' => '0',
	//'sef_groups' => '0',
	//'api_server' => '1',
);
