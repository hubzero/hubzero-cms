<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.plugin.plugin' );
ximport('Hubzero_User_Profile');

class plgXAuthenticationHubzero extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param 	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 */
	function plgXAuthenticationHubzero(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param   array 	$credentials Array holding the user credentials
	 * @param 	array   $options	 Array of extra options
	 * @param	object	$response	Authentication response object
	 * @return	object	boolean
	 */
	function onAuthenticate( $credentials, $options, &$response )
	{
		// For JLog
		$response->type = 'hubzero';

		if (empty($credentials['password']))
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Can not have a blank password';
			return false;
		}

		$profile = Hubzero_User_Profile::getInstance( $credentials['username'] );

		if (empty($profile)) {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Username not found';
			return false;
		}

		$passhash = $profile->get('userPassword');

		if (empty($passhash)) {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Password not found for requested account';
			return false;
		}

		if( Hubzero_User_Helper::encrypt_password( $credentials['password'] ) != $passhash )
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Incorrect username/password';
			return false;
		}

		$response->username = $profile->get('username');
		$response->email = $profile->get('email');
		$response->fullname = $profile->get('name');
		$response->password_clear = $credentials['password'];
		// Were good - So say so.
		$response->status		= JAUTHENTICATE_STATUS_SUCCESS;
		$response->error_message = '';
	}
}

?>
