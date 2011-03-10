<?php
/**
 * @version		
 * @package		Joomdle
 * @copyright	Copyright (C) 2008 - 2010 Antonio Duran Terres
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.user.helper');

define ('CJ_EMPTY_VALUE', -775577);


/**
 * Content Component Query Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class JoomdleHelperContent
{
	function _get_xmlrpc_url () {
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		switch ($params->get( 'moodle_version'))
		{
		
			case 20:
				//$moodle_xmlrpc_server_url = $params->get( 'MOODLE_URL' ).'/webservice/xmlrpc/server.php?wstoken=9f84c2bdd1aaa6dc179945b83b748e7a';
				$moodle_xmlrpc_server_url = $params->get( 'MOODLE_URL' ).'/webservice/xmlrpc/server.php?wstoken='.$params->get( 'auth_token');
				break;
			case 19:
				$moodle_xmlrpc_server_url = $params->get( 'MOODLE_URL' ).'/mnet/xmlrpc/server.php';
		}
		return $moodle_xmlrpc_server_url;
	}

	function _get_cm () {
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$connection_method = $params->get( 'connection_method' );
		return $connection_method;
	}

	function get_request ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
	{
		$comp_params = &JComponentHelper::getParams( 'com_joomdle' );
		switch ($comp_params->get( 'moodle_version'))
		{
		
			case 20:
				if ($params == CJ_EMPTY_VALUE)
					$request = xmlrpc_encode_request("joomdle_".$method, array (), array ('encoding' => 'utf8'));
				else if ($params2 == CJ_EMPTY_VALUE)
					$request = xmlrpc_encode_request("joomdle_".$method, array ($params), array ('encoding' => 'utf8'));
				else if ($params3 == CJ_EMPTY_VALUE)
					$request = xmlrpc_encode_request("joomdle_".$method, array ($params, $params2), array ('encoding' => 'utf8'));
				else if ($params4 == CJ_EMPTY_VALUE)
					$request = xmlrpc_encode_request("joomdle_".$method, array ($params, $params2, $params3), array ('encoding' => 'utf8'));
				else if ($params5 == CJ_EMPTY_VALUE)
					$request = xmlrpc_encode_request("joomdle_".$method, array ($params, $params2, $params3, $params4), array ('encoding' => 'utf8'));
				else
					$request = xmlrpc_encode_request("joomdle_".$method, array ($params, $params2, $params3, $params4, $params5), array ('encoding' => 'utf8'));
				break;
			case 19:
				$request = xmlrpc_encode_request("auth/joomdle/auth.php/$method", array ($params, $params2, $params3, $params4, $params5)); //, array ('encoding' => 'utf8'));
				break;
		}
		return $request;

	}

	function call_method_curl ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
	{
		$moodle_xmlrpc_server_url = JoomdleHelperContent::_get_xmlrpc_url ();

		//$request = xmlrpc_encode_request("auth/joomdle/auth.php/$method", array ($params, $params2, $params3, $params4, $params5)); //, array ('encoding' => 'utf8')); 
		$request =  JoomdleHelperContent::get_request ($method, $params, $params2, $params3, $params4, $params5);

		$headers = array();
		array_push($headers,"Content-Type: text/xml");
		array_push($headers,"Content-Length: ".strlen($request));
		array_push($headers,"\r\n");

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $moodle_xmlrpc_server_url); # URL to post to
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $request );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' ); # This POST is special, and uses its specified Content-type
		$response = curl_exec( $ch ); # run!
		curl_close($ch); 

		//XXX Dont know exactly why there is a double-encode problem if doing this this way, like we do in Moodle
		//$response = xmlrpc_decode ($response, 'utf8');
		$response = xmlrpc_decode ($response);

		if (is_array ($response))
			if (xmlrpc_is_fault ($response))
			{
				echo "XML-RPC Error (".$response['faultCode']."): ".$response['faultString'];
				die; // XXX Something softer?
			}

		return $response;
	}

	function call_method_fgc ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
	{
		$moodle_xmlrpc_server_url = JoomdleHelperContent::_get_xmlrpc_url ();

		//$request = xmlrpc_encode_request("joomdle_$method", array ($params, $params2, $params3, $params4, $params5)); //, array ('encoding' => 'utf8'));
		//$request = xmlrpc_encode_request("joomdle_$method", array ($params, $params2, $params3)); //, $params4, $params5)); //, array ('encoding' => 'utf8'));

		$request =  JoomdleHelperContent::get_request ($method, $params, $params2, $params3, $params4, $params5);

		$context = stream_context_create(array('http' => array(
					'method' => "POST",
					'header' => "Content-Type: text/xml",
					'content' => $request
						)));
		$file = file_get_contents($moodle_xmlrpc_server_url , false, $context);
	//	$response = xmlrpc_decode($file, 'utf8');
		$response = xmlrpc_decode($file);


		if (is_array ($response))
			if (xmlrpc_is_fault ($response))
			{
				echo "XML-RPC Error (".$response['faultCode']."): ".$response['faultString'];
				die; // XXX Something softer?
			}

		return $response;
	}


	function call_method ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
	{

		$cm = JoomdleHelperContent::_get_cm ();
	//	$params = &JComponentHelper::getParams( 'com_joomdle' ); XXX esto no se porque da error despues de 3 horas
	//	$cm = $params->get( 'connection_method' );

		if ($cm == 'fgc')
			$response = JoomdleHelperContent::call_method_fgc ($method, $params, $params2, $params3, $params4,  $params5);
		else //if ($cm == 'curl')
			$response = JoomdleHelperContent::call_method_curl ($method, $params, $params2, $params3, $params4,  $params5);

		return $response;
	}

	function call_method_debug ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE)
	{
		$cm = JoomdleHelperContent::_get_cm ();

		if ($cm == 'fgc')
			$response = JoomdleHelperContent::call_method_debug_fgc ($method, $params, $params2, $params3, $params4);
		else
			$response = JoomdleHelperContent::call_method_debug_curl ($method, $params, $params2, $params3, $params4);

		return $response;
	}

	function call_method_debug_fgc ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE)
	{
		$moodle_xmlrpc_server_url = JoomdleHelperContent::_get_xmlrpc_url ();

	//	$request = xmlrpc_encode_request("auth/joomdle/auth.php/$method", array ($params, $params2));

		$request =  JoomdleHelperContent::get_request ($method, $params, $params2);

		$context = stream_context_create(array('http' => array(
					'method' => "POST",
					'header' => "Content-Type: text/xml ",
					'content' => $request
						)));
		$file = file_get_contents($moodle_xmlrpc_server_url , false, $context);
		$response = xmlrpc_decode($file);

		return $response;
	}

	function call_method_debug_curl ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE)
	{
		$moodle_xmlrpc_server_url = JoomdleHelperContent::_get_xmlrpc_url ();

		//$request = xmlrpc_encode_request("auth/joomdle/auth.php/$method", array ($params, $params2));
		$request =  JoomdleHelperContent::get_request ($method, $params, $params2);
		$headers = array();
		array_push($headers,"Content-Type: text/xml");
		array_push($headers,"Content-Length: ".strlen($request));
		array_push($headers,"\r\n");

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $moodle_xmlrpc_server_url); # URL to post to
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $request );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' ); # This POST is special, and uses its specified Content-type
		$response = curl_exec( $ch ); # run!
		curl_close($ch); 

		$response = xmlrpc_decode ($response);

		if (is_array ($response))
			if (xmlrpc_is_fault ($response))
			{
				echo "XML-RPC Error (".$response['faultCode']."): ".$response['faultString'];
				die; // XXX Something softer?
			}

		return $response;
	}

	function get_file ($file)
    {
        $cm = JoomdleHelperContent::_get_cm ();

		if ($cm == 'fgc')
				$response = file_get_contents ($file, FALSE, NULL);
		else
				$response = JoomdleHelperContent::get_file_curl ($file);

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


	function getCourseEvents($id)
	{
		return JoomdleHelperContent::call_method ('get_upcoming_events', $id);
	}

	function getCourseInfo ($id)
	{
		return JoomdleHelperContent::call_method ('get_course_info', $id);
	}

	function getCourseCategories ($id = 0)
	{
		return JoomdleHelperContent::call_method ('get_course_categories', $id);
	}

	function getCourseCategory ($id)
	{
		return JoomdleHelperContent::call_method ('courses_by_category', $id, 0);
	}

	function getCourseNews ($id)
	{
		return JoomdleHelperContent::call_method ('get_news_items', $id);
	}

	function getCourseStudentsNo ($id)
	{
		return JoomdleHelperContent::call_method ('get_course_students_no', $id);
	}

	function getAssignmentSubmissions ($id)
	{
		return JoomdleHelperContent::call_method ('get_assignment_submissions', $id);
	}

	function getAssignmentGrades ($id)
	{
		return JoomdleHelperContent::call_method ('get_assignment_grades', $id);
	}

	function getCourseDailyStats ($id)
	{
		return JoomdleHelperContent::call_method ('get_course_daily_stats', $id);
	}

	function getCourseList ($enrollable_only = 0, $orderby = 'fullname ASC', $guest = 0)
	{
		return JoomdleHelperContent::call_method ('list_courses', (int) $enrollable_only, $orderby, (int) $guest);
	}

	function getStudentsNo ()
	{
		return JoomdleHelperContent::call_method ('get_student_no');
	}

	function getCoursesNo ()
	{
		return JoomdleHelperContent::call_method ('get_course_no');
	}

	function getEnrollableCoursesNo ()
	{
		return JoomdleHelperContent::call_method ('get_enrollable_course_no');
	}

	function getAssignmentsNo ()
	{
		return JoomdleHelperContent::call_method ('get_total_assignment_submissions');
	}

	function getLastWeekStats ()
	{
		return JoomdleHelperContent::call_method ('get_site_last_week_stats');
	}

	function getCourseTeachers ($id)
	{
		return JoomdleHelperContent::call_method ('get_course_editing_teachers', $id);
	}

	function getCourseContents ($id)
	{
		return JoomdleHelperContent::call_method ('get_course_contents', $id);
	}

	function enrolUser ($username, $id)
	{
		return JoomdleHelperContent::call_method ('enrol_user', $username, (int) $id);
	}

	function getMyCourses ($username = "")
	{
		if ($username)
			$user = & JFactory::getUser($username);
		else 
			$user = & JFactory::getUser();

		if (!$user)
			return array ();

		$id = $user->get('id');
		$username = $user->get('username');

		$cursos = JoomdleHelperContent::call_method ('my_courses', $username);

		return $cursos;
	}

	function getMyEvents ()
	{

		$user = & JFactory::getUser();
		$id = $user->get('id');
		$username = $user->get('username');

		$cursos = JoomdleHelperContent::call_method ('my_courses', $username);

		/* Para cada curso, obtenemos todos los eventos */
		$i = 0;
		foreach ($cursos as $id => $curso) {
			$id = $curso['id'];
			$course_events[$i]['events'] = JoomdleHelperContent::getCourseEvents ($id);
			$course_events[$i]['info'] = JoomdleHelperContent::getCourseInfo ($id);
			$i++;
		}

		return ($course_events);

	}

	function getMyNews ()
	{
		$user = & JFactory::getUser();
		$id = $user->get('id');
		$username = $user->get('username');

		$cursos = JoomdleHelperContent::call_method ('my_courses', $username);

		/* Para cada curso, obtenemos todas las noticias */
		$i = 0;
		foreach ($cursos as $id => $curso) {
			$id = $curso['id'];
			$course_news[$i]['news'] = JoomdleHelperContent::getCourseNews ($id);
			$course_news[$i]['info'] = JoomdleHelperContent::getCourseInfo ($id);
			$i++;
		}

		return ($course_news);

	}

	function getCourseGradeCategories ($id)
	{
		return JoomdleHelperContent::call_method ('get_course_grade_categories', $id);
	}

	/* Note: Moodle only users have negative ID */
	function getMoodleUsers ($limitstart = 0, $limit = 20, $order, $order_dir, $search = "")
	{
		$users = JoomdleHelperContent::call_method ('get_moodle_users', $limitstart, $limit, $order, $order_dir, $search);
		$i = 0;
		if (!is_array ($users))
			return array();

		foreach ($users as $user)
		{
			$users[$i]['id'] = -$users[$i]['id']; // Set negative. If is a Joomla user, next lines change its ID again
			$users[$i]['m_account'] = 1;
			$id = JUserHelper::getUserId($users[$i]['username']);
			if ($id)
			{
				$user_obj =& JFactory::getUser($id);
				if (!$user['admin'])
				{
					// If not moodle admin, check if joomla admin
					if (($user_obj->usertype == 'Administrator') || ($user_obj->usertype == 'Super Administrator'))
						$users[$i]['admin'] = 1;
					else $users[$i]['admin'] = 0;
				}

				$users[$i]['j_account'] = 1;
				$users[$i]['id'] = $id;
			}
			else
				$users[$i]['j_account'] = 0;


			$i++;
		}

		return $users;
	}

	function getMoodleUsersNumber ($search = "")
	{
		return  JoomdleHelperContent::call_method ('get_moodle_users_number', $search);
	}

	function getJoomlaUsers ($limitstart, $limit, $order, $order_dir, $search = "")
	{
		$db           =& JFactory::getDBO();


		$limit_c = "";
		if ($limit)
			$limit_c = " LIMIT $limitstart, $limit";

		if ($order != "")
			$order_c = " ORDER BY $order $order_dir";
		else $order_c = "";

		if ($search)
			$query = 'SELECT *' .
                                ' FROM #__users' .
                                ' WHERE (username LIKE '.$search.' OR email LIKE '.$search.' OR name LIKE '.$search.')'.
				$order_c.
				$limit_c;
		else
			$query = 'SELECT *' .
                                ' FROM #__users'.
				$order_c.
				$limit_c;
		$db->setQuery($query);
		$users = $db->loadObjectList();
		foreach ($users as $user)
		{
			$u[]['username'] = $user->username;
		}
		if (count ($users))
			$u = JoomdleHelperContent::call_method ('check_moodle_users', $u);
	//	print_r ($users);
		$rdo = array();
		$i = 0;
		foreach ($users as $user)
		{
			$rdo[$i] = get_object_vars ($user);
			
			if (($user->usertype == 'Administrator') || ($user->usertype == 'Super Administrator'))
				$rdo[$i]['admin'] = 1;
			else $rdo[$i]['admin'] = 0;

			$rdo[$i]['j_account'] = 1;
			$rdo[$i]['m_account'] = $u[$i]['m_account']; 
			if ( $rdo[$i]['m_account'] )
			{
				$rdo[$i]['auth'] = $u[$i]['auth']; 
				if (!$rdo[$i]['admin'])
					$rdo[$i]['admin'] = $u[$i]['admin'];
			}
			else
				 $rdo[$i]['auth'] = 'N/A';
			$i++;
		}
	//	print_r ($rdo);

		return ($rdo);
	}

	function getJoomlaUsersNumber ($search = "")
	{
		$db           =& JFactory::getDBO();
		if ($search)
			$query = 'SELECT count(id) as n' .
                                ' FROM #__users' .
                                ' WHERE (username LIKE '.$search.' OR email LIKE '.$search.' OR name LIKE '.$search.')'.
				" AND (usertype != 'Administrator' AND usertype != 'Super Administrator')";
		else
			$query = 'SELECT count(id) as n' .
                                ' FROM #__users'.
				" WHERE (usertype != 'Administrator' AND usertype != 'Super Administrator')";
		$db->setQuery($query);
		$number = $db->loadAssoc();

		return ($number['n']);
	}

function multisort($array, $order_dir, $sort_by, $key1, $key2=NULL, $key3=NULL, $key4=NULL, $key5=NULL, $key6=NULL, $key7=NULL, $key8=NULL){
    // sort by ?

if (!count ($array))
	return $array;

    foreach ($array as $pos =>  $val)
        $tmp_array[$pos] = $val[$sort_by];

if ($order_dir == 'desc')
    arsort($tmp_array);
else
    asort($tmp_array);
   
    // display however you want
    foreach ($tmp_array as $pos =>  $val){
        $return_array[$pos][$sort_by] = $array[$pos][$sort_by];
        $return_array[$pos][$key1] = $array[$pos][$key1];
        if (isset($key2)){
            $return_array[$pos][$key2] = $array[$pos][$key2];
            }
        if (isset($key3)){
            $return_array[$pos][$key3] = $array[$pos][$key3];
            }
        if (isset($key4)){
            $return_array[$pos][$key4] = $array[$pos][$key4];
            }
        if (isset($key5)){
            $return_array[$pos][$key5] = $array[$pos][$key5];
            }
        if (isset($key6)){
            $return_array[$pos][$key6] = $array[$pos][$key6];
            }
        if (isset($key7)){
            $return_array[$pos][$key7] = $array[$pos][$key7];
            }
        if (isset($key8)){
            $return_array[$pos][$key8] = $array[$pos][$key8];
            }
        }
    return $return_array;
    }


	/* Note: Moodle only users have negative ID */
	function getAllUsers ($limitstart, $limit, $order, $order_dir, $search)
	{

		$lang = &JFactory::getLanguage();
		$db           =& JFactory::getDBO();

		$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );

		if ($search)
			$query = 'SELECT *' .
                                ' FROM #__users' .
                                ' WHERE (username LIKE '.$searchEscaped.' OR email LIKE '.$searchEscaped.' OR name LIKE '.$searchEscaped.')';
	//			" AND (usertype != 'Administrator' AND usertype != 'Super Administrator')";
		else
			$query = 'SELECT *' .
                                ' FROM #__users';
		//		" WHERE (usertype != 'Administrator' AND usertype != 'Super Administrator')";
		$db->setQuery($query);
		$users = $db->loadObjectList();
		$u = array();
		foreach ($users as $user)
		{
			$u[]['username'] = $user->username;
		}
		$u_usernames = $u;
		$u = JoomdleHelperContent::call_method ('check_moodle_users', $u);
	//	print_r ($users);
		$rdo = array();
		$i = 0;
		foreach ($users as $user)
		{
			$rdo[$i] = get_object_vars ($user);
			$rdo[$i]['name_lower'] = strtolower($rdo[$i]['name']);;
			$rdo[$i]['username_lower'] = strtolower($rdo[$i]['username']);;
			$rdo[$i]['email_lower'] = strtolower($rdo[$i]['email']);;

			$rdo[$i]['j_account'] = 1;
			$rdo[$i]['m_account'] = $u[$i]['m_account']; 
			if ( $rdo[$i]['m_account'] )
				$rdo[$i]['auth'] = $u[$i]['auth']; 
			else
				 $rdo[$i]['auth'] = 'N/A';

			if (($user->usertype == 'Administrator') || ($user->usertype == 'Super Administrator'))
				$rdo[$i]['admin'] = 1;
			else $rdo[$i]['admin'] = 0;
			$i++;
		}

		$u = JoomdleHelperContent::call_method ('get_moodle_only_users', $u_usernames, $search);
		if (!is_array ($u))
			$u = array();
		$i = 0;
		foreach ($u as $user)
		{
			$u[$i]['name_lower'] = strtolower($u[$i]['name']);;
			$u[$i]['username_lower'] = strtolower($u[$i]['username']);;
			$u[$i]['email_lower'] = strtolower($u[$i]['email']);;
			$u[$i]['m_account'] = 1;
			$u[$i]['j_account'] = 0;
			$u[$i]['id'] = -$u[$i]['id'];
			$i++;
		}

		/* Kludge for uppercases */
		if ($order == 'name')
			$order = 'name_lower';
		if ($order == 'username')
			$order = 'username_lower';
		if ($order == 'email')
			$order = 'email_lower';
		
		$merged = array_merge ($rdo, $u);
		$all = JoomdleHelperContent::multisort ($merged, $order_dir, $order, 'id', 'name', 'username', 'email', 'm_account', 'j_account', 'auth', 'admin');
		if ($limit)
			return array_slice ($all, $limitstart, $limit);
		else
			return $all;
	}

	function getAllUsersNumber ($search)
	{
		$n = count (JoomdleHelperContent::getAllUsers (0, 100000, 'username', 'asc', $search));
		return $n;
	}

	function getJoomdleUsers ($limitstart, $limit, $order, $order_dir, $search)
	{
		$lang = &JFactory::getLanguage();
		$db           =& JFactory::getDBO();

		if ($order != "")
			$order_c = " ORDER BY $order $order_dir";
		else $order_c = "";

		if ($search)
			$query = 'SELECT *' .
                                ' FROM #__users' .
                                ' WHERE (username LIKE '.$search.' OR email LIKE '.$search.' OR name LIKE '.$search.')'.
				$order_c;
		else
			$query = 'SELECT *' .
                                ' FROM #__users'.
				$order_c;
		$db->setQuery($query);
		$users = $db->loadObjectList();
		$u = array();
		foreach ($users as $user)
		{
			$u[]['username'] = $user->username;
		}
		$u = JoomdleHelperContent::call_method ('check_moodle_users', $u);
	//	print_r ($users);
		$rdo = array();
		$i = 0;
		foreach ($users as $user)
		{
			if (($u[$i]['m_account'] == 0) || ($u[$i]['auth'] != 'joomdle'))
			{
				$i++;
				continue;
			}

			$rdo[$i] = get_object_vars ($user);
			if ($u[$i]['admin'] == 0)
			{
				if (($user->usertype == 'Administrator') || ($user->usertype == 'Super Administrator'))
					$rdo[$i]['admin'] = 1;
				else $rdo[$i]['admin'] = 0;
			}
			else $rdo[$i]['admin'] = 1;

			$rdo[$i]['j_account'] = 1;
			$rdo[$i]['m_account'] = $u[$i]['m_account']; 
			if ( $rdo[$i]['m_account'] )
				$rdo[$i]['auth'] = $u[$i]['auth']; 
			else
				 $rdo[$i]['auth'] = 'N/A';
			$i++;
		}


	//	$all = array_merge ($rdo, $u);
		if ($limit)
			return array_slice ($rdo, $limitstart, $limit);
		else
			return $rdo;
	}

	/* Note: Moodle only users have negative ID */
	function getNotJoomdleUsers ($limitstart, $limit, $order, $order_dir, $search)
	{
		$lang = &JFactory::getLanguage();
		$db           =& JFactory::getDBO();
		$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );

		if ($search)
			$query = 'SELECT *' .
                                ' FROM #__users' .
                                ' WHERE (username LIKE '.$searchEscaped.' OR email LIKE '.$searchEscaped.' OR name LIKE '.$searchEscaped.')';
		else
			$query = 'SELECT *' .
                                ' FROM #__users';
		$db->setQuery($query);
		$users = $db->loadObjectList();
		$u = array();
		foreach ($users as $user)
		{
			$u[]['username'] = $user->username;
		}
		$u_usernames = $u;
		$u = JoomdleHelperContent::call_method ('check_moodle_users', $u);
	//	print_r ($users);
		$rdo = array();
		$i = 0;
		foreach ($users as $user)
		{
			if (($u[$i]['m_account'] == 1) && ($u[$i]['auth'] == 'joomdle'))
			{
				$i++;
				continue;
			}

			$rdo[$i] = get_object_vars ($user);
			if (!$u[$i]['admin'])
			{
				if (($user->usertype == 'Administrator') || ($user->usertype == 'Super Administrator'))
					$rdo[$i]['admin'] = 1;
				else $rdo[$i]['admin'] = 0;
			}
			else $rdo[$i]['admin'] = 1;

			$rdo[$i]['j_account'] = 1;
			$rdo[$i]['m_account'] = $u[$i]['m_account']; 
			if ( $rdo[$i]['m_account'] )
				$rdo[$i]['auth'] = $u[$i]['auth']; 
			else
				 $rdo[$i]['auth'] = 'N/A';
			$i++;
		}

		$u = JoomdleHelperContent::call_method ('get_moodle_only_users', $u_usernames, $search);
		if (!is_array ($u))
			$u = array();
		$i = 0;
		foreach ($u as $user)
		{
			$u[$i]['m_account'] = 1;
			$u[$i]['j_account'] = 0;
			$u[$i]['id'] = -$u[$i]['id'];
			$i++;
		}

		$merged = array_merge ($rdo, $u);
		$all = JoomdleHelperContent::multisort ($merged, $order_dir, $order, 'id', 'name', 'username', 'email', 'm_account', 'j_account', 'auth', 'admin');
		if ($limit)
			return array_slice ($all, $limitstart, $limit);
		else
			return $all;
	}

	function add_moodle_users ($user_ids)
	{
		foreach ($user_ids as $id)
		{
			/* If user not already in Joomla, warn user and continue to next item */
			if ($id < 0)
			{
				JError::raiseWarning(500, JText::_( 'CJ USER ID DOES NOT EXIT IN JOOMLA' ) . ": " . $id);
				continue;
			}
			$user =& JFactory::getUser($id);
			/* If user already in Moodle, warn user and continue to next item */
			if (JoomdleHelperContent::call_method ('user_exists', $user->username))
			{
				JError::raiseWarning(500, JText::_( 'CJ USER ALREADY EXISTS IN MOODLE' ). ": ".$user->username );
				continue;
			}
			JoomdleHelperContent::call_method ('create_joomdle_user', $user->username);
		}
	}

	function migrate_users_to_joomdle ($user_ids)
	{
		foreach ($user_ids as $id)
		{
			/* If user not already in Joomla, warn user and continue to next item */
			if ($id < 0)
			{
				JError::raiseWarning(500, JText::_( 'CJ USER ID DOES NOT EXIT IN JOOMLA' ) . ": " . $id);
				continue;
			}
			$user =& JFactory::getUser($id);
			/* If user not already in Moodle, warn user and continue to next item */
			if (!JoomdleHelperContent::call_method ('user_exists', $user->username))
			{
				JError::raiseWarning(500, JText::_( 'CJ USER ID DOES NOT EXIT IN MOODLE' ). ": ".$user->username );
				continue;
			}
			JoomdleHelperContent::call_method ('migrate_to_joomdle', $user->username);
		}
	}

	function sync_moodle_profiles ($user_ids)
	{
		foreach ($user_ids as $id)
		{
			/* If user not already in Joomla, warn user and continue to next item */
			if ($id < 0)
			{
				JError::raiseWarning(500, JText::_( 'CJ USER ID DOES NOT EXIT IN JOOMLA' ) . ": " . $id);
				continue;
			}
			$user =& JFactory::getUser($id);
			/* If user not already in Moodle, warn user and continue to next item */
			if (!JoomdleHelperContent::call_method ('user_exists', $user->username))
			{
				JError::raiseWarning(500, JText::_( 'CJ USER ID DOES NOT EXIT IN MOODLE' ). ": ".$user->username );
				continue;
			}
			JoomdleHelperContent::call_method ('create_joomdle_user', $user->username);
		}
	}

	function create_joomla_users ($user_ids)
	{
		foreach ($user_ids as $id)
		{
			/* If user already in Joomla, warn user and continue to next item */
			if ($id >= 0)
			{
				JError::raiseWarning(500, JText::_( 'CJ USER ALREADY EXISTS IN JOOMLA' ). ": ".$id );
				continue;
			}
			/* Here we already now ID is from Moodle, as it is not from Joomla */
			$moodle_user = JoomdleHelperContent::call_method ('user_details_by_id', -$id); //We remove the minus
			if (!$moodle_user)
			{
				JError::raiseWarning(500, JText::_( 'CJ USER ID DOES NOT EXIT IN MOODLE' ). ": ".$id );
				continue;
			}
			$username = $moodle_user['username'];
			JoomdleHelperContent::create_joomla_user ($username);
		}
	}

	function create_joomla_user ($username)
	{
		 global $mainframe;


		$db           =& JFactory::getDBO();
                // Get required system objects
                $user           = clone(JFactory::getUser());
                $config         =& JFactory::getConfig();
                $authorize      =& JFactory::getACL();
                $document   =& JFactory::getDocument();

                $usersConfig    = &JComponentHelper::getParams( 'com_users' );
                $useractivation = $usersConfig->get( 'useractivation' );
		$url            = JURI::base();
		$pos =  strpos ($url, 'administrator/');
		$siteURL = substr ($url, 0, $pos);
                $sitename               = $mainframe->getCfg( 'sitename' );
		$mailfrom               = $mainframe->getCfg( 'mailfrom' );
		$fromname               = $mainframe->getCfg( 'fromname' );

		$newUsertype = 'Registered';

		$moodle_user['username'] = $username;
		$user_details =JoomdleHelperContent::call_method ('user_details', $username);


		$moodle_user['name'] = $user_details['firstname'] .' '.$user_details['lastname'];
		$moodle_user['email'] = $user_details['email'];

                $password = JUserHelper::genRandomPassword();
                $password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email

	//	$password = ''; //XXX FOR LDAP USERS

		$moodle_user['password'] = $password;
		$moodle_user['password2'] = $password;

                // Bind the post array to the user object
                if (!$user->bind( $moodle_user, 'usertype' )) {
                        JError::raiseError( 500, $user->getError());
                }

                // Set some initial user values
                $user->set('id', 0);
                $user->set('usertype', $newUsertype);
                $user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));

                $date =& JFactory::getDate();
                $user->set('registerDate', $date->toMySQL());

		jimport('joomla.user.helper');

		if ( $useractivation == 1 ){
			$user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
			$user->set('block', '1');
		}

		$user->set('lastvisitDate', '0000-00-00 00:00:00');

                // If there was an error with registration, set the message and display form
                if ( !$user->save() )
                {
                        JError::raiseWarning('', JText::_( $user->getError()));
                        return false;
                }
                // Send registration / confirmation mail

		$lang = &JFactory::getLanguage();
		$lang->load ('com_user', JPATH_SITE);

                $subject        = sprintf ( JText::_( 'Account details for' ), $user->name, $sitename);
                $subject        = html_entity_decode($subject, ENT_QUOTES);

		if ( $useractivation == 1 ){
                        $message = sprintf ( JText::_( 'SEND_MSG_ACTIVATE' ), $user->name, $sitename, $siteURL."index.php?option=com_user&task=activate&activation=".$user->get('activation'), $siteURL, $username, $password);
                } else {
                        $message = sprintf ( JText::_( 'SEND_MSG' ), $name, $sitename, $siteURL);
                }

                $message = html_entity_decode($message, ENT_QUOTES);

                //get all super administrator
                $query = 'SELECT name, email, sendEmail' .
                                ' FROM #__users' .
                                ' WHERE LOWER( usertype ) = "super administrator"';
                $db->setQuery( $query );
                $rows = $db->loadObjectList();

	//	return; //XXX FOR NOT SENDING MAIL FOR LDAP USERS

                // Send email to user
                if ( ! $mailfrom  || ! $fromname ) {
                        $fromname = $rows[0]->name;
                        $mailfrom = $rows[0]->email;
                }

                JUtility::sendMail($mailfrom, $fromname, $user->email, $subject, $message);
	}

	function getJumpURL ()
	{
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$moodle_auth_land_url = $params->get( 'MOODLE_URL' ).'/auth/joomdle/land.php';

		 $linkstarget = $params->get( 'linkstarget' );
		 if ($linkstarget == 'wrapper')
			 $use_wrapper = 1;
		 else $use_wrapper = 0;

		$user = & JFactory::getUser();
		$id = $user->get('id');
		$username = $user->get('username');


		$db           =& JFactory::getDBO();
		$query = 'SELECT session_id' .
			' FROM #__session' .
			' WHERE userid =';
		$query .= "'$id'";
		$db->setQuery($query);
		$sessions = $db->loadObjectList();

		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}

		if (count($sessions))
		foreach ($sessions as $session)
			$token = md5 ($session->session_id);

                $jump_url = $moodle_auth_land_url."?username=$username&token=$token&use_wrapper=$use_wrapper";

		return $jump_url;
	}

	function getMenuItem ()
	{
		$menu = &JSite::getMenu();
		$menuItem = &$menu->getActive();

		if (!$menuItem)
			return;

		$itemid = $menuItem->id;

		return $itemid;
	}

	function get_language_str ($lang)
        {
                require_once (dirname(__FILE__).DS.'languages.php');
                $l = explode ("_", $lang);
                $index = $l[0];

                return $LANGUAGES["$index"];
        }

	//XXX ---------------- QUITAR
	function getTiendaCourses ()
        {
                $cursos = JoomdleHelperContent::getCourseList (0);

                $c = array ();
                $i = 0;
		if (!is_array ($cursos))
			return $c;

                foreach ($cursos as $curso)
                {
                        $c[$i]->id = $curso['remoteid'];
                        $c[$i]->fullname = $curso['fullname'];
			$c[$i]->published = JoomdleHelperContent::is_course_on_sell_on_tienda ($curso['remoteid']);
                        $i++;
                }

                return $c;
        }

	function is_course_on_sell_on_tienda ($course_id)
	{
		$db           =& JFactory::getDBO();
		$query = 'SELECT product_sku' .
                                ' FROM #__tienda_products' .
                                ' WHERE product_sku =';
		$query .= $db->Quote ($course_id) . " and product_enabled='1'";
		$db->setQuery($query);
		$products = $db->loadObjectList();
		if (count ($products))
			return 1;
		else
			return 0;

	}


	//XXX ---------------- QUITAR
	function user_id_exists ($id)
	{
		$db           =& JFactory::getDBO();

		$id = $db->Quote ($id);
		$query = "SELECT id from #__users where id=$id";
		$db->setQuery($query);
		$users = $db->loadObjectList();

		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}

		return (count ($users) != 0);
	}

	function check_joomdle_system ()
	{
		$joomla_config = new JConfig();

		/* PHP XMLRPC extension enabled */
		$php_exts = get_loaded_extensions ();
		$xmlrpc_enabled = in_array ('xmlrpc', $php_exts);
		$system[2]['description'] = JText::_ ('CJ XMLRPC PHP EXTENSION');
		$system[2]['value'] = $xmlrpc_enabled;
		if ($system[2]['value'] == '0')
			$system[2]['error'] =  JText::_ ('CJ XMLRPC PHP EXTENSION ERROR');
		else $system[2]['error'] = '';


		/* Joomla Web services */

		$system[0]['description'] = JText::_ ('CJ JOOMLA WEB SERVICES');
		$system[0]['value'] = $joomla_config->xmlrpc_server;
		if ($joomla_config->xmlrpc_server == '0')
			$system[0]['error'] =  JText::_ ('CJ JOOMLA WEB SERVICES ERROR');
		else $system[0]['error'] = '';

		/* Mandatory Joomdle plugins enabled */

		$system[4]['description'] = JText::_ ('CJ XMLRPC PLUGIN');
		$system[4]['value'] = JPluginHelper::isEnabled ('xmlrpc', 'joomdle');
		if (JPluginHelper::isEnabled ('xmlrpc', 'joomdle') != '1')
			$system[4]['error'] =  JText::_ ('CJ XMLRPC PLUGIN ERROR');
		else $system[4]['error'] = '';

		$system[5]['description'] = JText::_ ('CJ JOOMDLEHOOKS PLUGIN');
		$system[5]['value'] = JPluginHelper::isEnabled ('user', 'joomdlehooks');
		if (JPluginHelper::isEnabled ('user', 'joomdlehooks') != '1')
			$system[5]['error'] =  JText::_ ('CJ JOOMDLEHOOKS PLUGIN ERROR');
		else $system[5]['error'] = '';

		$comp_params = &JComponentHelper::getParams( 'com_joomdle' );
		$connection = $comp_params->get( 'connection_method' );

		if ($connection == 'fgc')
		{
			/* file_get_contents function.  Test to see if allow_url_fopen PHP option is enabled */
			$system[1]['description'] = JText::_ ('CJ ALLOW URL FOPEN');
			$system[1]['value'] = ini_get ('allow_url_fopen');
			if ($system[1]['value'] != '1')
				$system[1]['error'] =  JText::_ ('CJ ALLOW URL FOPEN ERROR');
			else $system[1]['error'] = '';
		}
		else if ($connection == 'curl')
		{
			$system[1]['description'] = JText::_ ('CJ CURL ENABLED');
			$system[1]['value'] = function_exists('curl_version') == "Enabled";
			if (!$system[1]['value'])
				$system[1]['error'] =  JText::_ ('CJ CURL ENABLED ERROR');
			else $system[1]['error'] = '';
		}

		if ($system[1]['error'] != '')
		{
			/* If no working connection, no need to continue */
			return $system;
		}

		/* Test Moodle Web services in joomdle plugin */
		$system[3]['description'] = JText::_ ('CJ JOOMDLE WEB SERVICES');
		$response = JoomdleHelperContent::call_method_debug ('system_check');
	//	$response = JoomdleHelperContent::call_method ('system_check');
		if ($response == '')
		{
			$system[3]['value'] = 0;
			$system[3]['error'] =  JText::_ ('CJ EMPTY RESPONSE FROM MOODLE');
		}
		else if ((is_array ($response)) && (xmlrpc_is_fault ($response)))
		{
			$code = $response['faultCode']; //."): ".$response['faultString'];
			switch ($code)
			{
				case '702':
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('CJ JOOMDLE WEB SERVICES ERROR 702');
					break;
				case '704':
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('CJ JOOMDLE WEB SERVICES ERROR 704');
					break;
				case '7021':
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('CJ JOOMDLE WEB SERVICES ERROR 7021');
					break;
				case '7015':
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('CJ JOOMDLE WEB SERVICES ERROR 7015');
					break;
				case '0':
					$system[3]['value'] = 0;
					if (strstr ($response['faultString'], 'joomdle_auth'))
						$system[3]['error'] =  JText::_ ('CJ JOOMDLE AUTH NOT ENABLED');
					else if (strstr ($response['faultString'], 'mnet_auth'))
						$system[3]['error'] =  JText::_ ('CJ MNET AUTH NOT ENABLED');
					break;
				default:
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('CJ JOOMDLE WEB SERVICES UNEXPECTED ERROR'). ": ".$code .": ".$response['faultString'];

			}
		}
		else {
			if ($response ['joomdle_auth'] != 1)
			{
				$system[3]['value'] = 0;
				$system[3]['error'] =  JText::_ ('CJ JOOMDLE AUTH NOT ENABLED');
			}
			else if ($response ['mnet_auth'] != 1)
			{
				$system[3]['value'] = 0;
				$system[3]['error'] =  JText::_ ('CJ MNET AUTH NOT ENABLED');
			}
			else if ($response['joomdle_configured'] == 0)
			{
				$system[3]['value'] = 0;
				$system[3]['error'] =  JText::_ ('CJ JOOMLA URL NOT CONFIGURED IN MOODLE PLUGIN');
			}
			else if ($response['test_data'] != 'It works')
			{
				if ($response['test_data']['faultString'] == 'IP not allowed')
				{
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('CJ IP NOT ALLOWED');
				}
				else {
					$system[3]['value'] = 0;
					$system[3]['error'] =  JText::_ ('CJ JOOMLA URL MISCONFIGURED IN MOODLE PLUGIN');
				}
			}
			else {
				$system[3]['value'] = 1;
				$system[3]['error'] = '';
			}
		}


		return $system;

	}
}
