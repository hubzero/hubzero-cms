<?php
/**
 * @version             
 * @package             Joomdle
 * @copyright   Copyright (C) 2008 - 2010 Antonio Duran Terres
 * @license             GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(JPATH_SITE.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'profiletypes.php');

class plgUserJoomdlehooks extends JPlugin
{
	function plgUserJoomdlehooks(& $subject, $config) {
                parent::__construct($subject, $config);
        }

	/* Destroys Moodle session */
	function onLogoutUser($user, $options = array())
	{
		global $mainframe;
			
	//	setcookie(JUtility::getHash('JLOGIN_REMEMBER'), '',  time() - 3600, '/','','',0);
		if (array_key_exists ('skip_joomdlehooks', $options))
			return true;

		if ($mainframe->isAdmin()) 
			return true;

		setcookie(JUtility::getHash('JLOGIN_REMEMBER'), '',  time() - 86400, '/','','',0);
		/* NEW */
		$comp_params = &JComponentHelper::getParams( 'com_joomdle' );
		$moodle_url = $comp_params->get( 'MOODLE_URL' );
		$app = & JFactory::getApplication();
		$app->redirect($moodle_url."/auth/joomdle/land_logout.php" ); 
		return;
		/* NEW */

		
		// XXX discard
		if (!array_key_exists ('MoodleSession', $_COOKIE))
			return;

		$old_session = session_id ();
		session_name ("MoodleSession");
		session_id("");
		session_destroy();
		session_unregister("USER");
		session_unregister("SESSION");
		setcookie('MoodleSession', '',  time() - 3600, '/','','',0);
		unset($_SESSION);

	}

	/* Creates Moodle session */
	function onLoginUser($user, $options = array())
	{
		global $mainframe;
			
		if (array_key_exists ('skip_joomdlehooks', $options))
			return;

		if ($mainframe->isAdmin()) 
			return;


		$comp_params = &JComponentHelper::getParams( 'com_joomdle' );

		$moodle_url = $comp_params->get( 'MOODLE_URL' );
	//	$create_user = $comp_params->get( 'auto_create_users' ); not used here

		$session                =& JFactory::getSession();
		$token = md5 ($session->getId());

		$username = $user['username'];

		/* Don't log in Moodle if user is blocked */
		$user_id = JUserHelper::getUserId($username);
		$user_obj =& JFactory::getUser($user_id);
		if  ($user_obj->block)
			return;

		$app = & JFactory::getApplication();


		if (JRequest::getVar ('return'))
		{
			$return = JRequest::getVar ('return');
            if (!strncmp ($return, 'B:', 2))
            {
                /* CB login module */
                $login_url = urlencode (base64_decode (substr ($return, 2)));
            }
            else
            {
                /* Normal login */
                $login_url = urlencode (base64_decode (JRequest::getVar ('return')));
            }
		}
		else if (array_key_exists ('url', $options))
			$login_url = urlencode ($options['url']);
		else
			$login_url = urlencode (JRequest::getUri ());
	//	echo $login_url;
	//	exit ();

		// Set the remember me cookie if enabled
		// as we are redirecting and this would not be executed by Joomla
		if (isset($options['remember']) && $options['remember'])
		{
			jimport('joomla.utilities.simplecrypt');
			jimport('joomla.utilities.utility');

			//Create the encryption key, apply extra hardening using the user agent string
			$key = JUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);

			$credentials = array ($user->username, $user->password);
			$user_obj = & JFactory::getUser();
			$credentials = array ('username'=>$username, 'password'=>$user['password']);

			$crypt = new JSimpleCrypt($key);
			$rcookie = $crypt->encrypt(serialize($credentials));
			$lifetime = time() + 365*24*60*60;
			setcookie( JUtility::getHash('JLOGIN_REMEMBER'), $rcookie, $lifetime, '/' );
		}
		// Metodo normal usando redirect
	//	echo $moodle_url."/auth/joomdle/land.php?username=$username&token=$token&use_wrapper=0&create_user=0&wantsurl=$login_url";
	//	return;
	//	exit ();
		$app->redirect($moodle_url."/auth/joomdle/land.php?username=$username&token=$token&use_wrapper=0&create_user=0&wantsurl=$login_url" ); 
		// Metodo nuevo con cURL
	//	plgUserJoomdlehooks::log_into_moodle ($username, $token);
	}

	/* Logs the user into Moodle using cURL to set the cookies */
	function log_into_moodle ($username, $token)
	{
		$comp_params = &JComponentHelper::getParams( 'com_joomdle' );

		$moodle_url = $comp_params->get( 'MOODLE_URL' );

                $login_url = '';
                $file = $moodle_url. "/moodle/auth/joomdle/land.php?username=$username&token=$token&use_wrapper=0&create_user=0&wantsurl=$login_url";

                $ch = curl_init();
                // set url
                curl_setopt($ch, CURLOPT_URL, $file);

                //return the transfer as a string
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
                curl_setopt($ch, CURLOPT_HEADER, 1);

                $output = curl_exec($ch);
		curl_close($ch);

                $cr = curl_init($moodle_url);
                curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($cr, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
                $output = curl_exec($cr);
                curl_close($cr);
                $f = fopen ('/tmp/cookies.txt', 'ro');
                while (!feof ($f))
                {
                        $line = fgets ($f);
                        if (($line == '\n') || ($line[0] == '#'))
                                continue;
                        $parts = explode ("\t", $line);
                        if (array_key_exists (5, $parts))
                        {
                                $name = $parts[5];
                                $value = trim ($parts[6]);
        //                      if ($parts[5] == 'MOODLEID_')
                                        setcookie ($name, $value, 1286634223, '/');
        //                      else
        //                              setcookie ($parts[5], $parts[6], 0, '/');
        //              echo $parts[5]." ".$parts[6];
                        }
                }
	}

	/* Creates Moodle user */
	function onAfterStoreUser ($user, $isnew, $success, $msg)
	{
		global $mainframe;

		$last_visit = $user['lastvisitDate'];


		/// XXX Test this!!!
		if ($last_visit == 0)
			$isnew = 1;
		else $isnew = 0;

		$comp_params = &JComponentHelper::getParams( 'com_joomdle' );

		/* Don't create user if not configured to do so */
		if (($isnew) && (!$comp_params->get( 'auto_create_users' ))) //XXX comprobar si va el isnew o hacelo de otra forma
			return;

		//XXX probar bien el isnew con y sin JS y simplicar esto, pa no ejecutar el get moodle user por ej

		$username = $user['username'];
		$str =  $user['name'];
		$moodle_user = JoomdleHelperContent::call_method ("user_id", $username);

		/* If user don't exist, and it is configured to not autocreate return  */
                if ((!$moodle_user) && (!$comp_params->get( 'auto_create_users' )))
                        return;

		$use_xipt_integration = $comp_params->get( 'use_xipt_integration' );
		if ((!$moodle_user) && ($use_xipt_integration))
		{
			 /* Only create Moodle user if Profile Type in selected ones */
			$db = &JFactory::getDBO();
			$query = "select id from #__community_fields where fieldcode = 'XIPT_PROFILETYPE'";
			$db->setQuery($query);
			$field_id = $db->loadResult();
			$field = 'field'.$field_id;

			/* If editing anyhting else in the profile, not intereseting */
			if (!array_key_exists ($field, $_POST))
				return;

			$profile_type = $_POST[$field];


			$profile_type_ids = JoomdleHelperProfiletypes::get_profiletypes_to_create ();
			$profile_ok = in_array ($profile_type, $profile_type_ids);
			if ((!$profile_ok) &&  (!$moodle_user) )
				return;

		}

		$reply = JoomdleHelperContent::call_method ("create_joomdle_user", $username);

		/* Auto login user */
		if ($comp_params->get( 'auto_login_users' ))
		{
			$credentials = array ( 'username' => $user['username'], 'password' => $user['password_clear']);
			$options = array ();
		//	$link =  JRequest::getVar( 'link' );
		//	$options = array ( 'url' => $link);

			$mainframe->login( $credentials, $options );
		}

	}

	function onAfterDeleteUser ($user, $options = array())
	{
		global $mainframe;

		$comp_params = &JComponentHelper::getParams( 'com_joomdle' );

		/* Don't delete user if not configured to do so */
		if (!$comp_params->get( 'auto_delete_users' ))
			return;

		$otherlanguage =& JFactory::getLanguage();
		$otherlanguage->load( 'com_joomdle', JPATH_SITE );

		$username = $user['username'];

		$reply = JoomdleHelperContent::call_method ("delete_user", $username);

		if ($reply)
			 $mainframe->enqueueMessage(JText::_('CJ USER DELETED FROM MOODLE'));
	}
}

?>
