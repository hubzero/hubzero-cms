<?php

/**
 * @author Antonio Duran
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: Joomdle
 *
 * Checks against Joomla web services provided my Joomdle
 *
 * 2009-10-25  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/auth/manual/auth.php');
require_once($CFG->dirroot.'/search/lib.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->dirroot.'/mod/forum/lib.php');
require_once($CFG->dirroot.'/lib/datalib.php');
require_once($CFG->dirroot.'/lib/gdlib.php');
require_once($CFG->dirroot.'/lib/grade/grade_grade.php');
require_once($CFG->dirroot.'/lib/grade/grade_item.php');
require_once($CFG->dirroot.'/lib/gradelib.php');

/**
 * Joomdle authentication plugin.
 */
class auth_plugin_joomdle extends auth_plugin_manual {

    /**
     * Constructor.
     */
    function auth_plugin_joomdle() {
        $this->authtype = 'joomdle';
        $this->config = get_config('', 'joomla_url');
	$this->config = get_config('auth/joomdle'); //XXX
        if (empty($this->config->extencoding)) {
            $this->config->extencoding = 'utf-8';
        }
    }

       function can_signup() {
        return true;
    }

  function user_signup($user, $notify=true) {
        global $CFG;
        require_once($CFG->dirroot.'/user/profile/lib.php');


	$password_clear = $user->password;
        $user->password = hash_internal_user_password($user->password);

        if (! ($user->id = insert_record('user', $user)) ) {
            print_error('auth_emailnoinsert','auth');
        }

        /// Save any custom profile field information
        profile_save_data($user);

        $user = get_record('user', 'id', $user->id);

	/* Create user in Joomla */
	$userinfo['username'] = $user->username;
	$userinfo['password'] = $password_clear;
	$userinfo['password2'] = $password_clear;
	$userinfo['name'] = $user->firstname. " " . $user->lastname;
	$userinfo['email'] = $user->email;
	$userinfo['block'] = 1;

	$this->call_method ("createUser", $userinfo);

        events_trigger('user_created', $user);

        if (! send_confirmation_email($user)) {
            print_error('auth_emailnoemail','auth');
        }

        if ($notify) {
            global $CFG;
            $emailconfirm = get_string('emailconfirm');
            $navlinks = array();
            $navlinks[] = array('name' => $emailconfirm, 'link' => null, 'type' => 'misc');
            $navigation = build_navigation($navlinks);

            print_header($emailconfirm, $emailconfirm, $navigation);
            notice(get_string('emailconfirmsent', '', $user->email), "$CFG->wwwroot/index.php");
        } else {
            return true;
        }
    }

 function can_confirm() {
        return true;
    }

   function user_confirm($username, $confirmsecret) {
        $user = get_complete_user_data('username', $username);

        if (!empty($user)) {
            if ($user->confirmed) {
                return AUTH_CONFIRM_ALREADY;

            } else if ($user->auth != 'joomdle') {
                return AUTH_CONFIRM_ERROR;

            } else if ($user->secret == stripslashes($confirmsecret)) {   // They have provided the secret key to get in
                if (!set_field("user", "confirmed", 1, "id", $user->id)) {
                    return AUTH_CONFIRM_FAIL;
                }
                if (!set_field("user", "firstaccess", time(), "id", $user->id)) {
                    return AUTH_CONFIRM_FAIL;
                }

		/* Enable de user in Joomla */
		$this->call_method ("activateUser", $username);

                return AUTH_CONFIRM_OK;
            }
        } else {
            return AUTH_CONFIRM_ERROR;
        }
    }


    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     *
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {

	    if (!$password)
		    return false;

	$user = get_complete_user_data ('username', $username);

	if (!$user)
		return false;

	$logged = $this->call_method ("login", $username, $password);

	return $logged;

    }
	function can_change_password() {
		return true;
	}

	function user_update_password ($user, $password)
	{
		$return =  $this->call_method ("changePassword", $user->username, $password);

		return true;
//		return $return;
	}

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err, $user_fields) {
        $this->config = get_config('', 'joomla_url');
        include "config.html";
    }

    function process_config($config) {
	if (!isset($config->joomla_url)) {
		$config->joomla_url = 'http://localhost/joomla';
				            }
	set_config('joomla_url', $config->joomla_url); 

	if (!isset($config->connection_method)) {
		$config->connection_method = 'fgc';
	}
	set_config('connection_method', $config->connection_method, 'auth/joomdle'); 

	if (!isset($config->sync_to_joomla)) {
		$config->sync_to_joomla = 0;
	}
	set_config('sync_to_joomla', $config->sync_to_joomla, 'auth/joomdle'); 
	/*
	if (!isset($config->jomsocial_integration)) {
		$config->jomsocial_integration = 0;
	}
	set_config('jomsocial_integration', $config->jomsocial_integration, 'auth/joomdle'); 
	*/

	if (!isset($config->jomsocial_activities)) {
		$config->jomsocial_activities = 0;
	}
	set_config('jomsocial_activities', $config->jomsocial_activities, 'auth/joomdle'); 

	if (!isset($config->jomsocial_groups)) {
		$config->jomsocial_groups = 0;
	}
	set_config('jomsocial_groups', $config->jomsocial_groups, 'auth/joomdle'); 

	if (!isset($config->enrol_parents)) {
		$config->enrol_parents = 0;
	}
	set_config('enrol_parents', $config->enrol_parents, 'auth/joomdle'); 

	if (!isset($config->parent_role_id)) {
		$config->parent_role_id = '';
	}
	set_config('parent_role_id', $config->parent_role_id, 'auth/joomdle'); 

	if (($config->jomsocial_activities) || ($config->jomsocial_groups) || ($config->enrol_parents))
	{
		/* Insert the event handlers */
		if (!record_exists ('events_handlers', 'eventname', 'course_created', 'handlermodule', 'auth/joomdle'))
		{
			$event = 'course_created';
			$handler->eventname = $event;
			$handler->handlermodule = 'auth/joomdle';
			$handler->handlerfile = '/auth/joomdle/auth.php';
			$handler->handlerfunction = serialize ('joomdle_'.$event);
			$handler->schedule = 'instant';
			$handler->status = 0;

			insert_record ('events_handlers', $handler);
		}
		if (!record_exists ('events_handlers', 'eventname', 'course_deleted', 'handlermodule', 'auth/joomdle'))
		{
			$event = 'course_deleted';
			$handler->eventname = $event;
			$handler->handlermodule = 'auth/joomdle';
			$handler->handlerfile = '/auth/joomdle/auth.php';
			$handler->handlerfunction = serialize ('joomdle_'.$event);
			$handler->schedule = 'instant';
			$handler->status = 0;

			insert_record ('events_handlers', $handler);
		}
		if (!record_exists ('events_handlers', 'eventname', 'role_assigned', 'handlermodule', 'auth/joomdle'))
		{
			$event = 'role_assigned';
			$handler->eventname = $event;
			$handler->handlermodule = 'auth/joomdle';
			$handler->handlerfile = '/auth/joomdle/auth.php';
			$handler->handlerfunction = serialize ('joomdle_'.$event);
			$handler->schedule = 'instant';
			$handler->status = 0;

			insert_record ('events_handlers', $handler);
		}
		if (!record_exists ('events_handlers', 'eventname', 'role_unassigned', 'handlermodule', 'auth/joomdle'))
		{
			$event = 'role_unassigned';
			$handler->eventname = $event;
			$handler->handlermodule = 'auth/joomdle';
			$handler->handlerfile = '/auth/joomdle/auth.php';
			$handler->handlerfunction = serialize ('joomdle_'.$event);
			$handler->schedule = 'instant';
			$handler->status = 0;

			insert_record ('events_handlers', $handler);
		}
	}
	else
	{
		/* Delete the event handlers */
		delete_records ('events_handlers', 'eventname', 'course_created', 'handlermodule', 'auth/joomdle');
		delete_records ('events_handlers', 'eventname', 'course_deleted', 'handlermodule', 'auth/joomdle');
		delete_records ('events_handlers', 'eventname', 'role_assigned', 'handlermodule', 'auth/joomdle');
		delete_records ('events_handlers', 'eventname', 'role_unassigned', 'handlermodule', 'auth/joomdle');
	}

	if ($config->sync_to_joomla)
	{
		/* Insert the event handlers */
		if (!record_exists ('events_handlers', 'eventname', 'user_created', 'handlermodule', 'auth/joomdle'))
		{
			$event = 'user_created';
			$handler->eventname = $event;
			$handler->handlermodule = 'auth/joomdle';
			$handler->handlerfile = '/auth/joomdle/auth.php';
			$handler->handlerfunction = serialize ('joomdle_'.$event);
			$handler->schedule = 'instant';
			$handler->status = 0;

			insert_record ('events_handlers', $handler);
		}
		if (!record_exists ('events_handlers', 'eventname', 'user_updated', 'handlermodule', 'auth/joomdle'))
		{
			$event = 'user_updated';
			$handler->eventname = $event;
			$handler->handlermodule = 'auth/joomdle';
			$handler->handlerfile = '/auth/joomdle/auth.php';
			$handler->handlerfunction = serialize ('joomdle_'.$event);
			$handler->schedule = 'instant';
			$handler->status = 0;

			insert_record ('events_handlers', $handler);
		}
		if (!record_exists ('events_handlers', 'eventname', 'user_deleted', 'handlermodule', 'auth/joomdle'))
		{
			$event = 'user_deleted';
			$handler->eventname = $event;
			$handler->handlermodule = 'auth/joomdle';
			$handler->handlerfile = '/auth/joomdle/auth.php';
			$handler->handlerfunction = serialize ('joomdle_'.$event);
			$handler->schedule = 'instant';
			$handler->status = 0;

			insert_record ('events_handlers', $handler);
		}
	}
	else
	{
		/* Delete the event handlers */
		delete_records ('events_handlers', 'eventname', 'user_created', 'handlermodule', 'auth/joomdle');
		delete_records ('events_handlers', 'eventname', 'user_updated', 'handlermodule', 'auth/joomdle');
		delete_records ('events_handlers', 'eventname', 'user_deleted', 'handlermodule', 'auth/joomdle');
	}

	return true; //XXX
    }

    function call_method ($method, $params = '', $params2 = '', $params3 = '' , $params4 = '', $params5 = '')
    {
	$connection_method = get_config('auth/joomdle', 'connection_method');

	if ($connection_method == 'fgc')
		$response = auth_plugin_joomdle::call_method_fgc ($method, $params, $params2, $params3, $params4, $params5);
	else
		$response = auth_plugin_joomdle::call_method_curl ($method, $params, $params2, $params3, $params4, $params5);

	return $response;
    }

    function call_method_fgc ($method, $params = '', $params2 = '', $params3 = '' , $params4 = '', $params5 = '')
    {
	$joomla_xmlrpc_url = get_config (NULL, 'joomla_url').'/xmlrpc/index.php';

	if ($params == '')
		$request = xmlrpc_encode_request("joomdle.".$method, array (), array ('encoding' => 'utf8'));
	else if ($params2 == '')
		$request = xmlrpc_encode_request("joomdle.".$method, array ($params), array ('encoding' => 'utf8'));
	else if ($params3 == '')
		$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2), array ('encoding' => 'utf8'));
	else if ($params4 == '')
		$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2, $params3), array ('encoding' => 'utf8'));
	else if ($params5 == '')
		$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2, $params3, $params4), array ('encoding' => 'utf8'));
	else
		$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2, $params3, $params4, $params5), array ('encoding' => 'utf8'));

	$context = stream_context_create(array('http' => array(
	    'method' => "POST",
	    'header' => "Content-Type: text/xml ",
	    'content' => $request
	)));
	$response = file_get_contents($joomla_xmlrpc_url, false, $context);
	$data = xmlrpc_decode($response, 'utf8');

	if (is_array ($data))
		if (xmlrpc_is_fault ($data))
		{
			return  "XML-RPC Error (".$data['faultCode']."): ".$data['faultString'];
		}

	return $data;
    }

    function call_method_curl ($method, $params = '', $params2 = '', $params3 = '' , $params4 = '', $params5 = '')
    {
	$joomla_xmlrpc_url = get_config (NULL, 'joomla_url').'/xmlrpc/index.php';

	if (!$params)
		$request = xmlrpc_encode_request("joomdle.".$method, array (), array ('encoding' => 'utf8'));
	else if (!$params2)
		$request = xmlrpc_encode_request("joomdle.".$method, array ($params), array ('encoding' => 'utf8'));
	else if (!$params3)
		$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2), array ('encoding' => 'utf8'));
	else if (!$params4)
		$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2, $params3), array ('encoding' => 'utf8'));
	else if (!$params5)
		$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2, $params3, $params4), array ('encoding' => 'utf8'));
	else
		$request = xmlrpc_encode_request("joomdle.".$method, array ($params, $params2, $params3, $params4, $params5), array ('encoding' => 'utf8'));

	$headers = array();
	array_push($headers,"Content-Type: text/xml");
	array_push($headers,"Content-Length: ".strlen($request));
	array_push($headers,"\r\n");

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $joomla_xmlrpc_url); # URL to post to
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $request );
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' ); # This POST is special, and uses its specified Content-type
	$response = curl_exec( $ch ); # run!
	curl_close($ch);

	$response = xmlrpc_decode($response, 'utf8');

	if (is_array ($response))
		if (xmlrpc_is_fault ($response))
		{
			return  "XML-RPC Error (".$response['faultCode']."): ".$response['faultString'];
		}

	return $response;

}


	function get_file ($file)
	{
		$connection_method = get_config('auth/joomdle', 'connection_method');

		if ($connection_method == 'fgc')
			$response = file_get_contents ($file, FALSE, NULL);
		else
			$response = auth_plugin_joomdle::get_file_curl ($file);

		return $response;
	}

	function get_file_curl ($file)
	{
		$ch = curl_init();
		// set url
		curl_setopt($ch, CURLOPT_URL, $file);

		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// $output contains the output string
		$output = curl_exec($ch);

		// close curl resource to free up system resources
		curl_close($ch);      

		return $output;
	}

    function test () {
	    return "Moodle web services are working!";
    }

    function system_check ()
    {
		$system['joomdle_auth'] = is_enabled_auth('joomdle');
		$system['mnet_auth'] = is_enabled_auth('mnet');

		$joomla_url = get_config (NULL, 'joomla_url');
		if ($joomla_url == '')
		{
			$system['joomdle_configured'] = 0;
		}
		else
		{
			$system['joomdle_configured'] = 1;
			$data = $this->call_method ("test");
			$system['test_data'] = $data;

		}
		return $system;
    }

	function get_paypal_config ()
	{
		global $CFG;

		$paypal_config = array ();
		$paypal_config['paypalurl'] = empty($CFG->usepaypalsandbox) ? 'https://www.paypal.com/cgi-bin/webscr' : 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		$paypal_config['paypalbusiness'] = $CFG->enrol_paypalbusiness;

		return $paypal_config;
	}

    function my_courses ($username) {
        global $CFG;
	
	$username = addslashes ($username);
	$user = get_complete_user_data ('username', $username);
	$c = get_my_courses ($user->id);
        return $c;
    }

    /**
      * Returns course list
      * 
      * @param int $available If true, return only enrollable courses
      */
    function list_courses ($available = 0, $sortby = 'created', $guest = 0) {
	global $CFG;

	$sortby = addslashes ($sortby);

	$where = '';
	if ($available)
		$where = " AND co.enrollable = '1'";
	if ($guest)
		$where = " AND co.guest = '1'";

	$query =
	    "SELECT
		co.id          AS remoteid,
		ca.id          AS cat_id,
		ca.name        AS cat_name,
		ca.description AS cat_description,
		co.sortorder,
		co.fullname,
		co.shortname,
		co.idnumber,
		co.summary,
		co.startdate,
		co.cost,
		co.currency,
		co.timecreated as created,
		co.timemodified as modified,
		co.defaultrole AS defaultroleid,
		r.name         AS defaultrolename 
	    FROM
		{$CFG->prefix}course_categories ca
	    JOIN
		{$CFG->prefix}course co ON
		ca.id = co.category
	    LEFT JOIN
		{$CFG->prefix}role r ON
		r.id = co.defaultrole
	    WHERE
		co.visible = '1' 
		$where
	    ORDER BY
		$sortby 
		";

	$a = get_records_sql($query);

return $a;

    }
	
	/**
      * Returns assignment list
      * 
      * @param int $course_id the course to list for
      */
    function list_scorm_quiz ($course_id = 0, $sortby = 'course', $guest = 0) {
	global $CFG;

	$sortby = addslashes ($sortby);
	$course_id = addslashes ($course_id);

	$where = '';

	$query =
	    "SELECT
		a.id AS remoteid,
		a.name AS name,
		a.course AS course
	    FROM
		{$CFG->prefix}scorm a

	    ORDER BY
		$sortby 
		";

	$a = get_records_sql($query);

return $a;

    }
	
		/**
      * Returns quiz list
      * 
      * @param int $course_id the course to list for
      */
    function list_quiz ($course_id = 0, $sortby = 'course', $guest = 0) {
	global $CFG;

	$sortby = addslashes ($sortby);
	$course_id = addslashes ($course_id);

	$where = '';

	$query =
	    "SELECT
		a.id AS remoteid,
		a.name AS name,
		a.course AS course
	    FROM
		{$CFG->prefix}quiz a

	    ORDER BY
		$sortby 
		";

	$a = get_records_sql($query);

return $a;

    }

    /**
      * Returns course category list
      * 
      * @param int $cat Parent category
      */
    function get_course_categories ($cat = 0) {
	global $CFG;

	$cat = addslashes ($cat);
	$query =
	    "SELECT *
	    FROM
		{$CFG->prefix}course_categories
	    WHERE
		visible = '1' AND
		parent = '$cat'
	    ORDER BY
		sortorder ASC
		";

	return get_records_sql($query);

    }


    /**
      * Returns courses from a specific category
      * 
      * @param string $category Category name
      * @param int $available If true, return only enrollable courses
      */
    function courses_by_category ($category, $available = 0) {
	global $CFG;

	$category = addslashes ($category);
	$where = '';
	if ($available)
		$where = " AND co.enrollable = '1'";

	$query =
	    "SELECT
		co.id          AS remoteid,
		ca.id          AS cat_id,
		ca.name        AS cat_name,
		ca.description AS cat_description,
		co.sortorder,
		co.fullname,
		co.shortname,
		co.idnumber,
		co.summary,
		co.startdate,
		co.cost,
		co.currency,
		co.timecreated as created,
		co.timemodified as modified,
		co.defaultrole AS defaultroleid,
		r.name         AS defaultrolename 
	    FROM
		{$CFG->prefix}course_categories ca
	    JOIN
		{$CFG->prefix}course co ON
		ca.id = co.category
	    LEFT JOIN
		{$CFG->prefix}role r ON
		r.id = co.defaultrole
	    WHERE
		co.visible = '1' AND
		ca.id = '$category'
		$where
	    ORDER BY
		sortorder ASC
		";

	return get_records_sql($query);

    }


    /**
      * Returns courses that have their enrolment period between two dates
      * 
      * @param int $start_date Interval start, Unix Timestamp format
      * @param int $end_date Interval end, Unix Timestamp format
      */
    function courses_by_date ($start_date, $end_date) {
	global $CFG;

	$start_date = addslashes ($start_date);
	$end_date = addslashes ($end_date);

		$query =
		    "SELECT
			co.id          AS remoteid,
			ca.id          AS cat_id,
			ca.name        AS cat_name,
			ca.description AS cat_description,
			co.sortorder,
			co.fullname,
			co.shortname,
			co.idnumber,
			co.summary,
			co.startdate,
			co.cost,
			co.currency,
			co.defaultrole AS defaultroleid,
			r.name         AS defaultrolename 
		    FROM
			{$CFG->prefix}course_categories ca
		    JOIN
			{$CFG->prefix}course co ON
			ca.id = co.category
		    LEFT JOIN
			{$CFG->prefix}role r ON
			r.id = co.defaultrole
		    WHERE
			co.visible = '1' AND
			co.enrollable = '2' AND
			enrolstartdate >= '$start_date' AND
			enrolenddate <= '$end_date'
		    ORDER BY
			sortorder ASC
			";

	return get_records_sql($query);
    }

    /**
      * Returns detailed info aboout a course
      * 
      * @param int $id Course identifier
      */
    function get_course_info ($id) {
	global $CFG;

	$id = addslashes ($id);
	$query =
	    "SELECT
		co.id          AS remoteid,
		ca.id          AS cat_id,
		ca.name        AS cat_name,
		ca.description AS cat_description,
		co.sortorder,
		co.fullname,
		co.shortname,
		co.idnumber,
		co.summary,
		co.startdate,
		co.cost,
		co.currency,
		co.numsections,
		co.enrolstartdate,
		co.enrolenddate,
		co.enrolperiod,
		co.lang,
		co.defaultrole AS defaultroleid,
		r.name         AS defaultrolename 
	    FROM
		{$CFG->prefix}course_categories ca
	    JOIN
		{$CFG->prefix}course co ON
		ca.id = co.category
	    LEFT JOIN
		{$CFG->prefix}role r ON
		r.id = co.defaultrole
	    WHERE
		co.id = '$id'
	    ORDER BY
		sortorder ASC
		";

	return get_record_sql($query);
		
    }
	
	/**
	*	Returns info about a scorm quiz
	*	
	* @param int $id Assignment identifier
	*/
	function get_scorm_quiz_info ($id) {
	global $CFG;
	
	$id = addslashes ($id);
	
	$query =
	    "SELECT
		a.id AS remoteid,
		a.name AS fullname,
		a.summary
	    FROM
		{$CFG->prefix}scorm a 
	    WHERE
		a.id = '$id'
		";

	return get_record_sql($query);
	
	}
	
	/**
	*	Returns info about a quiz
	*	
	* @param int $id Assignment identifier
	*/
	function get_quiz_info ($id) {
	global $CFG;
	
	$id = addslashes ($id);
	
	$query =
	    "SELECT
		a.id AS remoteid,
		a.name AS fullname,
		a.intro AS summary
	    FROM
		{$CFG->prefix}quiz a 
	    WHERE
		a.id = '$id'
		";

	return get_record_sql($query);
	
	}

    /**
      * Returns course topics
      * 
      * @param int $id Course identifier
      */
    function get_course_contents ($id) {
	global $CFG;

	$id = addslashes ($id);
	$query =
	    "SELECT
		cs.section,
		cs.summary
	    FROM
		{$CFG->prefix}course_sections cs
	    WHERE
		cs.course = '$id'
		and cs.visible = 1
		";

	    // get_records_sql necesita un ID unico en la primera columna devuelta
	return get_records_sql($query);

    }

    /**
      * Returns editing teachers
      * 
      * @param int $id Course identifier
      */
    function get_course_editing_teachers ($id) {
	global $CFG;

	$id = addslashes ($id);
	$context = get_context_instance(CONTEXT_COURSE, $id);
	/* 3 indica profesores editores (table mdl_role) */
	$profs = get_role_users(3 , $context);

	return $profs;
    }

    /**
      * Returns non-editing teachers
      * 
      * @param int $id Course identifier
      */
    function get_course_non_editing_teachers ($id) {
	global $CFG;

	$id = addslashes ($id);
	$context = get_context_instance(CONTEXT_COURSE, $id);
	/* 4 indica profesores no editores (table mdl_role) */
	$profs = get_role_users(4 , $context);

	return $profs;
    }

    /**
      * Returns number of visible courses
      * 
      */
    function get_course_no () {
	global $CFG;

	$query =
	    "SELECT count(*)
	    FROM
		{$CFG->prefix}course_categories ca
	    JOIN
		{$CFG->prefix}course co ON
		ca.id = co.category
	    LEFT JOIN
		{$CFG->prefix}role r ON
		r.id = co.defaultrole
	    WHERE
		co.visible = '1'
		";

	return count_records_sql($query);

    }

    /**
      * Returns number of visible and enrollable courses
      * 
      */
    function get_enrollable_course_no () {
	global $CFG;

	$query =
	    "SELECT count(*)
	    FROM
		{$CFG->prefix}course_categories ca
	    JOIN
		{$CFG->prefix}course co ON
		ca.id = co.category
	    LEFT JOIN
		{$CFG->prefix}role r ON
		r.id = co.defaultrole
	    WHERE
		co.visible = '1' and
		co.enrollable = '1'
		";

	return count_records_sql($query);

    }

    /**
      * Returns student number
      * 
      */
    function get_student_no () {
	global $CFG;

	$query =
		"select count(distinct (userid)) from  {$CFG->prefix}role_assignments where roleid=5;
		";

	return count_records_sql($query);

    }

    /**
      * Returns number of submitted assignments in each task of the course
      *
      * @param int $id Course identifier
      * 
      */
    function get_assignment_submissions ($id) {
        global $CFG;

	$id = addslashes ($id);
        /* Obtenemos todas las tareas del curso */
        $query =
                "select id,name from  {$CFG->prefix}assignment where course='$id';
                ";
        /* Para cada una, obtenemos el numero de trabajos entregados */

        $tareas = get_records_sql($query);

        $i = 0;
        foreach ($tareas as $tarea)
        {
                $ass_id = $tarea->id;
                $query =
                        "select count(*) from  {$CFG->prefix}assignment_submissions where assignment='$ass_id';
                        ";
                $n = count_records_sql($query);
                $rdo[$i]['id'] = $tarea->id;
                $rdo[$i]['tarea'] = $tarea->name;
                $rdo[$i]['entregados'] = $n;
                $i++;
        }

        return $rdo;
    }

    /**
      * Returns number of submitted assignments in a course
      * 
      * @param int $id Course identifier
      */
    function get_total_assignment_submissions ($id) {
        global $CFG;

	$id = addslashes ($id);
        $query =
		"select count(*) from  {$CFG->prefix}assignment_submissions;
                ";

	$n = count_records_sql($query);

        return $n;
    }

    /**
      * Returns average grade for a task
      * 
      * @param int $id Course identifier
      */
	function get_assignment_grades ($id)
    {
        global $CFG;

		$id = addslashes ($id);
        $SQL = "SELECT g.itemid, gi.itemname as iname,SUM(g.finalgrade) AS sum
              FROM {$CFG->prefix}grade_items gi
               JOIN {$CFG->prefix}grade_grades g      ON g.itemid = gi.id
             WHERE gi.courseid = $id
               AND g.finalgrade IS NOT NULL
              GROUP BY g.itemid";
        $sum_array = array();
        if ($sums = get_records_sql($SQL))
        {
            foreach ($sums as $itemid => $csum) {
                $sql = " select count(*) from mdl_grade_grades where itemid= $itemid";
                $n = count_records_sql($sql);
                $nota['tarea'] = $csum->iname;
                $nota['media'] = $csum->sum / $n;

                $sum_array[] = $nota;
            }
        }

        return $sum_array;
    }

    function get_average_grade ($itemid) {
        global $CFG;

	$id = addslashes ($itemid);
	$avg = 0;
	$SQL = "SELECT g.itemid, gi.itemname as iname,SUM(g.finalgrade) AS sum
		      FROM {$CFG->prefix}grade_items gi
			   JOIN {$CFG->prefix}grade_grades g      ON g.itemid = gi.id
		     WHERE gi.id = '$itemid'
			   AND g.finalgrade IS NOT NULL
		  GROUP BY g.itemid";
	    $sum_array = array();
	    if ($sums = get_record_sql($SQL)) {
		    $sql = " select count(*) from mdl_grade_grades where itemid=$itemid;";
		    $n = count_records_sql($sql);
		    $avg = $sums->sum / $n;
	    }

	return $avg;
  }

    /**
      * Returns stats about student grades
      * FIXME doc return type
      * 
      * @param int $id Course identifier
      */
    function get_assignments_grades ($id) {
        global $CFG;

	$id = addslashes ($id);
        /* Obtenemos todas las tareas del curso */
        $query =
                "select id,name from  {$CFG->prefix}assignment where course='$id';
                ";
        /* Para cada una, obtenemos la nota media */

        $tareas = get_records_sql($query);

        $i = 0;
        foreach ($tareas as $tarea)
        {
                $ass_id = $tarea->id;
                $query =
                        "select itemid,avg(finalgrade) as media
			from  {$CFG->prefix}grade_grades 
			where itemid='$ass_id' and
			finalgrade is not NULL
			GROUP BY itemid;
                        ";
                $n = get_records_sql($query);
                $rdo[$i]['tarea'] = $tarea->name;
		foreach ($n as $nn)
			$rdo[$i]['media'] = $nn;
                $i++;
        }

        return $rdo;
    }

    /**
      * Returns grades of a student for each task in a course
      * 
      * @param string $user Username
      * @param int $cid Course identifier
      */
    function get_user_grades ($user,$cid) {
        global $CFG;

	$user = addslashes ($user);
	$cid = addslashes ($cid);
        $user = get_complete_user_data ('username', $user);
        $uid = $user->id;



 $SQL = "SELECT g.itemid, g.finalgrade,gi.courseid,gi.itemname,gi.id, g.timemodified
                      FROM {$CFG->prefix}grade_items gi
                           JOIN {$CFG->prefix}grade_grades g      ON g.itemid = gi.id
                           JOIN {$CFG->prefix}user u              ON u.id = g.userid
                           JOIN {$CFG->prefix}role_assignments ra ON ra.userid = u.id
                           $groupsql
                     WHERE g.finalgrade IS NOT NULL
			   AND u.id = '$uid'
			   AND gi.courseid ='$cid'
                  GROUP BY g.itemid";


	    $sum_array = array();
	    if ($sums = get_records_sql($SQL)) {

		    $i = 0;
		    foreach ($sums as $sum)
		    {
			 if (! $grade_grade = grade_grade::fetch(array('itemid'=>$sum->id,'userid'=>$uid))) {
				$grade_grade = new grade_grade();
				$grade_grade->userid = $this->user->id;
				$grade_grade->itemid = $grade_object->id;
			    }

			    $grade_item = $grade_grade->load_grade_item();

			    $sums2[$i] = $sum;
			    $scale = $grade_item->load_scale();
			    $formatted_grade = grade_format_gradevalue($sums2[$i]->finalgrade, &$grade_item, true, GRADE_DISPLAY_TYPE_REAL);

			    $sums2[$i]->finalgrade = $formatted_grade;
			    $i++;
		    }
		return $sums2;

	    }

	return array();
    }

    /**
      * Returns latest grades 
      * 
      * @param string $user Username
      * @param int $cid Course identifier
      */
    function get_last_user_grades ($user, $limit) {
        global $CFG;

	$user = addslashes ($user);
        $user = get_complete_user_data ('username', $user);
        $uid = $user->id;

	if (!$limit)
		$limit = 1000;


 $SQL = "SELECT distinct(g.itemid), g.finalgrade,gi.courseid,gi.itemname,gi.id, g.timemodified as tm
                      FROM {$CFG->prefix}grade_items gi
                           JOIN {$CFG->prefix}grade_grades g      ON g.itemid = gi.id
                           JOIN {$CFG->prefix}user u              ON u.id = g.userid
                           JOIN {$CFG->prefix}role_assignments ra ON ra.userid = u.id
                           $groupsql
                     WHERE g.finalgrade IS NOT NULL 
		     	and gi.itemname IS NOT NULL
			   AND u.id = '$uid'
                  	ORDER BY tm
			LIMIT $limit";


	    $sum_array = array();
	    if ($sums = get_records_sql($SQL)) {

		    $i = 0;
		    foreach ($sums as $sum)
		    {
			 if (! $grade_grade = grade_grade::fetch(array('itemid'=>$sum->id,'userid'=>$uid))) {
				$grade_grade = new grade_grade();
				$grade_grade->userid = $this->user->id;
				$grade_grade->itemid = $grade_object->id;
			    }

			    $grade_item = $grade_grade->load_grade_item();

			    $sums2[$i] = $sum;
			    $scale = $grade_item->load_scale();
			    $formatted_grade = grade_format_gradevalue($sums2[$i]->finalgrade, &$grade_item, true, GRADE_DISPLAY_TYPE_REAL);

			    $sums2[$i]->finalgrade = $formatted_grade;

			    $sums2[$i]->average = $this->get_average_grade ($grade_grade->itemid);
			    $i++;
		    }
		return $sums2;

	    }

	return array();
    }
    /**
      * Retursn number of enrolled students in a course
      * 
      * @param int $id Course identifier
      */
    function get_course_students_no($id) {
	global $CFG;

	$id = addslashes ($id);
	$context = get_context_instance(CONTEXT_COURSE, $id);
	/* 5 indica estudiantes (table mdl_role) */
	$alumnos = get_role_users(5 , $context);

	return count($alumnos);
    }

    /**
      * Returns upcoming events for a course
      * 
      * @param int $id Course identifier
      */
    function get_upcoming_events ($id) {
	$id = addslashes ($id);
	$courseshown = $id;
	$filtercourse    = array($courseshown => $id);
	$groupeventsfrom = array($courseshown => 1);

        calendar_set_filters($courses, $group, $user, $filtercourse, $groupeventsfrom, true);
        $events = calendar_get_upcoming($courses, $group, $user,
	get_user_preferences('calendar_lookahead', CALENDAR_UPCOMING_DAYS),
	get_user_preferences('calendar_maxevents', CALENDAR_UPCOMING_MAXEVENTS));


	return ($events);
    }

    /**
      * Returns last news for a course
      * 
      * @param int $id Course identifier
      */
    function get_news_items ($id) {
	$id = addslashes ($id);
        $COURSE = get_record('course', 'id', $id);

	if (!$forum = forum_get_course_forum($COURSE->id, 'news')) {
		return '';
	}

	$modinfo = get_fast_modinfo($COURSE);
	if (empty($modinfo->instances['forum'][$forum->id])) {
		return '';
	}
	$cm = $modinfo->instances['forum'][$forum->id];

	$context = get_context_instance(CONTEXT_MODULE, $cm->id);

        /// User must have perms to view discussions in that forum
	if (!has_capability('mod/forum:viewdiscussion', $context)) {
		return '';
	}

        /// Get all the recent discussions we're allowed to see

	if (! $discussions = forum_get_discussions($cm, 'p.modified DESC', false,
                                                       $currentgroup, $COURSE->newsitems) ) {
		$text .= '('.get_string('nonews', 'forum').')';
                return '';
	}

	return $discussions;
}


    /**
      * Returns daily stats for a course
      * 
      * @param int $id Course identifier
      */
    function get_course_daily_stats ($id) {
        global $CFG;
	$id = addslashes ($id);

        $query =
		"select * from {$CFG->prefix}stats_daily 
		where courseid='$id'
		and roleid='5'
		and stattype='activity';
                ";

	$stats = get_records_sql($query);

	return $stats;
    }

    /**
      * Return access daily stats
      * 
      */
    function get_site_last_week_stats () {
        global $CFG;

        $query =
		"select * from {$CFG->prefix}stats_weekly
		where stattype='logins' 
		order by timeend DESC LIMIT 1;
                ";

	$stats = get_records_sql($query);

	return $stats;
    }

    /**
      * Returns grading system for a course
      * 
      * @param int $id Course identifier
      */
    function get_course_grade_categories ($id) {
        global $CFG;
	$id = addslashes ($id);
        $query =
		"select mdl_grade_categories.fullname, mdl_grade_items.grademin, mdl_grade_items.grademax 
		from mdl_grade_categories, mdl_grade_items 
		where mdl_grade_categories.id = mdl_grade_items.iteminstance 
		and mdl_grade_items.courseid='$id' and itemtype='category';
                ";
		
	$cats = get_records_sql($query);

	return $cats;
    }

function user_exists ($username)
{
	$username = addslashes ($username);
	$user = get_record('user','username',$username);
	if ($user)
		return 1;
	return 0;
}



function init_byte_map(){
  global $byte_map;
  for($x=128;$x<256;++$x){
    $byte_map[chr($x)]=utf8_encode(chr($x));
  }
  $cp1252_map=array(
    "\x80"=>"\xE2\x82\xAC",    // EURO SIGN
    "\x82" => "\xE2\x80\x9A",  // SINGLE LOW-9 QUOTATION MARK
    "\x83" => "\xC6\x92",      // LATIN SMALL LETTER F WITH HOOK
    "\x84" => "\xE2\x80\x9E",  // DOUBLE LOW-9 QUOTATION MARK
    "\x85" => "\xE2\x80\xA6",  // HORIZONTAL ELLIPSIS
    "\x86" => "\xE2\x80\xA0",  // DAGGER
    "\x87" => "\xE2\x80\xA1",  // DOUBLE DAGGER
    "\x88" => "\xCB\x86",      // MODIFIER LETTER CIRCUMFLEX ACCENT
    "\x89" => "\xE2\x80\xB0",  // PER MILLE SIGN
    "\x8A" => "\xC5\xA0",      // LATIN CAPITAL LETTER S WITH CARON
    "\x8B" => "\xE2\x80\xB9",  // SINGLE LEFT-POINTING ANGLE QUOTATION MARK
    "\x8C" => "\xC5\x92",      // LATIN CAPITAL LIGATURE OE
    "\x8E" => "\xC5\xBD",      // LATIN CAPITAL LETTER Z WITH CARON
    "\x91" => "\xE2\x80\x98",  // LEFT SINGLE QUOTATION MARK
    "\x92" => "\xE2\x80\x99",  // RIGHT SINGLE QUOTATION MARK
    "\x93" => "\xE2\x80\x9C",  // LEFT DOUBLE QUOTATION MARK
    "\x94" => "\xE2\x80\x9D",  // RIGHT DOUBLE QUOTATION MARK
    "\x95" => "\xE2\x80\xA2",  // BULLET
    "\x96" => "\xE2\x80\x93",  // EN DASH
    "\x97" => "\xE2\x80\x94",  // EM DASH
    "\x98" => "\xCB\x9C",      // SMALL TILDE
    "\x99" => "\xE2\x84\xA2",  // TRADE MARK SIGN
    "\x9A" => "\xC5\xA1",      // LATIN SMALL LETTER S WITH CARON
    "\x9B" => "\xE2\x80\xBA",  // SINGLE RIGHT-POINTING ANGLE QUOTATION MARK
    "\x9C" => "\xC5\x93",      // LATIN SMALL LIGATURE OE
    "\x9E" => "\xC5\xBE",      // LATIN SMALL LETTER Z WITH CARON
    "\x9F" => "\xC5\xB8"       // LATIN CAPITAL LETTER Y WITH DIAERESIS
  );
  foreach($cp1252_map as $k=>$v){
    $byte_map[$k]=$v;
  }
}

function fix_latin($instr){
$byte_map=array();
init_byte_map();
$ascii_char='[\x00-\x7F]';
$cont_byte='[\x80-\xBF]';
$utf8_2='[\xC0-\xDF]'.$cont_byte;
$utf8_3='[\xE0-\xEF]'.$cont_byte.'{2}';
$utf8_4='[\xF0-\xF7]'.$cont_byte.'{3}';
$utf8_5='[\xF8-\xFB]'.$cont_byte.'{4}';
$nibble_good_chars = "@^($ascii_char+|$utf8_2|$utf8_3|$utf8_4|$utf8_5)(.*)$@s";
  if(mb_check_encoding($instr,'UTF-8'))return $instr; // no need for the rest if it's all valid UTF-8 already
  $outstr='';
  $char='';
  $rest='';
  while((strlen($instr))>0){
    if(1==preg_match($nibble_good_chars,$input,$match)){
      $char=$match[1];
      $rest=$match[2];
      $outstr.=$char;
    }elseif(1==preg_match('@^(.)(.*)$@s',$input,$match)){
      $char=$match[1];
      $rest=$match[2];
      $outstr.=$byte_map[$char];
    }
    $instr=$rest;
  }
  return $outstr;
}




/**
* Creates a new Joomdle user
* XXX Also used to update user profile if the user already exists
* 
* @param string $username Joomla username
*/
function create_joomdle_user ($username)
{
        global $CFG;

	$username = addslashes ($username);
	/* Creamos el nuevo usuario de Moodle si no está creado */
	$user = get_record('user','username',$username);
	if (!$user)
		$user = create_user_record($username, "", "joomdle");

	/* Obtenemos la información del usuario en Joomla */
	$juser_info = $this->call_method ("getUserInfo", $username);

	$email = $juser_info['email'];
	$firstname = $juser_info['firstname'];
	$lastname = $juser_info['lastname'];
	$city = $juser_info['city'];
	$country = $juser_info['country'];
	$lang = $juser_info['lang'];
	$timezone = $juser_info['timezone'];
	$phone1 = $juser_info['phone1'];
	$phone2 = $juser_info['phone2'];
	$address = $juser_info['address'];
	$description = $juser_info['description'];
	$institution = $juser_info['institution'];
	$url = $juser_info['url'];

	$icq = $juser_info['icq'];
	$skype = $juser_info['skype'];
	$aim = $juser_info['aim'];
	$yahoo = $juser_info['yahoo'];
	$msn = $juser_info['msn'];
	$idnumber = $juser_info['idnumber'];
	$department = $juser_info['department'];

	//XXX Maybe this can be optimized for a single DB call...$bool = update_record('user', addslashes_recursive($localuser)); en ment/aut.php
	if (!xmlrpc_is_fault($response)) {

		/* Actualizamos la informacion del usuario recien creado con los datos de Joomla */
		set_field('user', 'firstname', ($firstname), 'id', $user->id);
		set_field('user', 'lastname', ($lastname), 'id', $user->id);
		set_field('user', 'email', $email, 'id', $user->id);

		/* Set first access as now */
		set_field('user', 'firstaccess', time (), 'id', $user->id);
		/* Optional data in Joomla, only fill if has a value */
		if ($city)
			set_field('user', 'city', ($city), 'id', $user->id);
		if ($country)
			set_field('user', 'country', $country, 'id', $user->id);
		if ($lang)
			set_field('user', 'lang', $lang, 'id', $user->id);
		if ($timezone)
			set_field('user', 'timezone', $timezone, 'id', $user->id);
		if ($phone1)
			set_field('user', 'phone1', ($phone1), 'id', $user->id);
		if ($phone2)
			set_field('user', 'phone2', ($phone2), 'id', $user->id);
		if ($address)
			set_field('user', 'address', ($address), 'id', $user->id);
		if ($description)
			set_field('user', 'description', ($description), 'id', $user->id);
		if ($institution)
			set_field('user', 'institution', ($institution), 'id', $user->id);
		if ($url)
			set_field('user', 'url', $url, 'id', $user->id);
		if ($icq)
			set_field('user', 'icq', $icq, 'id', $user->id);
		if ($skype)
			set_field('user', 'skype', $skype, 'id', $user->id);
		if ($aim)
			set_field('user', 'aim', $aim, 'id', $user->id);
		if ($yahoo)
			set_field('user', 'yahoo', $yahoo, 'id', $user->id);
		if ($msn)
			set_field('user', 'msn', $msn, 'id', $user->id);
		if ($idnumber)
			set_field('user', 'idnumber', $idnumber, 'id', $user->id);
		if ($department)
			set_field('user', 'department', $department, 'id', $user->id);
	}

	/* Get user pic */
	if ($juser_info['pic_url'])
	{
		$joomla_url = get_config (NULL, 'joomla_url');
		$pic_url = $joomla_url.'/'.$juser_info['pic_url'];
		//$pic = @file_get_contents ($pic_url, false, NULL);
		$pic = auth_plugin_joomdle::get_file ($pic_url);
		if ($pic)
		{
			//$pic = file_get_contents ($pic_url, false, NULL);
			$pic = auth_plugin_joomdle::get_file_curl ($pic_url);
			$tmp_file = $CFG->dataroot.'/temp/'.'tmp_pic';
			file_put_contents ($tmp_file, $pic);
			$destination = create_profile_image_destination($user->id, 'user');
			process_profile_image ($tmp_file, $destination);
			set_field('user', 'picture', 1, 'id', $user->id);
		}
	}

	return $user;
}

function search_courses ($text, $phrase, $ordering, $limit)
{
        global $CFG;

	$text = addslashes ($text);
	$limit = addslashes ($limit);
      $wheres = array();
        switch ($phrase) {
                case 'exact':
                        $text           = '\'%'.search_escape_string( $text).'%\'';
                        $wheres2        = array();
                        $wheres2[]      = 'co.fullname LIKE '.$text;
                        $wheres2[]      = 'co.shortname LIKE '.$text;
                        $wheres2[]      = 'co.summary LIKE '.$text;
                        $where          = '(' . implode( ') OR (', $wheres2 ) . ')';
                        break;

                case 'all':
                case 'any':
                default:
                        $words = explode( ' ', $text );
                        $wheres = array();
                        foreach ($words as $word) {
				$word           = '\'%'.search_escape_string( $word).'%\'';
                                $wheres2        = array();
				$wheres2[]      = 'co.fullname LIKE '.$word;
				$wheres2[]      = 'co.shortname LIKE '.$word;
				$wheres2[]      = 'co.summary LIKE '.$word;
                                $wheres[]       = implode( ' OR ', $wheres2 );
                        }
                        $where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
                        break;
        }

	switch ( $ordering ) {
                case 'alpha':
                        $order = 'co.fullname ASC';
                        break;

                case 'category':
                        $order = 'ca.name ASC, co.fullname ASC';
                        break;

                case 'newest':
                        $order = 'co.startdate DESC';
                        break;
                case 'oldest':
                        $order = 'co.startdate ASC';
                        break;
                case 'popular':
                default:
                        $order = 'co.fullname DESC';
        }

	$query =
	    "SELECT
		co.id          AS remoteid,
		ca.id          AS cat_id,
		ca.name        AS cat_name,
		ca.description AS cat_description,
		co.sortorder,
		co.fullname,
		co.shortname,
		co.idnumber,
		co.summary,
		co.startdate,
		co.cost,
		co.currency,
		co.defaultrole AS defaultroleid,
		r.name         AS defaultrolename 
	    FROM
		{$CFG->prefix}course_categories ca
	    JOIN
		{$CFG->prefix}course co ON
		ca.id = co.category
	    LEFT JOIN
		{$CFG->prefix}role r ON
		r.id = co.defaultrole
	    WHERE
		co.visible = '1' AND
		$where
	    ORDER BY
		$order
	    LIMIT $limit
		";

	return get_records_sql($query);
}

function search_categories ($text, $phrase, $ordering, $limit)
{
        global $CFG;

	$text = addslashes ($text);
	$limit = addslashes ($limit);
      $wheres = array();
        switch ($phrase) {
                case 'exact':
                        $text           = '\'%'.search_escape_string( $text).'%\'';
                        $wheres2        = array();
                        $wheres2[]      = 'ca.name LIKE '.$text;
                        $wheres2[]      = 'ca.description LIKE '.$text;
                        $where          = '(' . implode( ') OR (', $wheres2 ) . ')';
                        break;

                case 'all':
                case 'any':
                default:
                        $words = explode( ' ', $text );
                        $wheres = array();
                        foreach ($words as $word) {
				$word           = '\'%'.search_escape_string( $word).'%\'';
                                $wheres2        = array();
				$wheres2[]      = 'ca.name LIKE '.$word;
				$wheres2[]      = 'ca.description LIKE '.$word;
                                $wheres[]       = implode( ' OR ', $wheres2 );
                        }
                        $where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
                        break;
        }

	switch ( $ordering ) {
                case 'alpha':
                case 'category':
                        $order = 'ca.name ASC';
                        break;
                case 'newest':
                case 'oldest':
                case 'popular':
                default:
                        $order = 'ca.name DESC';
        }

	$query =
	    "SELECT
		ca.id          AS cat_id,
		ca.name        AS cat_name,
		ca.description AS cat_description
	    FROM
		{$CFG->prefix}course_categories ca
	    WHERE
		$where
	    ORDER BY
		$order
	    LIMIT $limit
		";

	return get_records_sql($query);
}

/* Esta seria para enlazar a moodle, con una consulta mas ligera a la BD XXX Eliminar*/
function search_topicsX ($text, $phrase, $ordering, $limit)
{
        global $CFG;

      $wheres = array();
        switch ($phrase) {
                case 'exact':
                        $text           = '\'%'.search_escape_string( $text).'%\'';
                        $where      = 'cs.summary LIKE '.$text;
                        break;

                case 'all':
                case 'any':
                default:
                        $words = explode( ' ', $text );
                        $wheres = array();
                        foreach ($words as $word) {
				$word           = '\'%'.search_escape_string( $word).'%\'';
				$wheres[]      = 'cs.summary LIKE '.$word;
                        }
                        $where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
                        break;
        }

	switch ( $ordering ) {
                case 'alpha':
                        $order = 'cs.summary ASC';
                        break;
                case 'category':
                        $order = 'co.id ASC';
                        break;
                case 'newest':
                        $order = 'co.id ASC, cs.section DESC';
                        break;
                case 'oldest':
                        $order = 'co.id ASC, cs.section ASC';
                        break;
                case 'popular':
                default:
                        $order = 'cs.summary DESC';
        }

	/* REMEMBER: For get_records_sql First field in query must be UNIQUE!!!!! */
	$query =
	    "SELECT cs.id,
		co.id          AS remoteid,
		co.fullname,
		cs.course,
		cs.section,
		cs.summary
	    FROM
		{$CFG->prefix}course_sections cs 
	JOIN {$CFG->prefix}course co  ON
		co.id = cs.course 
	    WHERE
		$where
	    ORDER BY
		$order
	    LIMIT $limit
		";
	
	return get_records_sql($query);
}

function search_topics ($text, $phrase, $ordering, $limit = 50)
{
        global $CFG;

	$text = addslashes ($text);
	$limit = addslashes ($limit);
      $wheres = array();
        switch ($phrase) {
                case 'exact':
                        $text           = '\'%'.search_escape_string( $text).'%\'';
                        $where      = 'cs.summary LIKE '.$text;
                        break;

                case 'all':
                case 'any':
                default:
                        $words = explode( ' ', $text );
                        $wheres = array();
                        foreach ($words as $word) {
				$word           = '\'%'.search_escape_string( $word).'%\'';
				$wheres[]      = 'cs.summary LIKE '.$word;
                        }
                        $where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
                        break;
        }
	$where .= " and cs.visible = 1";

	switch ( $ordering ) {
                case 'alpha':
                        $order = 'cs.summary ASC';
                        break;
                case 'category':
                        $order = 'co.id ASC';
                        break;
                case 'newest':
                        $order = 'co.id ASC, cs.section DESC';
                        break;
                case 'oldest':
                        $order = 'co.id ASC, cs.section ASC';
                        break;
                case 'popular':
                default:
                        $order = 'cs.summary DESC';
        }

	/* REMEMBER: For get_records_sql First field in query must be UNIQUE!!!!! */
	$query =
	    "SELECT cs.id,
		co.id          AS remoteid,
		co.fullname,
		cs.course,
		cs.section,
		cs.summary,
		ca.id as cat_id,
		ca.name as cat_name
	    FROM
		{$CFG->prefix}course_sections cs 
	JOIN {$CFG->prefix}course co  ON
		co.id = cs.course 
	LEFT JOIN {$CFG->prefix}course_categories ca  ON
		ca.id = co.category
	    WHERE
		$where
	    ORDER BY
		$order
	    LIMIT $limit
		";
	
	return get_records_sql($query);
}


function enrol_user ($username, $course_id)
{
	$username = addslashes ($username);
	$course_id = addslashes ($course_id);
	/* Create the user before if it is not created yet */
	$user = get_record('user','username',$username);
	if (!$user)
		$this->create_joomdle_user ($username);

	$user = get_record('user','username',$username);
	$course = get_record('course', 'id', $course_id);

	enrol_into_course ($course, $user, 'manual');
}


function get_cat_name ($cat_id)
{
        global $CFG;

	$cat_id = addslashes ($cat_id);

	$query = "SELECT name
		FROM  {$CFG->prefix}course_categories
		WHERE id = '$cat_id';";

	$rdo = get_records_sql($query);
	$row = (reset ($rdo));
	return $row->name;
}

function get_my_courses_grades ($username)
{
	$username = addslashes ($username);
	$i = 0;
	$rdo = array ();
	$user = get_complete_user_data ('username', $username);
	$cursos = get_my_courses ($user->id);
	foreach ($cursos as $curso)
	{
		$tareas = $this->get_user_grades ($username, $curso->id);
		$sum = 0;
		$n = count ($tareas);
		$rdo[$i]['id'] = $curso->id;
		$rdo[$i]['fullname'] = $curso->fullname;
		$rdo[$i]['cat_id'] = $curso->category;
		$rdo[$i]['cat_name'] = $this->get_cat_name ($curso->category);
		if ($n)
		{
			foreach ($tareas as  $tarea)
				$sum += $tarea->finalgrade;
			$rdo[$i]['avg'] = $sum/$n;
		}
		else $rdo[$i]['avg'] = 0;
		$i++; 
	}
	return $rdo;
}

function get_moodle_users ($limitstart, $limit, $order, $order_dir, $search )
{
        global $CFG;
	$limitstart = addslashes ($limitstart);
	$limit = addslashes ($limit);
	$order = addslashes ($order);
	$order_dir = addslashes ($order_dir);
	$search = addslashes ($search);

	/* Don't show admins and guests */
	$admins = get_admins();
	foreach ($admins as $admin)
	{
		$a[] = $admin->id;
	}
	$a[] = 1; //Guest user
	$userlist = "'".implode("','", $a)."'";

	if ($limit)
		$limit_c = "LIMIT $limitstart, $limit";
	if ($order != "")
		$order_c = " ORDER BY $order $order_dir";
	else $order_c = "";

	if ($search)
		$users = get_records_sql("SELECT id, username, email,  concat(firstname, ' ', lastname) as name ,auth
						FROM {$CFG->prefix}user
						WHERE deleted = 0
						AND((username like '%$search%') OR (email like '%$search%') OR (firstname like '%$search%') OR (lastname like '%$search%'))
						$order_c
						$limit_c");
	else
		$users = get_records_sql("SELECT id, username, email,  concat(firstname, ' ', lastname) as name,auth
				FROM {$CFG->prefix}user
				WHERE deleted = 0
				$order_c
				$limit_c");

	$i = 0;
	foreach ($users as $user)
	{
		$u[$i] = $user;
		if (in_array ($user->id, $a))
			$u[$i]->admin = '1';
		else $u[$i]->admin = '0';
		$i++;
	}
	return $u;
	//return $users;
}

function xget_moodle_users ($limitstart, $limit, $order, $order_dir, $search = "")
{
        global $CFG;

	/* Don't show admins and guets */
	$admins = get_admins();
	foreach ($admins as $admin)
	{
		$a[] = $admin->id;
	}
	$a[] = 1; //Guest user
	$userlist = "'".implode("','", $a)."'";

	if ($limit)
		$limit_c = "LIMIT $limitstart, $limit";
	if ($order != "")
		$order_c = " ORDER BY $order $order_dir";
	else $order_c = "";

	if ($search)
		$users = get_records_sql("SELECT id, username, email,  concat(firstname, ' ', lastname) as name ,auth
						FROM {$CFG->prefix}user
						WHERE deleted = 0
						AND((username like $search) OR (email like $search) OR (firstname like $search) OR (lastname like $search))
						AND id not in ($userlist)
						$order_c
						$limit_c");
	else
		$users = get_records_sql("SELECT id, username, email,  concat(firstname, ' ', lastname) as name,auth
				FROM {$CFG->prefix}user
				WHERE deleted = 0
				AND id not in ($userlist)
				$order_c
				$limit_c");
	return $users;
}

function get_moodle_users_number ($search = "")
{
        global $CFG;

	$search = addslashes ($search);
	/* Don't show admins and guets */
	$admins = get_admins();
	foreach ($admins as $admin)
	{
		$a[] = $admin->id;
	}
	$a[] = 1; //Guest user
	$userlist = "'".implode("','", $a)."'";

	if ($search)
		$users = count_records_sql("SELECT count(id) as n
						FROM {$CFG->prefix}user
						WHERE deleted = 0
						AND id not in ($userlist)
						AND((username like $search) OR (email like $search) OR (firstname like $search) OR (lastname like $search))");
	else
		$users = count_records_sql("SELECT count(id) as n
				FROM {$CFG->prefix}user
				WHERE deleted = 0
				AND id not in ($userlist)");
	return $users;
}

function check_moodle_users ($users)
{
        global $CFG;

	$admins = get_admins();
	foreach ($admins as $admin)
	{
		$a[] = $admin->id;
	}
	$a[] = 1; //Guest user
	$i = 0;
	foreach ($users as $user)
	{
		$user = get_record('user','username',$user['username']);
		if ($user)
		{
			$users[$i]['m_account'] = 1;
			$users[$i]['auth'] = $user->auth;
			if (in_array ($user->id, $a))
				$users[$i]['admin'] = 1;
			else
				$users[$i]['admin'] = 0;
		}
		else
		{
			$users[$i]['m_account'] = 0;
			$users[$i]['admin'] = 0;
		}
		$i++;
	}

	return $users;
}

function xget_moodle_only_users ($users, $search = "")
{
        global $CFG;

	/* Don't show admins and guets */
	$admins = get_admins();
	foreach ($admins as $admin)
	{
		$a[] = $admin->id;
	}
	$a[] = 1; //Guest user
	$adminlist = "'".implode("','", $a)."'";

	$usernames = array ();
	foreach ($users as $user)
	{
		$usernames[] = $user['username'];
	}

	$userlist = "'".implode("','", $usernames)."'";
	if ($search)
		$users = get_records_sql("SELECT id, username, email,  concat(firstname, ' ', lastname) as name ,auth
						FROM {$CFG->prefix}user
						WHERE deleted = 0 
						AND (username not in ($userlist))
						AND (id not in ($adminlist))
						AND ((username like $search) OR (email like $search) OR (firstname like $search) OR (lastname like $search))");
	else
		$users = get_records_sql("SELECT id, username, email,  concat(firstname, ' ', lastname) as name,auth
				FROM {$CFG->prefix}user
				WHERE deleted = 0
				AND (id not in ($adminlist))
				AND (username not in ($userlist))");
	return $users;
}

function get_moodle_only_users ($users, $search = "")
{
        global $CFG;

	$search = addslashes ($search);
	/* Don't show admins and guets */
	$admins = get_admins();
	foreach ($admins as $admin)
	{
		$a[] = $admin->id;
	}
	$a[] = 1; //Guest user
	$adminlist = "'".implode("','", $a)."'";

	$usernames = array ();
	foreach ($users as $user)
	{
		$usernames[] = $user['username'];
	}

	$userlist = "'".implode("','", $usernames)."'";
	$users = array();
	if ($search)
		$users = get_records_sql("SELECT id, username, email,  concat(firstname, ' ', lastname) as name ,auth
						FROM {$CFG->prefix}user
						WHERE deleted = 0 
						AND (username not in ($userlist))
						AND ((username like '%$search%') OR (email like '%$search%') OR (firstname like '%$search%') OR (lastname like '%$search%'))");
	else
		$users = get_records_sql("SELECT id, username, email,  concat(firstname, ' ', lastname) as name,auth
				FROM {$CFG->prefix}user
				WHERE deleted = 0
				AND (username not in ($userlist))");


	$n = count ($users);
	//return $n;
	$i = 0;
	foreach ($users as $user)
	{
		$u[$i] = $user;
		if (in_array ($user->id, $a))
			$u[$i]->admin = '1';
		else $u[$i]->admin = '0';
		$i++;
	}
	return $u;


/// XXX no vale!
	for ($i = 0; $i < $n; $i++)
	{
	//	if ($i > 2)
	//		return $i;
	//	 $users[$i]->username = 'aa';
		//if (in_array ($user->id, $a))
		//else  $users[$i]->admin = 0;
	}
	return count ($users);
	return $users;
	/*
	foreach ($users as $user)
	{
		if (in_array ($user->id, $a))
			$users[$i]['admin'] = 1;
		//else $users[$i]['admin'] = 0;
		$i++;
	}

	return $users;
	*/
}

function delete_user ($username)
{
	$user = get_record('user', 'username', $username);

	if ($user)
	{
		delete_user ($user);
		return 1;
	}
	return 0;
}

function user_id ($username)
{
	$user = get_record('user','username',$username);
	return $user->id;
}

function user_details ($username)
{
	$user = get_record('user','username',$username);
	return $user;
}

function user_details_by_id ($id)
{
	$user = get_record('user','id',$id);
	return $user;
}

function update_session ($session_id)
{
/*	$sesscache = $_SESSION;
	session_write_close();

	ini_set('session.save_handler', 'files');
	ini_set('session.save_path', '/var/moodledata_test7/sessions'); //XXX Parametro?
	session_name ("MoodleSession");
	session_start();
	session_write_close();
*/
	$_SESSION = $sesscache;
	$sesscache = $_SESSION;
	$sessidcache = session_id();
	session_write_close();
	unset($_SESSION);

	//$session_id = '9bbbc756d7d6fab712753e247c4b8d1e';
	
	$uc = ini_get('session.use_cookies');
	ini_set('session.use_cookies', false);

	unset($_SESSION);
	session_name('MoodleSession'.$CFG->sessioncookie);
	session_id($session_id);
	session_start();
	session_write_close();

	ini_set('session.use_cookies', $uc);
	session_name('MoodleSession'.$CFG->sessioncookie);
	session_id($sessidcache);
	session_start();
	$_SESSION = $sesscache;
	session_write_close();


}

function migrate_to_joomdle ($username)
{
	set_field('user', 'auth', 'joomdle', 'username', $username);
}

function my_events ($username, $cursosid) {
        global $CFG;


		if ($username == 'admin')
        {
            $whereclause .= ' (groupid = 0 AND courseid = 1) ';
        }
        else
		{
			$user = get_complete_user_data ('username', $username);

			$g = array ();
			$i = 0;
            foreach ($cursosid as $course)
            {
                $course_id = $course['id'];
                $cursos_ids[] = $course_id;
				$groups = groups_get_user_groups ($course_id, $user->id);

				if (!count($groups[0]))
					continue;

				foreach ($groups[0] as $group)
					$w[] = " or (courseid = $course_id and groupid = $group)";
			}

			$whereclause = ' (userid = '.$user->id.' AND courseid = 0 AND groupid = 0)';
			$whereclause .= ' OR  (groupid = 0 AND courseid IN ('.implode(',', $cursos_ids).')) ';

			foreach ($w as $cond)
				$whereclause .= $cond;
		}


		//$timeclause = 'timestart >= '.$tstart.' AND timestart <= '.$tend;
		//$whereclause = $timeclause.' AND ('.$whereclause.')';
		$whereclause .= ' AND visible = 1';
		//$events = get_records_select('event', $whereclause, 'timestart');
		$events = get_records_select('event', $whereclause);
		if ($events === false) {
			$events = array();
		}

		return $events;

    }


	function add_parent_role ($child, $parent)
	{
		$parent_user = get_complete_user_data ('username', $parent);
		$child_user = get_complete_user_data ('username', $child);

		$parent_role_id = get_config('auth/joomdle', 'parent_role_id');

		$context   = get_context_instance(CONTEXT_USER, $child_user->id);
		
		role_assign($parent_role_id, $parent_user->id, 0, $context->id ); //, $timestart, 0, $hidden);
	}

	function get_mentees ($username)
	{
		global $CFG;

		$user = get_complete_user_data ('username', $username);
		$usercontexts = get_records_sql("SELECT c.instanceid, c.instanceid, u.firstname, u.lastname
                                         FROM {$CFG->prefix}role_assignments ra,
                                              {$CFG->prefix}context c,
                                              {$CFG->prefix}user u
                                         WHERE ra.userid = $user->id
                                         AND   ra.contextid = c.id
                                         AND   c.instanceid = u.id
                                         AND   c.contextlevel = ".CONTEXT_USER);
		if (!$usercontexts)
			return array ();

		$i = 0;
		foreach ($usercontexts as $usercontext) {
			$users[$i]['id'] = $usercontext->instanceid;
			$child_user = get_complete_user_data ('id', $usercontext->instanceid);
			$users[$i]['username'] = $child_user->username;
			$users[$i]['name'] = $child_user->firstname. " " . $child_user->lastname;
			$i++;
		}

		return $users;
	}

	function get_roles ()
	{
		global $CFG;

		$roles = get_records_sql("SELECT id, name
                                         FROM {$CFG->prefix}role");

		return $roles;
	}

	function get_parents ($username)
	{
		global $CFG;
		$parent_role_id = get_config('auth/joomdle', 'parent_role_id');

		$user = get_complete_user_data ('username', $username);
		/* Get mentors for the student */
		$usercontext   = get_context_instance(CONTEXT_USER, $user->id);
		$usercontextid = $usercontext->id;

		$query =
		    "SELECT r.userid,u.username
		    FROM
			{$CFG->prefix}role_assignments r, {$CFG->prefix}user u
		    WHERE
			r.roleid = '$parent_role_id' and r.contextid = '$usercontextid'
			and r.userid  = u.id
			";

		$mentors =  get_records_sql($query);

		return $mentors;
	}


    function mnet_publishes() {


        $joomla_sp = array();
        $joomla_sp['name']         = 'joomla_sp'; // Name & Description go in lang file
        $joomla_sp['apiversion']   = 1;
        $joomla_sp['methods']      = array('test', 'my_courses', 'list_courses', 'list_quiz', 'list_scorm_quiz', 'get_course_info', 'get_scorm_quiz_info', 'get_quiz_info', 'get_course_contents', 
			'courses_by_category', 'get_course_editing_teachers', 'get_course_non_editing_teachers', 'courses_by_date',
			'get_course_no', 'get_enrollable_course_no', 'get_student_no', 'get_assignment_submissions', 'get_course_students_no',
			'get_upcoming_events', 'get_news_items', 'get_total_assignment_submissions', 'get_assignment_grades', 'get_assignments_grades',
			'get_course_categories', 'get_user_grades', 'get_course_daily_stats', 'get_site_last_week_stats', 'get_course_grade_categories',
			'create_joomdle_user', 'search_courses', 'search_categories', 'search_topics', 'enrol_user', 'get_my_courses_grades',
			'get_moodle_users', 'check_moodle_users', 'get_moodle_only_users', 'user_exists', 'migrate_to_joomdle', 'user_details', 'user_details_by_id',
			'system_check', 'get_moodle_users_number','update_session', 'my_events', 'user_id', 'get_paypal_config', 'delete_user', 'add_parent_role',
			'get_mentees', 'get_roles', 'get_last_user_grades', 'get_parents', 'get_cat_name'
	);

        return array($joomla_sp);
    }

   
	function logoutpage_hook() {
		global $redirect, $USER;

		if ($USER->auth != 'joomdle')
			return;
		
		$remember_cookie = $this->call_method ("logout", $USER->username);
		$r = $remember_cookie['0'];
	//	print_r ( $r );
	//	exit ();

		setcookie($r, '',  time() - 3600, '/','','',0);

		$redirect = get_config (NULL, 'joomla_url').'/components/com_joomdle/views/wrapper/getout.php';
	}

	/* Logs the user in both Joomla and Moodle once auth is passed */
	function user_authenticated_hook ($user, $username, $password)
	{
		global $redirect, $USER;

		/* Login from password change, don't log in to Joomla */
		if (($_POST['password']) && ($_POST['newpassword1']) && ($_POST['newpassword2']))
			return;

		if ($user->auth != 'joomdle')
			return;


		complete_user_login ($user);

		$login_data = base64_encode ($username.':'.$password);

		$redirect_url = get_config (NULL, 'joomla_url').'/index.php?option=com_joomdle&view=login&data='.$login_data;

		redirect($redirect_url);
	}

/*
	function user_updated ($user)
	{
		print_r($user);
		exit ();
	}
*/
	function update_joomla_sessions ()
	{
		global $CFG;
		 $cutoff = time() - 300;

		$query =
		    "SELECT username
		    FROM
			{$CFG->prefix}user
		    WHERE
			auth = 'joomdle' and
			lastaccess > '$cutoff'
			";

		$query = "SELECT username FROM {$CFG->prefix}user WHERE auth = 'joomdle' and lastaccess > '$cutoff';"; ///XXX PREFIX
		$records = get_records_sql($query);
		$usernames = array();
		foreach ($records as $record)
			$usernames[] = $record->username;

		/*
		$request = xmlrpc_encode_request("joomdle.updateSessions", array ($usernames));
		$context = stream_context_create(array('http' => array(
		    'method' => "POST",
		    'header' => "Content-Type: text/xml ",
		    'content' => $request
		)));
		$joomla_xmlrpc_url = get_config (NULL, 'joomla_url').'/xmlrpc/index.php';
		$response = file_get_contents($joomla_xmlrpc_url, false, $context);
		$updates = xmlrpc_decode($response);
*/
		$updates = $this->call_method ("updateSessions", $usernames);
	}

	function cron() {
		$this->update_joomla_sessions();
	}


} //class

	/* This function is outside the class
	   so it can get called by the dispacher
	   FIXME I don't know how to call a method inside the class
	   */

	function joomdle_user_updated ($user)
	{
		global $CFG;

		if ($user->auth != 'joomdle')
			return true;

		/* Update user info in Joomla */
		$userinfo['username'] = $user->username;
	//	$userinfo['password'] = $password_clear;
	//	$userinfo['password2'] = $password_clear;
		$userinfo['name'] = $user->firstname. " " . $user->lastname;
		$userinfo['email'] = $user->email;
		$userinfo['firstname'] = $user->firstname;
		$userinfo['lastname'] = $user->lastname;
		$userinfo['city'] = $user->city;
		$userinfo['country'] = $user->country;
		$userinfo['lang'] = $user->lang;
		$userinfo['timezone'] = $user->timezone;
		$userinfo['phone1'] = $user->phone1;
		$userinfo['phone2'] = $user->phone2;
		$userinfo['address'] = $user->address;
		$userinfo['description'] = $user->description;
		$userinfo['institution'] = $user->institution;
		$userinfo['url'] = $user->url;
		$userinfo['icq'] = $user->icq;
		$userinfo['skype'] = $user->skype;
		$userinfo['aim'] = $user->aim;
		$userinfo['yahoo'] = $user->yahoo;
		$userinfo['msn'] = $user->msn;
		$userinfo['idnumber'] = $user->idnumber;
		$userinfo['department'] = $user->department;

		$id = $user->id;
		$userinfo['pic_url'] = $CFG->wwwroot."/user/pix.php/$id/f1.jpg";

		$userinfo['block'] = 0;

		auth_plugin_joomdle::call_method ("updateUser", $userinfo);

		return true;
	}


	/* Creates a new Joomla user */
	function joomdle_user_created ($user)
	{
		if ($user->auth != 'joomdle')
			return true;

		$password_clear =  ($_POST['newpassword']);

		/* Create user in Joomla */
		$userinfo['username'] = $user->username;
		$userinfo['password'] = $password_clear;
		$userinfo['password2'] = $password_clear;
		$userinfo['name'] = $user->firstname. " " . $user->lastname;
		$userinfo['email'] = $user->email;
		$userinfo['firstname'] = $user->firstname;
		$userinfo['lastname'] = $user->lastname;
		$userinfo['city'] = $user->city;
		$userinfo['country'] = $user->country;
		$userinfo['lang'] = $user->lang;
		$userinfo['timezone'] = $user->timezone;
		$userinfo['phone1'] = $user->phone1;
		$userinfo['phone2'] = $user->phone2;
		$userinfo['address'] = $user->address;
		$userinfo['description'] = $user->description;
		$userinfo['institution'] = $user->institution;
		$userinfo['url'] = $user->url;
		$userinfo['icq'] = $user->icq;
		$userinfo['skype'] = $user->skype;
		$userinfo['aim'] = $user->aim;
		$userinfo['yahoo'] = $user->yahoo;
		$userinfo['msn'] = $user->msn;
		$userinfo['idnumber'] = $user->idnumber;
		$userinfo['department'] = $user->department;

		$userinfo['block'] = 0;

		auth_plugin_joomdle::call_method ("createUser", $userinfo);

		return true;
	}

	function joomdle_user_deleted ($user)
	{
		if ($user->auth != 'joomdle')
			return true;

		auth_plugin_joomdle::call_method ("deleteUser", $user->username);

		return true;
	}

	function joomdle_course_created ($course)
	{
		$activities = get_config('auth/joomdle', 'jomsocial_activities');
		$groups = get_config('auth/joomdle', 'jomsocial_groups');

		/* kludge for the call_method fn to work */
		if (!$course->summary)
			$course->summary = ' ';

		$cat = get_record('course_categories','id',$course->category);

		if ($activities)
			auth_plugin_joomdle::call_method ('addActivityCourse', (int) $course->id, $course->fullname,  $course->summary, (int) $course->category, $cat->name);
		if ($groups)
			auth_plugin_joomdle::call_method ('addJSGroup', $course->fullname,  'Group for course '.$course->fullname,  1, "x");

		return true;
	}

	function joomdle_course_deleted ($course)
	{
		$groups = get_config('auth/joomdle', 'jomsocial_groups');

		if (!$groups)
			return;

		auth_plugin_joomdle::call_method ('removeJSGroup', $course->fullname);

		return true;
	}

	function joomdle_role_assigned ($role)
	{
		global $CFG;

		$activities = get_config('auth/joomdle', 'jomsocial_activities');
		$groups = get_config('auth/joomdle', 'jomsocial_groups');
		$enrol_parents = get_config('auth/joomdle', 'enrol_parents');
		$parent_role_id = get_config('auth/joomdle', 'parent_role_id');


		$context = get_context_instance_by_id ($role->contextid);
		/* If a course enrolment, publish */
		if ($context->contextlevel == CONTEXT_COURSE)
		{
			$courseid = $context->instanceid;
			$course = get_record('course', 'id', $courseid);
			$cat = get_record('course_categories','id',$course->category);
			$user = get_record('user', 'id', $role->userid);

			if ($activities)
				auth_plugin_joomdle::call_method ('addActivityCourseEnrolment', $user->username, (int) $courseid, $course->fullname, (int) $course->category, $cat->name);

			if ($groups)
			{
				/* Join teachers as group admins, and students as regular members */
				if ($role->roleid == 3) //XXX not hardcoded value?
					auth_plugin_joomdle::call_method ('addJSGroupMember', $course->fullname, $user->username, 1);
				else 
				echo	auth_plugin_joomdle::call_method ('addJSGroupMember', $course->fullname, $user->username, -1);
			}

			if (($enrol_parents) && ($parent_role_id))
			{
				if ($role->roleid == 5) //XXX not hardcoded value?
				{
					/* Get mentors for the student */
					$usercontext   = get_context_instance(CONTEXT_USER, $role->userid);
					$usercontextid = $usercontext->id;

					$query =
					    "SELECT userid
					    FROM
						{$CFG->prefix}role_assignments
					    WHERE
						roleid = '$parent_role_id' and contextid = '$usercontextid'
						";

					$mentors =  get_records_sql($query);
					foreach ($mentors as $mentor)
					{
						/* Enrol as parent into course*/
						role_assign($parent_role_id, $mentor->userid, 0, $context->id );
					}
				}
			}
		}

		return true;
	}

	function joomdle_role_unassigned ($role)
	{

		$groups = get_config('auth/joomdle', 'jomsocial_groups');

		if (!$groups)
			return;

		$context = get_context_instance_by_id ($role->contextid);
		/* If a course unenrolment, remove from group */
		if ($context->contextlevel == CONTEXT_COURSE)
		{
			$courseid = $context->instanceid;
			$course = get_record('course', 'id', $courseid);
			$cat = get_record('course_categories','id',$course->category);
			$user = get_record('user', 'id', $role->userid);

			auth_plugin_joomdle::call_method ('removeJSGroupMember', $course->fullname, $user->username);
		}

		return true;
	}
?>
