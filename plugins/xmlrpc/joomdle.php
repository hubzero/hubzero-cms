<?php
/**
 * @version		
 * @package		Joomdle
 * @copyright	Copyright (C) 2008 - 2010 Antonio Duran Terres
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
//defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport('joomla.application.component.helper');
jimport('joomla.user.helper');

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'mappings.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'groups.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'users.php');

class plgXMLRPCJoomdle extends JPlugin
{
	function plgXMLRPCJoomdle(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage( '', JPATH_ADMINISTRATOR );
	}

	/**
	* @return array An array of associative arrays defining the available methods
	*/
	function onGetWebServices()
	{
		global $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		return array
		(
				'joomdle.confirmJoomlaSession' => array(
				'function' => 'plgXMLRPCJoomdleServices::confirmJoomlaSession',
				'docstring' => JText::_('Confirms that a user is logged in Joomla'),
				'signature' => array(array ($xmlrpcBoolean,$xmlrpcString, $xmlrpcString))
			),
				'joomdle.getUserInfo' => array(
				'function' => 'plgXMLRPCJoomdleServices::getUserInfo',
				'docstring' => JText::_('Returns user information'),
				'signature' => array(array ($xmlrpcArray,$xmlrpcString))
			),
				'joomdle.logout' => array(
				'function' => 'plgXMLRPCJoomdleServices::logout',
				'docstring' => JText::_('Logs the user out'),
				'signature' => array(array ($xmlrpcArray,$xmlrpcString))
			),
				'joomdle.updateSessions' => array(
				'function' => 'plgXMLRPCJoomdleServices::updateSessions',
				'docstring' => JText::_('Updates users sessions'),
				'signature' => array(array ($xmlrpcArray,$xmlrpcArray))
			),
				'joomdle.test' => array(
				'function' => 'plgXMLRPCJoomdleServices::test',
				'docstring' => JText::_('Test web services'),
				'signature' => array(array ($xmlrpcString))
			),
				'joomdle.login' => array(
				'function' => 'plgXMLRPCJoomdleServices::login',
				'docstring' => JText::_('Login from Moodle'),
				'signature' => array(array ($xmlrpcBoolean,$xmlrpcString, $xmlrpcString))
			),
				'joomdle.addActivityCourse' => array(
				'function' => 'plgXMLRPCJoomdleServices::addActivityCourse',
				'docstring' => JText::_('Add Jomsocial activities'),
				'signature' => array(array ($xmlrpcBoolean,$xmlrpcInt, $xmlrpcString, $xmlrpcString, $xmlrpcInt, $xmlrpcString))
			),
				'joomdle.addActivityCourseEnrolment' => array(
				'function' => 'plgXMLRPCJoomdleServices::addActivityCourseEnrolment',
				'docstring' => JText::_('Add Jomsocial activities'),
				'signature' => array(array ($xmlrpcBoolean, $xmlrpcString, $xmlrpcInt, $xmlrpcString, $xmlrpcInt, $xmlrpcString))
			),
				'joomdle.getJSGroupId' => array(
				'function' => 'plgXMLRPCJoomdleServices::getJSGroupId',
				'docstring' => JText::_('Get Jomsocial group ID'),
				'signature' => array(array ($xmlrpcBoolean, $xmlrpcString))
			),
				'joomdle.getJSGroupImageLink' => array(
				'function' => 'plgXMLRPCJoomdleServices::getJSGroupImageLink',
				'docstring' => JText::_('Get Jomsocial group image link'),
				'signature' => array(array ($xmlrpcArray, $xmlrpcString))
			),
				'joomdle.addJSGroup' => array(
				'function' => 'plgXMLRPCJoomdleServices::addJSGroup',
				'docstring' => JText::_('Add Jomsocial groups'),
				'signature' => array(array ($xmlrpcBoolean, $xmlrpcString, $xmlrpcString, $xmlrpcInt, $xmlrpcString))
			),
				'joomdle.removeJSGroup' => array(
				'function' => 'plgXMLRPCJoomdleServices::removeJSGroup',
				'docstring' => JText::_('Remove Jomsocial groups'),
				'signature' => array(array ($xmlrpcBoolean, $xmlrpcString))
			),
				'joomdle.addJSGroupMember' => array(
				'function' => 'plgXMLRPCJoomdleServices::addJSGroupMember',
				'docstring' => JText::_('Add Jomsocial group member'),
				'signature' => array(array ($xmlrpcBoolean, $xmlrpcString, $xmlrpcString, $xmlrpcInt))
			),
				'joomdle.removeJSGroupMember' => array(
				'function' => 'plgXMLRPCJoomdleServices::removeJSGroupMember',
				'docstring' => JText::_('Remove Jomsocial group member'),
				'signature' => array(array ($xmlrpcBoolean, $xmlrpcString, $xmlrpcString))
			),
				'joomdle.getDefaultItemid' => array(
				'function' => 'plgXMLRPCJoomdleServices::getDefaultItemid',
				'docstring' => JText::_('Gets default itemid for moodle links'),
				'signature' => array(array ($xmlrpcInt))
			),
				'joomdle.createUser' => array(
				'function' => 'plgXMLRPCJoomdleServices::createUser',
				'docstring' => JText::_('Create new Joomla user'),
				'signature' => array(array ($xmlrpcStruct, $xmlrpcStruct ))
			),
				'joomdle.activateUser' => array(
				'function' => 'plgXMLRPCJoomdleServices::activateUser',
				'docstring' => JText::_('Activates a new Joomla user'),
				'signature' => array(array ($xmlrpcBoolean, $xmlrpcString ))
			),
				'joomdle.updateUser' => array(
				'function' => 'plgXMLRPCJoomdleServices::updateUser',
				'docstring' => JText::_('Update Joomla user profile'),
				'signature' => array(array ($xmlrpcStruct, $xmlrpcStruct ))
			),
				'joomdle.changePassword' => array(
				'function' => 'plgXMLRPCJoomdleServices::changePassword',
				'docstring' => JText::_('Update Joomla user password'),
				'signature' => array(array ($xmlrpcBoolean, $xmlrpcString, $xmlrpcString))
			),
				'joomdle.deleteUser' => array(
				'function' => 'plgXMLRPCJoomdleServices::deleteUser',
				'docstring' => JText::_('Deletes Joomla user'),
				'signature' => array(array ($xmlrpcBoolean, $xmlrpcString))
			)
		);
	}
}

class plgXMLRPCJoomdleServices
{

	function check_origin ()
	{
		$request_ip = JRequest::getVar ('REMOTE_ADDR', '', 'server');
		$comp_params = &JComponentHelper::getParams( 'com_joomdle' );
		$moodle_url = $comp_params->get( 'MOODLE_URL' );
		$url = parse_url ($moodle_url);
		$domain = $url['host'];
		$moodle_ip = gethostbyname ($domain);

		return  ($request_ip == $moodle_ip);
	}

        /**
         * Remote session check
         *
         * @param       string  Username
         * @param       string  Session ID
         * @return      boolean   Wheter the user can log in Results
         * @since       1.5
         */

	function confirmJoomlaSession($username, $token)
	{
		global $mainframe, $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access denied"));

		$db = &JFactory::getDBO();
		$query = 'SELECT session_id' .
				' FROM #__session' .
				" WHERE username = ". $db->Quote($username). " and  md5(session_id) = ". $db->Quote($token);
		$db->setQuery( $query );
		$sessions = $db->loadObjectList();

		if (count ($sessions))
			$r = true;
		else $r = false;

		return new xmlrpcresp(new xmlrpcval( $r, $xmlrpcBoolean));
	}

	/* XXX put more langs */
	function get_moodle_lang ($lang)
	{
		if (!$lang)
			return '';

		switch ($lang)
		{
			case 'en-GB':
				return 'en_utf8';
			case 'es-ES':
				return 'es_utf8';
			default:
				return '';
		}
	}

	function get_moodle_country ($country)
	{
		include_once( dirname(__FILE__).DS.'joomdle'.DS.'countries.php');

		if ($country == 'selectcountry')
			return '';
		return $countries[$country];
	}

	function get_tienda_country ($country_id)
	{
		$db = &JFactory::getDBO();
		$query = 'SELECT *' .
				' FROM #__tienda_countries' .
				" WHERE country_id = " . $db->Quote($country_id);
		$db->setQuery( $query );
		$country = $db->loadAssoc();

		return $country['country_isocode_2'];
	}

	function get_firstname ($name)
	{
		$parts = explode (' ', $name);

		return  $parts[0];
	}

	function get_lastname ($name)
	{
		$parts = explode (' ', $name);

		$lastname = '';
		$n = count ($parts);
		for ($i = 1; $i < $n; $i++)
		{
			if ($i != 1)
				$lastname .= ' ';
			$lastname .= $parts[$i];
		}

		return $lastname;
	}

	function getUserInfo($username)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access Denied"));

		$user_info = JoomdleHelperMappings::get_user_info ($username);
		return $user_info;
	}

	function logout($username)
	{
		global $mainframe, $xmlrpcString;

		if (!plgXMLRPCJoomdleServices::check_origin ())
			return;

		$id = JUserHelper::getUserId($username);

//		return array ($username);
		//$error = $mainframe->logout($id, array ( 'clientid' => array (0)));
		$error = $mainframe->logout($id, array ( 'clientid' => array (0), 'skip_joomdlehooks' => 1));
		//return array ($error);

		//XXX el stcookie aki no pinta na!!! que stamos en servio web
	//	setcookie(JUtility::getHash('JLOGIN_REMEMBER'), '',  time() - 3600, '/','','',0);

		$r = JUtility::getHash('JLOGIN_REMEMBER');
		return array ($r);
	//	return  new xmlrpcresp(new xmlrpcval( $r, $xmlrpcString));
	}

	/* Updates Joomla sessions */
	function updateSessions ($usernames)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return;

		$db = &JFactory::getDBO();

		$time = time ();
		$store = 'database';
		$options = array();
		$session_storage  =& JSessionStorage::getInstance($store, $options);

		foreach ($usernames as $username)
		{
			$query = 'SELECT *' .
					' FROM #__session' .
					" WHERE username = " . $db->Quote($username).
					" ORDER BY time DESC LIMIT 1";
			$db->setQuery( $query );
			$session = $db->loadAssoc();
			$session_id = $session['session_id'];

			$data = $session_storage->read ($session_id);
			session_decode($data);
			$_SESSION['__default']['session.timer.last'] =  $_SESSION['__default']['session.timer.now']; 
			$_SESSION['__default']['session.timer.now'] = time ();
			$data = session_encode ();
			$data = $session_storage->write ($session_id, $data);

			$query = 'UPDATE  #__session' .
				" SET time = " . $db->Quote($time) .
				" WHERE username = " . $db->Quote($username) .
				" ORDER BY time DESC LIMIT 1";
			$db->setQuery( $query );
			if (!$db->query()) {
				return JError::raiseWarning( 500, $db->getError() );
			}
                }

		return $usernames;
	}

	/* Testing function */
	function test ()
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access Denied"));

		return "It works";
	}

	/* Web service used to log in from Moodle */
	//XXX mover code a moodle para no enviar la pass en claro
	function login ($username, $password)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access Denied"));

		$mainframe =& JFactory::getApplication('site');

		// NEW LDAP
		$options = array ( 'skip_joomdlehooks' => '1');
		$credentials = array ( 'username' => $username, 'password' => $password);
		if ($mainframe->login( $credentials, $options ))
			return true;
		return false;
		// NEW LDAP

		$db =& JFactory::getDBO();

                $query = 'SELECT `id`, `password`, `gid`'
                        . ' FROM `#__users`'
                        . ' WHERE username=' . $db->Quote( $username )
                        ;
                $db->setQuery( $query );
                $result = $db->loadObject();


                if($result)
                {
                        $parts  = explode( ':', $result->password );
                        $crypt  = $parts[0];
                        $salt   = @$parts[1];
                        $testcrypt = JUserHelper::getCryptedPassword($password, $salt);

                        if ($crypt == $testcrypt) {
				return true;
                        } else {
				return false;
                        }
                }
                else
                {
			return false;
                }

		return false;
	}

	function addActivityCourse ($id, $name, $desc, $cat_id, $cat_name)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access Denied"));

		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
		require_once( JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'methods.php' );

		CFactory::load ( 'libraries', 'activities' );

		$cat_slug = JFilterOutput::stringURLSafe ($cat_name);
		$course_slug = JFilterOutput::stringURLSafe ($name);

	//	$cat_name = utf8_encode ($cat_name);
		$mainframe =& JFactory::getApplication('site');
		$mainframe->initialise();

		/* Kludge para que no pete el call_method */
		if ($desc == ' ')
			$desc = '';

		/* It seems JS re-encodes this or something ... */
		$name = utf8_decode ($name);
		$desc = utf8_decode ($desc);
		$cat_name = utf8_decode ($cat_name);

		$act = new stdClass();

	//	$message                = JText::_(' SO1áĞ間 New course available').' '.utf8_encode ('SO1áĞ間');
		$message                = JText::_('New course available').' ';

		$link = ("index.php?option=com_joomdle&view=detail&cat_id=$cat_id:$cat_slug&course_id=$id:$course_slug");
		$message                .= ' <a href="' . $link .'">' . $name . '</a> ';

		$cat_link = ("index.php?option=com_joomdle&view=coursecategory&cat_id=$cat_id:$cat_slug");
		$message                .= JText::_('CJ IN CATEGORY')." ";
		$message                .= ' <a href="' . $cat_link .'">' . $cat_name . '</a> ';

		$act->cmd               = 'joomdle.create';
		$act->actor     = 0; 
		$act->access     = 0;
		$act->target    = 0;
		$act->title             = JText::_( $message );
		$act->content   = $desc; 
		$act->app               = 'joomdle';
		$act->cid               = 0;
		CActivityStream::add( $act );

		return "OK";

	}

	function addActivityCourseEnrolment ($username, $course_id, $course_name, $cat_id, $cat_name)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access Denied"));

		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
		require_once( JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'methods.php' );

		CFactory::load ( 'libraries', 'activities' );

		$course_slug = JFilterOutput::stringURLSafe ($course_name);

		$cat_slug = JFilterOutput::stringURLSafe ($cat_name);

		$user =& JFactory::getUser($username);
		$user_id = $user->id;

		$mainframe =& JFactory::getApplication('site');
		$mainframe->initialise();

		$act = new stdClass();

		$course_name = utf8_decode ($course_name);
		$cat_name = utf8_decode ($cat_name);
		
		$message                = JText::_('{actor} enroled into the course').' ';

		$link = ("index.php?option=com_joomdle&view=detail&cat_id=$cat_id:$cat_slug&course_id=$course_id:$course_slug");
		$message                .= ' <a href="' . $link .'">' . $course_name . '</a> ';

		$act->cmd               = 'joomdle.enrolment';
		$act->actor     = $user_id; 
		$act->access     = 0;
		$act->target    = 0;
		$act->title             = JText::_( $message );
		$act->content   = ''; 
		$act->app               = 'joomdle';
		$act->cid = 0;
		CActivityStream::add( $act );

		return "OK";

	}


	function getJSGroupId ($name)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access Denied"));

		return JoomdleHelperGroups::get_js_group_by_name ($name);
	}

	function getJSGroupImageLink ($name)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access Denied"));

		return JoomdleHelperGroups::get_js_group_image_link ($name);
	}

	function addJSGroup ($name, $description, $categoryId, $website)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access Denied"));

		$name = utf8_decode ($name);
		$description = utf8_decode ($description);

		return JoomdleHelperGroups::addJSGroup ($name, $description, $categoryId, $website);
	}

	function removeJSGroup ($name)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access Denied"));

		return JoomdleHelperGroups::removeJSGroup ($name);
	}


	function addJSGroupMember ($group_name, $username, $permissions)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access Denied"));

		return JoomdleHelperGroups::addJSGroupMember ($group_name, $username, $permissions);
	}

	function removeJSGroupMember ($group_name, $username)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access Denied"));

		return JoomdleHelperGroups::removeJSGroupMember ($group_name, $username);
	}

	/* 
	   This should be changed soon so there is no need to call this WS.
	   Left this way until we decide if this is the right approach
	   */
	function getDefaultItemid ()
	{
		$comp_params = &JComponentHelper::getParams( 'com_joomdle' );
		$default_itemid = $comp_params->get( 'default_itemid' );
		return $default_itemid;
	}

	function createUser ($user_info)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access denied"));

		return JoomdleHelperUsers::create_joomla_user ($user_info);
	}

	function activateUser ($username)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access denied"));

		return JoomdleHelperUsers::activate_joomla_user ($username);
	}

	function updateUser ($user_info)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access denied"));

		return JoomdleHelperMappings::save_user_info ($user_info);
	}

	function changePassword ($username, $password)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access denied"));

		$user =& JFactory::getUser($username);

		$salt           = JUserHelper::genRandomPassword(32);
                $crypt          = JUserHelper::getCryptedPassword($password, $salt);
		$password_crypt       = $crypt.':'.$salt;

		$user->password = $password_crypt;
		@$user->save();

		return true;
	}

	function deleteUser ($username)
	{
		if (!plgXMLRPCJoomdleServices::check_origin ())
			return new xmlrpcresp(0, 1, JText::_("Access denied"));

		$user =& JFactory::getUser($username);
		$user->delete();
	}

}
