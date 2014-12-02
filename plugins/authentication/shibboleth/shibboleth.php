<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Steven Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Authentication Plugin class for Shibboleth/InCommon
 */
class plgAuthenticationShibboleth extends JPlugin
{
	const DNS = '8.8.8.8'; // nameserver used to look up user's network to see if we can automatically match them with their id provider

	private function log($msg, $data = '')
	{
		static $fh = NULL;
		if ($fh === NULL) {
			$fh = fopen($this->params->get('debug_location', '/var/log/apache2/php/shibboleth.log'), 'a+');
		}
		if (!isset($_COOKIE['shib-dbg-token'])) {
			$token = base64_encode(uniqid());
			setcookie('shib-dbg-token', $token, time()+60*60*24);
		}
		else {
			$token =$_COOKIE['shib-dbg-token'];
		}
		if (@fwrite($fh, "$token - $msg")) {
			if ($data !== '') {
				@fwrite($fh, ":\t".(is_string($data) ? $data : json_encode($data))."\n");
			}
			else {
				@fwrite($fh, "\n");
			}
		}
	}

	/**
	 * Thanks, helpful PHP.net commenter. I don't want to use gethostbyaddr()
	 * directly and potentially DOS the login page because the DNS server is
	 * being a jerk.
	 *
	 * I want the hostname to see if I can automatically match people up with
	 * their institutions when they're on its network.
	 */
	private static function gethostbyaddr_timeout($ip, $dns, $timeout = 2)
	{
		$ip = is_array($ip) ? $ip[0] : $ip;
		// random transaction number (for routers etc to get the reply back)
		$data = rand(0, 99);
		// trim it to 2 bytes
		$data = substr($data, 0, 2);
		// request header
		$data .= "\1\0\0\1\0\0\0\0\0\0";
		// split IP up
		$bits = explode(".", $ip);
		// error checking
		if (count($bits) != 4)
		{
			return "ERROR";
		}
		// there is probably a better way to do this bit...
		// loop through each segment
		for ($x = 3; $x >= 0; $x--)
		{
			// needs a byte to indicate the length of each segment of the request
			switch (strlen($bits[$x]))
			{
				case 1: $data .= "\1"; break;
				case 2: $data .= "\2"; break;
				case 3: $data .= "\3"; break;
				default: return NULL;
			}
			// and the segment itself
			$data .= $bits[$x];
		}
		// and the final bit of the request
		$data .= "\7in-addr\4arpa\0\0\x0C\0\1";
		// create UDP socket
		$handle = @fsockopen("udp://$dns", 53);
		// send our request (and store request size so we can cheat later)
		$requestsize=@fwrite($handle, $data);

		@socket_set_timeout($handle, $timeout);
		// hope we get a reply
		$response = @fread($handle, 1000);
		@fclose($handle);
		if ($response == "")
		{
			return $ip;
		}
		// find the response type
		$type = @unpack("s", substr($response, $requestsize+2));
		if ($type[1] == 0x0C00)
		{
			// set up our variables
			$host="";
			$len = 0;
			// set our pointer at the beginning of the hostname
			// uses the request size from earlier rather than work it out
			$position=$requestsize+12;
			// reconstruct hostname
			do
			{
				// get segment size
				$len = unpack("c", substr($response, $position));
				// null terminated string, so length 0 = finished
				if ($len[1] == 0)
				{
					// return the hostname, without the trailing .
					return substr($host, 0, strlen($host) -1);
				}
				// add segment to our host
				$host .= substr($response, $position+1, $len[1]) . ".";
				// move pointer on to the next segment
				$position += $len[1] + 1;
			}
			while ($len != 0);
		}
		return $ip;
	}

	private static function getInstitutions()
	{
		static $inst = NULL;
		if ($inst === NULL)
		{
			$plugin = JPluginHelper::getPlugin('authentication', 'shibboleth');
			$inst = json_decode(json_decode($plugin->params)->institutions, TRUE);
			$inst = $inst['activeIdps'];
		}
		return $inst;
	}

	public function getInstitutionByEntityId($eid, $key = NULL)
	{
		foreach (self::getInstitutions() as $inst)
		{
			if ($inst['entity_id'] == $eid)
			{
				return $key ? $inst[$key] : $inst;
			}
		}
		return NULL;
	}

	/**
	 * When linking an account, by default a parameter of the plugin is used to
	 * determine the text "link your <something> account", and failing that the
	 * plugin name is used (eg., "link your Shibboleth account".
	 *
	 * Neither is appropriate here because we want to vary the text based on the
	 * ID provider used. I don't think the average user knows what InCommon or
	 * Shibboleth mean in this context.
	 */
	public static function onGetLinkDescription()
	{
		$sess = JFactory::getSession()->get('shibboleth.session', NULL);
		if ($sess && $sess['idp'] && ($rv = self::getInstitutionByEntityId($sess['idp'], 'label')))
		{
			return $rv;
		}
		// probably only possible if the user abruptly deletes their cookies
		return 'InCommon';
	}

	private static function getLoginParams()
	{
		$service = rtrim(JURI::base(),'/');

		if (empty($service))
		{
			$service = $_SERVER['HTTP_HOST'];
		}
		$juser = JFactory::getUser();
		if (version_compare(JVERSION, '2.5', 'ge'))
		{
			$com_user = 'com_users';
			$task     = ($juser->get('guest')) ? 'user.login' : 'user.link';
		}
		else
		{
			$com_user = 'com_user';
			$task     = ($juser->get('guest')) ? 'login' : 'link';
		}
		return array($service, $com_user, $task);
	}

	/**
	 * Similar justification to that for onGetLinkDescription.
	 *
	 * We want to show a button with the name of the previously-used ID
	 * provider on it instead of something generic like "Shibboleth"
	 */
	public static function onGetSubsequentLoginDescription($return)
	{
		// look up id provider
		if (isset($_COOKIE['shib-entity-id']) &&
			($idp = self::getInstitutionByEntityId($_COOKIE['shib-entity-id'], 'label')))
			{
			return '<input type="hidden" name="idp" value="'.$idp['id'].'" />Sign in with '.htmlentities($idp);
		}

		// if we couldn't figure out where they want to go to log in, we can't really help, so we redirect them with ?reset to get the full log-in provider list
		list($service, $com_user, $task) = self::getLoginParams();
		JFactory::getApplication()->redirect($service.'/index.php?reset=1&option='.$com_user.'&task=login'.(isset($_COOKIE['shib-return']) ? '&return='.$_COOKIE['shib-return'] : $return));
	}

	private static function htmlify()
	{
		return array(
			function($str) { return str_replace('"', '&quot;', $str); },
			function($str) { return htmlentities($str); }
		);
	}

	public static function onRenderOption($return, $title = 'With an affiliated institution:')
	{
		// hide the login box if the plugin is in "debug mode" and the special key is not set in the request
		$params = new JParameter(JPluginHelper::getPlugin('authentication', 'shibboleth')->params);
		if (($testKey = $params->get('testkey', NULL)) && !array_key_exists($testKey, $_GET))
		{
			return '<span />';
		}
		// saved id provider? use it as the default
		$prefill = isset($_COOKIE['shib-entity-id']) ? $_COOKIE['shib-entity-id'] : NULL;
		if (!$prefill && // no cookie
				($host = self::gethostbyaddr_timeout(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'], self::DNS)) && // can get a host
				preg_match('/[.]([^.]*?[.][a-z0-9]+?)$/', $host, $ma))
		{ // hostname lookup seems php jsonrational (not an ip address, has a few dots in it
			// try to look up a provider to pre-select based on the user's hostname
			foreach (self::getInstitutions() as $inst)
			{
				if (fnmatch('*'.$ma[1], $inst['host']))
				{
					$prefill = $inst['entity_id'];
					break;
				}
			}
		}

		// attach style and scripts
		foreach (array('bootstrap.min.js', 'bootstrap-select.min.js', 'shibboleth.js', 'bootstrap.min.css', 'bootstrap-select.min.css', 'bootstrap-theme.min.css', 'shibboleth.css') as $asset)
		{
			$isJs = preg_match('/[.]js$/', $asset);
			if ($isJs)
			{
				\Hubzero\Document\Assets::addPluginScript('authentication', 'shibboleth', $asset);
			}
			else
			{
				\Hubzero\Document\Assets::addPluginStylesheet('authentication', 'shibboleth', $asset);
			}
		}

		list($a, $h) = self::htmlify();

		// make a dropdown/button combo that (hopefully) gets prettied up client-side into a bootstrap dropdown
		$html = array();
		$html[] = '<form class="shibboleth account incommon-color" action="'.JRoute::_('index.php?option=com_users&view=login').'" method="get">';
		$html[] = '<div class="default-icon"></div>';
		$html[] = '<select title="'.$a($title).'" name="idp">';
		$html[] = '<option class="placeholder">'.$h($title).'</option>';
		foreach (self::getInstitutions() as $idp)
		{
			error_log($idp['label'].': '.$idp['entity_id']);
			$logo = $idp['logo_data'] ? '<img src="'.$idp['logo_data'].'" />' : '<span class="logo-placeholder"></span>';
			$html[] = '<option '.($prefill == $idp['entity_id'] ? 'selected="selected" ' : '').'value="'.$a($idp['entity_id']).'" data-content="'.$a($logo.' '.$h($idp['label'])).'">'.$h($idp['label']).'</option>';
		}
		$html[] = '</select>';
		$html[] = '<input type="hidden" name="authenticator" value="shibboleth" />';
		if ($return)
		{
			$html[] = '<input type="hidden" name="return" value="'.$a(preg_replace('/^.return=/', '', $return)).'" />';
		}
		$html[] = '<button class="submit" type="submit">Sign in</button>';
		$html[] = '</form>';
		return $html;
	}

	/**
	 * Actions to perform when logging out a user session
	 *
	 * @return     void
	 */
	public function logout()
	{
		list($service) = self::getLoginParams();

		$return = '/';
		if ($return = JRequest::getVar('return', '', 'method', 'base64'))
		{
			$return = base64_decode($return);

			if (!JURI::isInternal($return))
			{
				$return = '';
			}

			$return = '/' . ltrim($return, '/');
		}

		JFactory::getApplication()->redirect($return);
	}

	/**
	 * Check login status of current user with regards to Purdue CASphp json
	 *
	 * @access	public
	 * @return	Array $status
	 */
	public function status()
	{
		if (($sess = JFactory::getSession()->get('shibboleth.session', NULL)))
		{
			$this->log('found resumable session:', $sess);
			return array('username' => $sess['username'], 'eppn' => $sess['eppn'], 'idp' => $sess['idp']);
		}
		return array();
	}

	/**
	 * Actions to perform when logging in a user session
	 *
	 * @param      unknown &$credentials Parameter description (if any) ...
	 * @param      array &$options Parameter description (if any) ...
	 * @return     void
	 */
	public function login(&$credentials, &$options)
	{
		if ($return = JRequest::getVar('return', '', 'method', 'base64'))
		{
			$return = base64_decode($return);
			if (!JURI::isInternal($return))
			{
				$return = '';
			}
		}
		$options['return'] = $return;

		// extract variables set by mod_shib, if any
		https://www.incommon.org/federation/attributesummary.html
		if (($sid = isset($_SERVER['REDIRECT_Shib-Session-ID']) ? $_SERVER['REDIRECT_Shib-Session-ID'] : (isset($_SERVER['Shib-Session-ID']) ? $_SERVER['Shib-Session-ID'] : NULL)))
		{
			$attrs = array(
				'id' => $sid,
				'idp' => $_COOKIE['shib-entity-id']
			);
			foreach (array('email', 'eppn', 'displayName', 'givenName', 'sn', 'email') as $key)
			{
				if (isset($_SERVER[$key]))
				{
					$attrs[$key] = $_SERVER[$key];
				}
				elseif (isset($_SERVER['REDIRECT_'.$key]))
				{
					$attrs[$key] = $_SERVER['REDIRECT_'.$key];
				}
			}
			// normalize things a bit
			if (!isset($attrs['username']) && isset($attrs['eppn']))
			{
				$attrs['username'] = preg_replace('/@.*$/', '', $attrs['eppn']);
			}
			// eppn is sometimes or maybe always in practice an email address
			if (!isset($attrs['email']) && isset($attrs['eppn']) && strpos($attrs['eppn'], '@'))
			{
				$attrs['email'] = $attrs['eppn'];
			}
			if (!isset($attrs['displayName']) && isset($attrs['givenName']) && $attrs['sn'])
			{
				$attrs['displayName'] = $attrs['givenName'].' '.$attrs['sn'];
			}
			$options['shibboleth'] = $attrs;
			$this->log('login from:', $_SERVER);
			$this->log('session attributes: ', $attrs);
			JFactory::getSession()->set('shibboleth.session', $attrs);
		}
	}

	/**
	 * Method to setup Purdue CAS params and redirect to pucas auth URL
	 *
	 * @access	public
	 * @param   object	$view	view object
	 * @param 	object	$tpl	template object
	 * @return	void
	 */
	public function display($view, $tpl)
	{
		$app = JFactory::getApplication();
		list($service, $com_user, $task) = self::getLoginParams();
		$return = $view->return ? '&return='.$view->return : '';

		// discovery service for mod_shib to feed back the appropriate id provider
		// entityID. see below for more info
		if (array_key_exists('wayf', $_GET))
		{
			if (isset($_GET['return']) && isset($_COOKIE['shib-entity-id']))
			{
				$this->log('wayf passthru', $_COOKIE['shib-entity-id']);
				$app->redirect($_GET['return'].'&entityID='.$_COOKIE['shib-entity-id']);
			}
			$this->log('failed wayf');
			// invalid request, back to the login page with you
			$app->redirect($service.'/index.php?option='.$com_user.'&task=login'.(isset($_COOKIE['shib-return']) ? '&return='.$_COOKIE['shib-return'] : $return));
		}

		// invalid idp in request, send back to login landing
		$eid = isset($_GET['idp']) ? $_GET['idp'] : (isset($_COOKIE['shib-entity-id']) ? $_COOKIE['shib-entity-id'] : NULL);
		if (!isset($eid) || !self::getInstitutionByEntityId($eid))
		{
			$this->log('failed to look up entity id', $eid);
			$app->redirect($service.'/index.php?option='.$com_user.'&task=login'.$return);
		}

		// we're about to do at least a few redirects, some of which are out of our
		// control, so save a bit of state for when we get back
		//
		// we don't use the session store because we'd like it to outlive the
		// session so we can suggest this idp next time
		setcookie('shib-entity-id', $_GET['idp']);
		// send the request to mod_shib.
		//
		// this path should be set up in your configuration something like this:
		//
		// <Location /login/shibboleth>
		// 	AuthType shibboleth
		// 	ShibRequestSetting requireSession 1
		// 	Require valid-user
		// 	RewriteRule (.*) /index.php?option=com_users&authenticator=shibboleth&task=user.login [L]
		// </Location>
		//
		// mod_shib protects the path, and in doing so it looks at your SessionInitiators.
		// in shibobleth2.xml. ithis is what we use:
		//
		// <SessionInitiator type="Chaining" Location="/login/shibboleth" isDefault="true" id="Login">
		// 	<SessionInitiator type="SAML2" template="bindingTemplate.html"/>
		// 	<SessionInitiator type="Shib1"/>
		// 	<SessionInitiator type="SAMLDS" URL="https://dev06.hubzero.org/login?authenticator=shibboleth&amp;wayf"/>
		// </SessionInitiator>
		//
		// the important part here is the SAMLDS line pointing mod_shib right back
		// here, but with &wayf in the query string. we look for that a little bit
		// above here and feed the appropriate entity-id back to mod_shib with
		// another redirect. I wouldn't be at all surprised if there is a cleaner
		// way to communicate this that avoids the network hop. pull request, pls
		//
		// (if you are only using one ID provider you can avoid configuring
		// SessionInitiators at all and just define that service like:
		//
		//	<SSO entityID="https://idp.testshib.org/idp/shibboleth">
		// 	SAML2 SAML1
		// </SSO>
		//
		// in which case mod_shib will not need to do discovery, having only one
		// option.
		//
		// either way, the rewrite directs us back here to our login() method
		// where we can extract info about the authn from mod_shib
		$this->log('passing throuugh to shibd');
		$app->redirect($service.'/login/shibboleth');
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param   array 	$credentials Array holding the user credentials
	 * @param 	array   $options     Array of extra options
	 * @param	object	$response	 Authentication response object
	 * @return	boolean
	 */
	public function onAuthenticate($credentials, $options, &$response)
	{
		return $this->onUserAuthenticate($credentials, $options, $response);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param   array 	$credentials Array holding the user credentials
	 * @param 	array   $options     Array of extra options
	 * @param	object	$response	 Authentication response object
	 * @return	boolean
	 * @since 1.5
	 */
	public function onUserAuthenticate($credentials, $options, &$response)
	{
		// eppn is eduPersonPrincipalName and is the absolute lowest common
		// denominator for InCommon attribute exchanges. we can't really do
		// anything without it
		if (isset($options['shibboleth']['eppn'])) {
			$this->log('auth with', $options['shibboleth']);
			$method = (\JComponentHelper::getParams('com_users')->get('allowUserRegistration', FALSE)) ? 'find_or_create' : 'find';
			$hzal = \Hubzero\Auth\Link::$method('authentication', 'shibboleth', $options['shibboleth']['idp'], $options['shibboleth']['eppn']);

			if ($hzal === FALSE)
			{
				$response->status = JAUTHENTICATE_STATUS_FAILURE;
				$response->error_message = 'Unknown user and new user registration is not permitted.';
				return;
			}

			$hzal->email = isset($options['shibboleth']['email']) ? $options['shibboleth']['email'] : NULL;

			$this->log('hzal', $hzal);
			$response->auth_link = $hzal;
			$response->type = 'shibboleth';
			$response->status = JAUTHENTICATE_STATUS_SUCCESS;
			$response->fullname = isset($options['shibboleth']['displayName']) ? ucwords(strtolower($options['shibboleth']['displayName'])) : $options['shibboleth']['username'];

			if (!empty($hzal->user_id))
			{
				$user = JUser::getInstance($hzal->user_id); // Bring this in line with the rest of the system

				$response->username = $user->username;
				$response->email    = $user->email;
				$response->fullname = $user->name;
			}
			else
			{
				$response->username = '-' . $hzal->id; // The Open Group Base Specifications Issue 6, Section 3.426
				$response->email    = $response->username . '@invalid'; // RFC2606, section 2

				// Also set a suggested username for their hub account
				JFactory::getSession()->set('auth_link.tmp_username', $options['shibboleth']['username']);
			}

			$hzal->update();

			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'user_img'      => \Hubzero\User\Profile::getInstance($user->get('id'))->getPicture(0, false),
					'authenticator' => 'shibboleth'
				);
				$this->log('auth cookie', $prefs);

				$namespace = 'authenticator';
				$lifetime  = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
			}
		}
		else
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'An error occurred verifying your credentials.';
		}
	}

	/**
	 * @access	public
	 * @param   array - $options
	 * @return	void
	 */
	public function link($options = array())
	{
		$app = JFactory::getApplication();
		$juser = JFactory::getUser();

		if (($status = $this->status()))
		{
			$this->log('link', $status);
			// Get unique username
			$username = $status['eppn'];
			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'shibboleth', $status['idp']);

			if (\Hubzero\Auth\Link::getInstance($hzad->id, $username))
			{
				$this->log('already linked', array('domain' => $hzad->id, 'username' => $username));
				$app->redirect(JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=account'),
					'This account appears to already be linked to a hub account',
					'error');
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'shibboleth', $status['idp'], $username);
				$hzal->user_id = $juser->get('id');
				$this->log('setting link', $hzal);
				$hzal->update();
			}
		}
		else
		{
			// User somehow got redirect back without being authenticated (not sure how this would happen?)
			$app->redirect(JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=account'), 'There was an error linking your account, please try again later.', 'error');
		}
	}
}
