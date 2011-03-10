<?php
/**
* @version		
* @package		Joomdle
* @copyright		Antonio Duran Terres
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(JPATH_SITE.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');
jimport( 'joomla.plugin.plugin' );

class  plgSystemJoomdlesession extends JPlugin
{


	function plgSystemCache(& $subject, $config)
	{
		parent::__construct($subject, $config);

	}

	/* Updates Moodle Session */
	function onAfterRender()
	{
		global $mainframe;

		if($mainframe->isAdmin()) {
			return;
		}

		$logged_user = &JFactory::getUser();
		$user_id = $logged_user->id;
		
		/* Don't update guest sessions */
		if (!$user_id)
			return;

		if (!array_key_exists ('MoodleSession', $_COOKIE))
			return;

		$session_id = $_COOKIE['MoodleSession'];

		$reply = JoomdleHelperContent::call_method ("update_session", $session_id);
	}

}
