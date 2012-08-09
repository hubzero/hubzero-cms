<?php // no direct access
defined('_JEXEC') or die('Restricted access');
global $mainframe;
$mainframe->redirect( JRoute::_('index.php?option=com_user&view=login') );
JError::raiseError( 404, 'Not Found' );
