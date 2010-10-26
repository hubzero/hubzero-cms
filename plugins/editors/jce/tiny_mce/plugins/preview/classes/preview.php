<?php
/**
* @version		$Id: advlink.php 155 2009-07-11 13:18:25Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

// Load class dependencies
require_once(JCE_LIBRARIES .DS. 'classes' .DS. 'plugin.php');

class Preview extends JContentEditorPlugin 
{
	/**
	* Constructor activating the default information of the class
	*
	* @access	protected
	*/
	function __construct()
	{
		parent::__construct();
		
		// check the user/group has editor permissions
		$this->checkPlugin() or die(JError::raiseError(403, JText::_('Restricted Access')));
	}
	/**
	 * Returns a reference to a plugin object
	 *
	 * This method must be invoked as:
	 * 		<pre>  $advlink = &AdvLink::getInstance();</pre>
	 *
	 * @access	public
	 * @return	JCE  The editor object.
	 * @since	1.5
	 */
	function &getInstance()
	{
		static $instance;

		if (!is_object($instance)) {
			$instance = new Preview();
		}
		return $instance;
	}
	
	/**
	 * Display Preview content
	 * @return void
	 */
	function display()
	{				
		$db 		=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$dispatcher	=& JDispatcher::getInstance();
		$language 	=& JFactory::getLanguage();
		
		// If this is not set JDocument defaults to JDocumentRAW??? Is Koowa responsible?
		if (JPluginHelper::getPlugin('system', 'koowa')) {
			JRequest::setVar('format', 'raw');
		}	
		
		// Get variables
		$cid 		= JRequest::getInt('cid');
		$raw		= JRequest::getVar('json_data');

		// Try globals array
		if (!$raw && isset($_GLOBALS) && isset($_GLOBALS["HTTP_RAW_POST_DATA"]))
			$raw = $_GLOBALS["HTTP_RAW_POST_DATA"];
		
		// Try globals variable
		if (!$raw && isset($HTTP_RAW_POST_DATA))
			$raw = $HTTP_RAW_POST_DATA;
		
		// Try stream
		if (!$raw) {
			if (!function_exists('file_get_contents')) {
				$fp = fopen("php://input", "r");
				if ($fp) {
					$raw = "";
		
					while (!feof($fp))
						$raw = fread($fp, 1024);
		
					fclose($fp);
				}
			} else
				$raw = "" . file_get_contents("php://input");
		}
		
		$raw = $this->json_decode($raw);

		$query = 'SELECT *'
		. ' FROM #__components'
		. ' WHERE id = '.(int) $cid
		;
		$db->setQuery($query);
		$component = $db->loadObject();
		
		$params  	=& JComponentHelper::getParams($component->option);
		$article 	= new JObject();
		
		$article->id			= 0;
		$article->state			= 1;
		$article->cat_pub		= null;
		$article->sec_pub		= null;
		$article->cat_access	= null;
		$article->sec_access	= null;
		$article->author		= null;
		$article->created_by	= $user->get('id');
		$article->parameters	= new JParameter('');
		$article->text			= $raw->params;
		
		$limitstart = 0;
		JPluginHelper::importPlugin('content');
		
		$results = $dispatcher->trigger('onPrepareContent', array (& $article, & $params, $limitstart));
		
		$results = $dispatcher->trigger('onAfterDisplayTitle', array (&$article, &$params, $limitstart));

		$results = $dispatcher->trigger('onBeforeDisplayContent', array (&$article, &$params, $limitstart));

		$results = $dispatcher->trigger('onAfterDisplayContent', array (&$article, &$params, $limitstart));
		
		$this->processURLS($article);

		echo $this->json_encode(array('result'	=>	$article->text));
	}
	/**
	 * Convert URLs
	 * @param object $article Article object
	 * @return void
	 */
	function processURLS(&$article)
	{
		$base 		= JURI::root(true).'/';
		$buffer 	= $article->text;
		
		$protocols 	= '[a-zA-Z0-9]+:'; //To check for all unknown protocals (a protocol must contain at least one alpahnumeric fillowed by :
      	$regex     	= '#(src|href)="(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
        $buffer    	= preg_replace($regex, "$1=\"$base\$2\"", $buffer);
		$regex     	= '#(onclick="window.open\(\')(?!/|'.$protocols.'|\#)([^/]+[^\']*?\')#m';
		$buffer    	= preg_replace($regex, '$1'.$base.'$2', $buffer);
		
		// ONMOUSEOVER / ONMOUSEOUT
		$regex 		= '#(onmouseover|onmouseout)="this.src=([\']+)(?!/|'.$protocols.'|\#|\')([^"]+)"#m';
		$buffer 	= preg_replace($regex, '$1="this.src=$2'. $base .'$3$4"', $buffer);
		
		// Background image
		$regex 		= '#style\s*=\s*[\'\"](.*):\s*url\s*\([\'\"]?(?!/|'.$protocols.'|\#)([^\)\'\"]+)[\'\"]?\)#m';
		$buffer 	= preg_replace($regex, 'style="$1: url(\''. $base .'$2$3\')', $buffer);
		
		// OBJECT <param name="xx", value="yy"> -- fix it only inside the <param> tag
		$regex 		= '#(<param\s+)name\s*=\s*"(movie|src|url)"[^>]\s*value\s*=\s*"(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
		$buffer 	= preg_replace($regex, '$1name="$2" value="' . $base . '$3"', $buffer);
		
		// OBJECT <param value="xx", name="yy"> -- fix it only inside the <param> tag
		$regex 		= '#(<param\s+[^>]*)value\s*=\s*"(?!/|'.$protocols.'|\#|\')([^"]*)"\s*name\s*=\s*"(movie|src|url)"#m';
		$buffer 	= preg_replace($regex, '<param value="'. $base .'$2" name="$3"', $buffer);

		// OBJECT data="xx" attribute -- fix it only in the object tag
		$regex 		= '#(<object\s+[^>]*)data\s*=\s*"(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
		$buffer 	= preg_replace($regex, '$1data="' . $base . '$2"$3', $buffer);
		
		$article->text = $buffer;
	}
}