<?php
/**
 * @version		
 * @package		Joomdle
 * @subpackage	Content
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
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'mappings.php');

/**
 *
 * @static
 * @package		Joomdle
 * @since 1.5
 */
class JoomdleHelperUsers
{
	function create_joomla_user ($user_info)
	{
                $usersConfig = &JComponentHelper::getParams( 'com_users' );

                $authorize      =& JFactory::getACL();

		$user = new JUser ();

                // Initialize new usertype setting
                $newUsertype = $usersConfig->get( 'new_usertype' );
                if (!$newUsertype) {
                        $newUsertype = 'Registered';
                }


                // Bind the user_info array to the user object
                if (!$user->bind( $user_info, 'usertype' )) {
                        JError::raiseError( 500, $user->getError());
                }

                // Set some initial user values
                $user->set('id', 0);
                $user->set('usertype', $newUsertype);
                $user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));

                $date =& JFactory::getDate();
                $user->set('registerDate', $date->toMySQL());

                $parent =& JFactory::getUser();
                $user->setParam('u'.$parent->id.'_parent_id', $parent->id);

		if ($user_info['block'])
			$user->set('block', '1');

		// If there was an error with registration
                if ( !$user->save() )
                {
                        return false;
                }

		/* Update profile additional data */
		return JoomdleHelperMappings::save_user_info ($user_info);
                // Send registration confirmation mail
              //  $password = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
               // $password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email
               // UserController::_sendMail($user, $password);
	}

	function activate_joomla_user ($username)
	{
		$user =& JFactory::getUser($username);
		$user->set('block', '0');
                if ( !$user->save() )
                        return false;

		return true;
	}
}

?>
