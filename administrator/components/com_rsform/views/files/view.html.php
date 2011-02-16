<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RSFormViewFiles extends JView
{
	function display( $tpl = null )
	{
		$this->assignRef('canUpload', $this->get('canUpload'));
		
		$files = $this->get('files');
		$this->assignRef('files', $files);
		
		$folders = $this->get('folders');
		$this->assignRef('folders', $folders);
		
		$this->assignRef('elements', $this->get('elements'));
		
		$this->assignRef('current', $this->get('current'));
		$this->assignRef('previous', $this->get('previous'));
		
		$start = count($folders);
		$count = $start + count($files);
		
		$this->assignRef('link', $link);
		$this->assignRef('params', $params);
		$this->assignRef('start', $start);
		$this->assignRef('count', $count);
		$this->assignRef('task', $task);
		
		parent::display($tpl);
	}
}