<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSFormControllerFiles extends RSFormController
{
	function __construct()
	{
		parent::__construct();
	}
	
	function display()
	{
		JRequest::setVar('view', 'files');
		JRequest::setVar('layout', 'default');
		
		parent::display();
	}
	
	function upload()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the model
		$model = $this->getModel('files');
		
		$folder = $model->getCurrent();
		$result = $model->upload();
		$file = $model->getUploadFile();
		
		if ($result)
			$this->setRedirect('index.php?option=com_rsform&controller=files&task=display&folder='.$folder.'&tmpl=component', JText::sprintf('UPLOAD OF IMAGE SUCCESSFUL', $file, $folder));
		else
			$this->setRedirect('index.php?option=com_rsform&controller=files&task=display&folder='.$folder.'&tmpl=component', JText::sprintf('UPLOAD OF IMAGE FAILED', $file));
	}
}
?>