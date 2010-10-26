<?php
/**
 * @version		$Id$
 * @package		Joomla Content Editor (JCE)
 * @copyright	Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
 * @license		GNU/GPL
 * This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

jimport('joomla.application.component.view');

class JceViewPopup extends JView
{
    function display($tpl = null)
    {
        global $mainframe;
		
		// Get variables
        $img 	= JRequest::getVar('img');
        $title 	= JRequest::getWord('title');
        $mode 	= JRequest::getInt('mode', '0');
        $click 	= JRequest::getInt('click', '0');
        $print 	= JRequest::getInt('print', '0');

        $width 	= JRequest::getInt('w');
        $height = JRequest::getInt('h');

		// Cleanup img variable
		$img 	= preg_replace('/[^a-z\.\/_-]/i', '', $img);
		
		$title 	= isset($title) ? str_replace('_', ' ', $title) : basename($img);
		// img src must be passed
		if ($img) {
			$features = array (
	        	'img'	=>	str_replace(JURI::root(), '', $img),
	        	'title'	=>	$title,
				'alt'	=>	$title,
	        	'mode'	=>	$mode,
	        	'click'	=>	$click,
	        	'print'	=>	$print,
	        	'width'	=>	$width,
	        	'height'=>	$height
        	);

        	$this->assign('features', $features);
        	parent::display($tpl);	
		} else {
			$mainframe->redirect('index.php');
		}
    }
}
?>
