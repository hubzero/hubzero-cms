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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Include LinkedIn php library
require_once( join( DS, array( JPATH_ROOT, 'libraries', 'simplelinkedin-php', 'linkedin_3.2.0.class.php' ) ) );

class plgAuthenticationLinkedIn extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An array that holds the plugin configuration
	 */
	function plgAuthenticationJoomla(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Perform logout (not currently used)
	 *
	 * @access	public
	 * @return	void
	 */
	public function logout()
	{
		// This is handled by the JS API, and cannot be done server side
		// (at least, it cannot be done server side, given our authentication workflow
		// and the current limitations of the PHP SDK).
	}

	/**
	 * Check login status of current user with regards to linkedin
	 *
	 * @access	public
	 * @return	Array $status
	 */
	public function status()
	{
		$js = "$(document).ready(function() {
					$.getScript('https://platform.linkedin.com/in.js?async=true', function success() {
						onLinkedInLoad = function () {
							if(IN.User.isAuthorized()) {
								IN.API.Profile('me').result(function(profile) {
									var linkedin = $('#linkedin').siblings('.sign-out');
									linkedin
										.find('.current-user')
										.html(profile.values[0].firstName+' '+profile.values[0].lastName);

									linkedin.on('click', function( e ) {
										e.preventDefault();
										IN.User.logout(function() {
											linkedin.animate({'margin-top': -42}, function() {
												linkedin.find('.current-user').html('');
											});
										});
									});
								});
							}
						}

						IN.init({
							api_key   : '{$this->params->get('api_key')}',
							onLoad    : 'onLinkedInLoad',
							authorize : true
						});
					});
				});";

		JFactory::getDocument()->addScriptDeclaration($js);
	}

	/**
	 * Method to call when redirected back from linkedin after authentication
	 * Grab the return URL if set and handle denial of app privileges from linkedin
	 *
	 * @access	public
	 * @param   object	$credentials
	 * @param 	object	$options
	 * @return	void
	 */
	public function login(&$credentials, &$options)
	{
		$app = JFactory::getApplication();

		$jsession =& JFactory::getSession();

		// Check to see if a return parameter was specified
		if($return = JRequest::getVar('return', '', 'method', 'base64'))
		{
			$b64dreturn = base64_decode($return);
			if(!JURI::isInternal($b64dreturn))
			{
				$b64dreturn = '';
			}
		}

		// Set the return variable
		$options['return'] = $b64dreturn;
		$com_user = (version_compare(JVERSION, '2.5', 'ge')) ? 'com_users' : 'com_user';

		// Set up linkedin configuration
		$linkedin_config['appKey']    = $this->params->get('api_key');
		$linkedin_config['appSecret'] = $this->params->get('app_secret');

		// Create Object
		$linkedin_client = new LinkedIn($linkedin_config);

		if(!JRequest::getVar('oauth_verifier', NULL))
		{
			// User didn't authorize our app, or, clicked cancel
			$app->redirect(JRoute::_('index.php?option=' . $com_user . '&view=login&return=' . $return),
				'To log in via LinkedIn, you must authorize the ' . $app->getCfg('sitename') . ' app.', 
				'error');
		}

		// LinkedIn has sent a response, user has granted permission, take the temp access token,
		// the user's secret and the verifier to request the user's real secret key
		$request = $jsession->get('linkedin.oauth.request');
		$reply = $linkedin_client->retrieveTokenAccess($request['oauth_token'],
					$request['oauth_token_secret'],
					JRequest::getVar('oauth_verifier'));
		if($reply['success'] === TRUE)
		{
			// The request went through without an error, gather user's 'access' tokens
			$jsession->set('linkedin.oauth.access', $reply['linkedin']);

			// Set the user as authorized for future quick reference
			$jsession->set('linkedin.oauth.authorized', TRUE);
		}
		else
		{
			return JError::raiseError(500, JText::_('Something went wrong here...'));
		}
	}

	/**
	 * Method to setup linkedin params and redirect to linkedin auth URL
	 *
	 * @access	public
	 * @param   object	$view	view object
	 * @param 	object	$tpl	template object
	 * @return	void
	 */
	public function display($view, $tpl)
	{
		$app = JFactory::getApplication();

		// If someone is logged in already, then we're linking an account
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

		// Set up the redirect URL
		$juri        =& JURI::getInstance();
		$service     = trim($juri->base(), DS);
		$return      = isset($view->return) ? "&return=".$view->return : '';
		$redirect_to = "{$service}/index.php?option={$com_user}&task={$task}&authenticator=linkedin{$return}";

		// User initiated LinkedIn connection, setup linkedin configuration
		$linkedin_config['callbackUrl'] = $redirect_to . '&' . LINKEDIN::_GET_TYPE . '=initiate&' . LINKEDIN::_GET_RESPONSE . '=1';
		$linkedin_config['appKey']      = $this->params->get('api_key');
		$linkedin_config['appSecret']   = $this->params->get('app_secret');

		// Create linkedin object
		$linkedin_client = new LinkedIn($linkedin_config);

		// Check for a response from LinkedIn
		$_GET[LINKEDIN::_GET_RESPONSE] = (isset($_GET[LINKEDIN::_GET_RESPONSE])) ? $_GET[LINKEDIN::_GET_RESPONSE] : '';
		if(!$_GET[LINKEDIN::_GET_RESPONSE])
		{
			// LinkedIn hasn't sent us a response, the user is initiating the connection
			// Send a request for a LinkedIn access token
			$reply = $linkedin_client->retrieveTokenRequest();
			if($reply['success'] === TRUE)
			{
				// Store the request token
				$jsession =& JFactory::getSession();
				$jsession->set('linkedin.oauth.request', $reply['linkedin']);

				// Redirect the user to the LinkedIn authentication/authorization page to initiate validation
				$app->redirect(LINKEDIN::_URL_AUTH . $reply['linkedin']['oauth_token']);
			}
			return;
		}

		// Are the already logged on?
		return JError::raiseError(500, JText::_('Something went wrong here...'));
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
	public function onAuthenticate( $credentials, $options, &$response )
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
	 */
	public function onUserAuthenticate($credentials, $options, &$response)
	{
		// Make sure we have authorization
		$jsession =& JFactory::getSession();
		if($jsession->get('linkedin.oauth.authorized') == TRUE)
		{
			// User initiated LinkedIn connection, set up config
			$linkedin_config['appKey']    = $this->params->get('api_key');
			$linkedin_config['appSecret'] = $this->params->get('app_secret');

			// Create the object
			$linkedin_client = new LinkedIn($linkedin_config);
			$linkedin_client->setTokenAccess($jsession->get('linkedin.oauth.access'));

			// Get the linked in profile
			$profile = $linkedin_client->profile('~:(id,first-name,last-name,email-address)');
			$profile = $profile['linkedin'];

			// Parse the profile XML
			$profile = new SimpleXMLElement($profile);

			// Get the profile values
			$li_id      = $profile->{'id'};
			$first_name = $profile->{'first-name'};
			$last_name  = $profile->{'last-name'};
			$full_name  = $first_name . ' ' . $last_name;
			$username   = (string) $li_id; // (make sure this is unique)

			$hzal = Hubzero_Auth_Link::find_or_create('authentication', 'linkedin', null, $username);
			$hzal->email = (string) $profile->{'email-address'};

			// Set response variables
			$response->auth_link = $hzal;
			$response->type      = 'linkedin';
			$response->status    = JAUTHENTICATE_STATUS_SUCCESS;
			$response->fullname  = $full_name;

			if(!empty($hzal->user_id))
			{
				$user = JUser::getInstance($hzal->user_id);

				$response->username = $user->username;
				$response->email    = $user->email;
				$response->fullname = $user->name;
			}
			else
			{
				$response->username = '-'.$hzal->id;
				$response->email    = $response->username . '@invalid';

				// Also set a suggested username for their hub account
				$sub_email    = explode('@', (string) $profile->{'email-address'}, 2);
				$tmp_username = $sub_email[0];
				JFactory::getSession()->set('auth_link.tmp_username', $tmp_username);
			}

			$hzal->update();
		}
		else // no authorization
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Username and password do not match or you do not have an account yet.';
		}
	}

	/**
	 * Similar to onAuthenticate, except we already have a logged in user, we're just linking accounts
	 *
	 * @access	public
	 * @return	void
	 */
	public function link()
	{
		$app = JFactory::getApplication();
		$juser    = JFactory::getUser();
		$jsession =& JFactory::getSession();

		// Set up linkedin configuration
		$linkedin_config['appKey']    = $this->params->get('api_key');
		$linkedin_config['appSecret'] = $this->params->get('app_secret');

		// Create Object
		$linkedin_client = new LinkedIn($linkedin_config);

		if(!JRequest::getVar('oauth_verifier', NULL))
		{
			// User didn't authorize our app, or, clicked cancel
			$app->redirect(JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=account'),
				'To log in via LinkedIn, you must authorize the ' . $app->getCfg('sitename') . ' app.', 
				'error');
		}

		// LinkedIn has sent a response, user has granted permission, take the temp access token,
		// the user's secret and the verifier to request the user's real secret key
		$request = $jsession->get('linkedin.oauth.request');
		$reply = $linkedin_client->retrieveTokenAccess($request['oauth_token'],
						$request['oauth_token_secret'],
						JRequest::getVar('oauth_verifier'));
		if($reply['success'] === TRUE)
		{
			// The request went through without an error, gather user's 'access' tokens
			$jsession =& JFactory::getSession();
			$jsession->set('linkedin.oauth.access', $reply['linkedin']);

			// Set the user as authorized for future quick reference
			$jsession->set('linkedin.oauth.authorized', TRUE);
		}
		else
		{
			return JError::raiseError(500, JText::_('Access token retrieval failed'));
		}

		if($jsession->get('linkedin.oauth.authorized') == TRUE)
		{
			$linkedin_client->setTokenAccess($jsession->get('linkedin.oauth.access'));

			// Get the linked in profile
			$profile = $linkedin_client->profile('~:(id,first-name,last-name,email-address)');
			$profile = $profile['linkedin'];

			// Parse the profile XML
			$profile = new SimpleXMLElement($profile);

			// Get the profile values
			$li_id      = $profile->{'id'};
			$username   = (string) $li_id; // (make sure this is unique)

			$hzad = Hubzero_Auth_Domain::getInstance('authentication', 'linkedin', '');

			// Create the link
			if(Hubzero_Auth_Link::getInstance($hzad->id, $username))
			{
				// This linkedin account is already linked to another hub account
				$app->redirect(JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=account'), 
					'This linkedin account appears to already be linked to a hub account', 
					'error');
			}
			else
			{
				$hzal = Hubzero_Auth_Link::find_or_create('authentication', 'linkedin', null, $username);
				$hzal->user_id = $juser->get('id');
				$hzal->email = (string) $profile->{'email-address'};
				$hzal->update();
			}
		}
		else // no authorization
		{
			// User didn't authorize our app, or, clicked cancel
			$app->redirect(JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=account'),
				'To log in via LinkedIn, you must authorize the ' . $app->getCfg('sitename') . ' app.', 
				'error');
		}
	}
}