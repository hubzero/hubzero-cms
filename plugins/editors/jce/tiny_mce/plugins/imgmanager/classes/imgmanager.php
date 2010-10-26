<?php
/**
* @version 		$Id: imgmanager.php 46 2009-05-26 16:59:42Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
 
defined('_JEXEC') or die('Restricted access'); 

require_once(JCE_LIBRARIES .DS. 'classes' .DS. 'manager.php'); 
 
class ImageManager extends Manager
{
    var $_ext = 'image=jpg,jpeg,gif,png';
	/**
	* @access	protected
	*/
	function __construct()
	{
		parent::__construct();			
		
		// Set the file type map from parameters
		$this->setFileTypes($this->getPluginParam('imgmanager_extensions', $this->_ext));
		// Init plugin
		$this->init();
	}
	/**
	 * Returns a reference to a editor object
	 *
	 * This method must be invoked as:
	 * 		<pre>  $browser = &JCE::getInstance();</pre>
	 *
	 * @access	public
	 * @return	JCE  The editor object.
	 * @since	1.5
	 */
	function &getInstance()
	{
		static $instance;

		if (!is_object($instance)) {
			$instance = new ImageManager();
		}
		return $instance;
	}
	/**
	 * Initialise the plugin
	 */
	function init()
	{
		// check the user/group has editor permissions
		$this->checkPlugin() or die(JError::raiseError(403, JText::_('Access Forbidden')));
		
		parent::init();
		
		// Setup plugin XHR callback functions 
		$this->setXHR(array($this, 'getDimensions'));
		
		// Set javascript file array
		$this->script(array('imgmanager'), 'plugins');
		// Set css file array
		$this->css(array('imgmanager'), 'plugins');
		
		// Load extensions if any
		$this->loadExtensions();
	}

	/**
	 * Get the dimensions of an image
	 * @return array Dimensions as array
	 * @param object $file Relative path to image
	 */
	function getDimensions($file)
	{			
		$path = Utils::makePath($this->getBaseDir(), rawurldecode($file));
		$h = array(
			'width'		=>	'', 
			'height'	=>	''
		);
		if (file_exists($path)) {
			$dim = @getimagesize($path);
			$h = array(
				'width'		=>	$dim[0], 
				'height'	=>	$dim[1]
			);
		}
		return $h;
	}
	/**
	 * Get list of uploadable extensions
	 * @return Mapped extension list (list mapped to type object eg: 'images', 'jpeg,jpg,gif,png')
	 */
	function getUploadFileTypes()
	{
		$list = $this->getPluginParam('imgmanager_extensions', 'image=jpg,jpeg,gif,png');
		return $this->mapUploadFileTypes($list);
	}
}
?>