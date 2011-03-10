<?php
/**
 * Joomla! 1.5 component Joomdle
 *
 * @version $Id: controller.php 2009-04-17 03:54:05 svn $
 * @author Antonio Durán Terrés
 * @package Joomla
 * @subpackage Joomdle
 * @license GNU/GPL
 *
 * Shows information about Moodle courses
 *
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'parents.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'shop.php');

/**
 * Joomdle Component Controller
 */
class JoomdleController extends JController {

/*	function execute ($task)
	{
		$task = 'display';
		parent::execute ($task);
	}
*/
	function display() {
        // Make sure we have a default view
        if( !JRequest::getVar( 'view' )) {
		    JRequest::setVar('view', 'joomdle' );
        } else {
		$view = JRequest::getVar( 'view' );
		JRequest::setVar('view', $view );
    }
    
    //load the styles into the sheet
		ximport('xdocument');
		XDocument::addComponentStylesheet("com_joomdle");

		parent::display();
	}

	function enrol () {
		global $mainframe;

		$user = & JFactory::getUser();

		if (!$user->id)
			$mainframe->redirect(JURI::base ().'index.php?option=com_user&view=login');

		$course_id = JRequest::getVar( 'course_id' );
		$course_id = (int) $course_id;
		$params = &$mainframe->getParams();

		$user = & JFactory::getUser();
		$username = $user->get('username');
		JoomdleHelperContent::enrolUser ($username, $course_id);

		// Redirect to course

		$session                =& JFactory::getSession();
		$token = md5 ($session->getId());
		
		$itemid = JRequest::getVar( 'Itemid' );

		$open_in_wrapper = JRequest::getVar( 'wrapper', '' );
		$params = &$mainframe->getParams();
		if ($open_in_wrapper == '')
		{		if ($params->get( 'linkstarget' ) == 'wrapper')
			$open_in_wrapper = 1;
		else
			$open_in_wrapper = 0;
		}
		$moodle_auth_land_url = $params->get( 'MOODLE_URL' ).'auth/joomdle/land.php';

		$url = $moodle_auth_land_url."?username=$username&token=$token&mtype=course&id=$course_id&use_wrapper=$open_in_wrapper&Itemid=$itemid";

		$mainframe->redirect ($url);
	}


	function assigncourses ()
	{

		$children = JRequest::getVar( 'children' );

		if (!JoomdleHelperParents::check_assign_availability ($children))
		{
			$message = JText::_( 'CJ NOT ENOUGH COURSES' );
			$this->setRedirect('index.php?option=com_joomdle&view=assigncourses', $message); //XXX poenr un get current uri
		}
		else
		{
			JoomdleHelperParents::assign_courses ($children);
			$message = JText::_( 'CJ COURSES ASSIGNED' );
			$this->setRedirect('index.php?option=com_joomdle&view=assigncourses', $message); //XXX poenr un get current uri
		}
	}

	function register_save ()
	{

		$otherlanguage =& JFactory::getLanguage();
		$otherlanguage->load( 'com_user', JPATH_SITE );

		// If user registration is not allowed, show 403 not authorized.
                $usersConfig = &JComponentHelper::getParams( 'com_users' );
                if ($usersConfig->get('allowUserRegistration') == '0') {
                        JError::raiseError( 403, JText::_( 'Access Forbidden' ));
                        return;
                }

                $authorize      =& JFactory::getACL();

                $user = new JUser ();

                // Initialize new usertype setting
                $newUsertype = $usersConfig->get( 'new_usertype' );
                if (!$newUsertype) {
                        $newUsertype = 'Registered';
                }

                // Bind the post array to the user object
                if (!$user->bind( JRequest::get('post'), 'usertype' )) {
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

                // If user activation is turned on, we need to set the activation information
                $useractivation = $usersConfig->get( 'useractivation' );
                if ($useractivation == '1')
                {
                        jimport('joomla.user.helper');
                        $user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
                        $user->set('block', '1');
                }

                // If there was an error with registration, set the message and display form
                if ( !$user->save() )
                {
                        JError::raiseWarning('', JText::_( $user->getError()));
 //                       $this->register();
			$this->setRedirect('index.php?option=com_joomdle&view=register'); //XXX poenr un get current uri
                        return false;
                }


                // Send registration confirmation mail
                $password = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
                $password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email
              //  UserController::_sendMail($user, $password);

		$parent_user   =& JFactory::getUser();
		// Set parent role in Moodle
		JoomdleHelperContent::call_method ("add_parent_role", $user->username, $parent_user->username);

		$message = JText::_( 'CJ USER CREATED' );
		$this->setRedirect('index.php?option=com_joomdle&view=register', $message); //XXX poenr un get current uri
	}
}
?>
