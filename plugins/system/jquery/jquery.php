<?php
/**
 * @version             $Id: jqueryintegrator.php revision date tushev $
 * @package             Joomla
 * @subpackage  System
 * @copyright   Copyright (C) S.A. Tushev, 2010. All rights reserved.
 * @license     GNU GPL v2.0
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
 
 // no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemJquery extends JPlugin 
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args (void) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	public function __construct(&$subject, $params)
	{
		parent::__construct($subject, $params);
	}
	
	public function onAfterRoute() 
	{
		$app = JFactory::getApplication();

		$client = 'Site';
		if ($app->isAdmin()) 
		{
			$client = 'Admin';
		}
		
		// Check if active for this client (Site|Admin)
		if (!$this->params->get('activate' . $client)) 
		{
			return;
		}
		
		$document = JFactory::getDocument();
		
		if ($this->params->get('noconflict' . $client)) 
		{
			JHTML::_('behavior.mootools');
			$document->addScriptDeclaration('jQuery.noConflict();');
		}

		if ($value = $this->params->get('jquery')) 
		{
			$version = $this->params->get('jqueryVersion', '1.7.2');
			
			switch ($value)
			{
				case 5:
					$document->addScript('//ajax.microsoft.com/ajax/jquery/jquery-' . $version . '.min.js');
				break;
				case 4:
					$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/' . $version . '/jquery.min.js');
				break;
				case 3:
					$document->addScript('http://code.jquery.com/jquery-' . $version . '.min.js');
				break;
				case 2:
					$document->addScript($this->params->get('jquerycdnpath'));
				break;
				case 1:
				default:
					$document->addScript(JURI::root(true) . '/media/system/js/jquery.js');
				break;
			}
		}
		if ($value = $this->params->get('jqueryui')) 
		{
			if ($value == 1) 
			{
				$version = $this->params->get('jqueryuiVersion', '1.8.6');
				
				$document->addScript(JURI::root(true) . '/media/system/js/jquery.ui.js');
			}
			elseif ($value == 2) 
			{
				$document->addScript($this->params->get('jqueryuicdnpath'));
			}

			/*if ($value = $this->params->get('jqueryuicss')) 
			{
				if ($value == 1) 
				{
					$document->addStyleSheet($this->params->get('jqueryuicsspath'));
				}
			}*/
		}
		if ($value = $this->params->get('jqueryfb')) 
		{
			if ($value == 1) 
			{
				$version = $this->params->get('jqueryfbVersion', '2.0.4');
				
				$document->addScript(JURI::root(true) . '/media/system/js/jquery.fancybox.js');
			}
			elseif ($value == 2) 
			{
				$document->addScript($this->params->get('jqueryfbcdnpath'));
			}

			/*if ($value = $this->params->get('jqueryfbcss')) 
			{
				if ($value == 1) 
				{
					$document->addStyleSheet($this->params->get('jqueryfbcsspath'));
				}
			}*/
		}
		if ($value = $this->params->get('jquerytools')) 
		{
			if ($value == 1) 
			{
				$version = $this->params->get('jquerytoolsVersion', '1.2.5');
				
				$document->addScript(JURI::root(true) . '/media/system/js/jquery.tools.js');
			}
			elseif ($value == 2) 
			{
				$document->addScript($this->params->get('jquerytoolscdnpath'));
			}
		}
	}

	public function onAfterDispatch()
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();

		// No remember me for admin
		if ($app->isAdmin()) 
		{
			return;
		}
		
		$no_html = JRequest::getVar('no_html', 0);

		if ($document->getType() == 'raw')
		{
			$no_html = 1;
		}

		if (!$this->params->get('jquerynoconflict') && !$no_html) 
		{
			
			$data = $document->getHeadData();
			$nd = array();
			$mootools = array(
				'/media/system/js/mootools-uncompressed.js',
				'/media/system/js/mootools.js',
				'/media/system/js/caption.js'
			);
			foreach ($data['scripts'] as $key => $val)
			{
				//if (substr($key, 0, strlen('/media/system/js')) != '/media/system/js')
				if (!in_array($key, $mootools))
				{
					$nd[$key] = $val;
				}
			}
			$data['scripts'] = $nd;

			$nds = array();
			$data['script'] = preg_replace('/window\.addEvent\(\'domready\', function\(\)\{(.*)\}\)\;/', '', $data['script']);
			//print_R($data['script']);
			/*foreach ($data['script'] as $key => $val)
			{
				if (substr(trim($val), 0, strlen('window.addEvent(\'domready\', function(){')) == 'window.addEvent(\'domready\', function(){')
				{
					continue;
				}
				$nds[$key] = $val;
				if (substr(trim($val), 0, strlen('function keepAlive() {')) == 'function keepAlive() {')
				{
					$nds[$key] = 'function keepAlive() { $.get("index.php"); } jQuery(document).ready(function($){ var periodic = setInterval(keepAlive, 840000); });';
				}
			}
			$data['script'] = $nds;*/

			$document->setHeadData($data);
		}
	}
}
