<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------

$config = JFactory::getConfig();

if ($config->getValue('config.debug')) {
	error_reporting(E_ALL);
	@ini_set('display_errors','1');
}

jimport('joomla.application.component.view');

require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'controller.php' );

$pathway   =& $mainframe->getPathway();
$pathway->addItem( 'Subscribe to newsletters', '/index.php?option=' . $option);


$controller = new MailingListController();

// Require user to be logged in for ANY page or request of this component
if (!$controller->userloggedin())
{
	$controller->redirect();
}
else
{
        $controller->execute(JRequest::getCmd('task'));
	$controller->redirect();
}

?>

