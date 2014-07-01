<?php
/**
 * @version     $Id: jqueryintegrator.php revision date tushev $
 * @package     Joomla
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
	 * Hook for after routing application
	 * 
	 * @return     void
	 */
	public function onAfterRoute()
	{
		$app = JFactory::getApplication();

		$client = 'Site';
		if ($app->isAdmin())
		{
			$client = 'Admin';
		}

		// Check if active for this client (Site|Admin)
		if (!$this->params->get('activate' . $client) || JRequest::getVar('format') == 'pdf')
		{
			return;
		}

		$document = JFactory::getDocument();
		$root = JURI::root(true);

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
					//$document->addScript($root . '/media/system/js/jquery.js');
					//$document->addScript($root . '/media/system/js/jquery.migrate.js');
					JHTML::_('behavior.framework');
				break;
			}
		}
		if ($value = $this->params->get('jqueryui'))
		{
			if ($value == 1)
			{
				//$version = $this->params->get('jqueryuiVersion', '1.8.6');
				//$document->addScript($root . '/media/system/js/jquery.ui.js');
				JHTML::_('behavior.framework', true);
			}
			elseif ($value == 2)
			{
				$document->addScript($this->params->get('jqueryuicdnpath'));
			}

			/*if ($value = $this->params->get('jqueryuicss'))
			{
				if ($value == 1)
				{
					$path = $this->params->get('jqueryuicsspath', '/media/system/css/jquery.ui.css');
					if ($path != '/media/system/css/jquery.ui.css') //$root . '/media/system/css/jquery.ui.css'
					{
						if (substr($path, 0, strlen($root)) != $root && substr($path, 0, strlen('http')) != 'http')
						{
							$path = $root . '/' . ltrim($path, '/');
						}
						$document->addStyleSheet($path);
					}
					else
					{
						\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');
					}
				}
			}*/
		}
		if ($value = $this->params->get('jqueryfb'))
		{
			if ($value == 1)
			{
				//$version = $this->params->get('jqueryfbVersion', '2.0.4');

				//$document->addScript($root . '/media/system/js/jquery.fancybox.js');

				JHTML::_('behavior.modal');
			}
			elseif ($value == 2)
			{
				$document->addScript($this->params->get('jqueryfbcdnpath'));
			}

			/*if ($value = $this->params->get('jqueryfbcss'))
			{
				if ($value == 1)
				{
					$path = $this->params->get('jqueryfbcsspath', '/media/system/css/jquery.fancybox.css'); //$root . '/media/system/css/jquery.fancybox.css'
					if ($path != '/media/system/css/jquery.fancybox.css')
					{
						if (substr($path, 0, strlen($root)) != $root && substr($path, 0, strlen('http')) != 'http')
						{
							$path = $root . '/' . ltrim($path, '/');
						}
						$document->addStyleSheet($path);
					}
					else
					{
						\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancybox.css');
					}
				}
			}*/
		}
		/*if ($value = $this->params->get('jquerytools')) 
		{
			if ($value == 1)
			{
				$version = $this->params->get('jquerytoolsVersion', '1.2.5');
				
				$document->addScript($root . '/media/system/js/jquery.tools.js');
			}
			elseif ($value == 2)
			{
				$document->addScript($this->params->get('jquerytoolscdnpath'));
			}
		}*/
		if ($this->params->get('noconflict' . $client))
		{
			$document->addScript($root . '/media/system/js/jquery.noconflict.js');
			JHTML::_('behavior.mootools');
		}
	}

	/**
	 * hook for after dispatching application
	 * 
	 * @return     void
	 */
	public function onAfterDispatch()
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();

		// No remember me for admin
		$client = 'Site';
		if ($app->isAdmin() || JRequest::getVar('format') == 'pdf')
		{
			$client = 'Admin';
			return;
		}

		if (!method_exists($document, 'getHeadData'))
		{
			return;
		}

		$no_html = JRequest::getInt('no_html', 0);
		$format  = JRequest::getVar('format', '');

		if ($document->getType() == 'raw')
		{
			$no_html = 1;
		}

		if (!$this->params->get('noconflict' . $client) && !$no_html && $format != 'xml')
		{
			$base = rtrim(JURI::base(true), '/');

			$data = $document->getHeadData();
			$nd = array();
			$mootools = array(
				$base . '/media/system/js/mootools-uncompressed.js',
				$base . '/media/system/js/mootools.js',
				$base . '/media/system/js/mootools-core-uncompressed.js',
				$base . '/media/system/js/mootools-core.js',
				$base . '/media/system/js/mootools-more-uncompressed.js',
				$base . '/media/system/js/mootools-more.js',
				$base . '/media/system/js/caption-uncompressed.js',
				$base . '/media/system/js/caption.js',
				$base . '/media/system/js/core-uncompressed.js',
				$base . '/media/system/js/core.js'
			);
			foreach ($data['scripts'] as $key => $val)
			{
				if (!in_array($key, $mootools))
				{
					$nd[$key] = $val;
				}
			}
			$data['scripts'] = $nd;

			$nds = array();
			if (is_array($data['script']))
			{
				foreach ($data['script'] as $key => $script)
				{
					if (is_array($script))
					{
						foreach ($script as $i => $sc)
						{
							$data['script'][$key][$i] = preg_replace('/window\.addEvent\(\'domready\', function\(\)\s*\{(.*)\}\)\;/is', '', $sc);
							$data['script'][$key][$i] = preg_replace('/window\.addEvent\(\'load\', function\(\)\s*\{(.*)\}\)\;/is', '', $data['script'][$key][$i]);
						}
					}
					else
					{
						$data['script'][$key] = preg_replace('/window\.addEvent\(\'domready\', function\(\)\s*\{(.*?)\}\)\;/is', '', $script);
						$data['script'][$key] = preg_replace('/window\.addEvent\(\'load\', function\(\)\s*\{(.*?)\}\)\;/is', '', $data['script'][$key]);
					}
				}
			}
			else
			{
				$data['script'] = preg_replace('/window\.addEvent\(\'domready\', function\(\)\s*\{(.*)\}\)\;/uiUs', '', $data['script']);
				$data['script'] = preg_replace('/window\.addEvent\(\'load\', function\(\)\s*\{(.*)\}\)\;/uiUs', '', $data['script']);
			}

			$document->setHeadData($data);
		}
	}
}
