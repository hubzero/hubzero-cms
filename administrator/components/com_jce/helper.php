<?php
/**
* @version		$Id: helper.php 75 2009-06-04 14:00:32Z happynoodleboy $
* @package		JCE Admin Component
* @copyright	Copyright (C) 2006 - 2009 Ryan Demmmer. All rights reserved.
* @license		GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
class JCEHelper 
{			
	function getOrderArray($input, $listname, $itemKeyName = 'element', $orderKeyName = 'order') 
	{
		parse_str($input, $inputArray);
		$inputArray = $inputArray[$listname];
		$orderArray = array();
		for ($i=0; $i<count($inputArray); $i++) {
			$orderArray[] = array($itemKeyName => $inputArray[$i], $orderKeyName => $i +1);
		}
		return $orderArray;
	}
	function getAccessName($id)
	{
		$db =& JFactory::getDBO();
		// get list of Groups for dropdown filter
		$query = 'SELECT name'
		. ' FROM #__core_acl_aro_groups'
		. ' WHERE id = '. $id
		. ' AND name != "ROOT"'
		. ' AND name != "USERS"'
		;
		$db->setQuery($query);
		return $db->loadResult();
	}
	function accessList($name, $access = '', $size = 1, $extra)
	{
		$db =& JFactory::getDBO();
		// get list of Groups for dropdown filter
		$query = 'SELECT id AS value, name AS text'
		. ' FROM #__core_acl_aro_groups'
		. ' WHERE id NOT IN (28,29,30)'
		;
		$db->setQuery($query);
		$types[] = JHTML::_('select.option',  '0', '- '. JText::_('Select Access') .' -');
		$i = '-';
		foreach ($db->loadObjectList() as $obj) {
			$types[] = JHTML::_('select.option', $obj->value, $i . JText::_($obj->text));
			$i .= '-';
		}
		return JHTML::_('select.genericlist', $types, $name, 'class="inputbox" size="'. $size .'"'. $extra, 'value', 'text', $access);
	}
	function quickiconButton($link, $image, $text, $disabled = false)
	{
		global $mainframe;
		$lang		=& JFactory::getLanguage();
		$template	= $mainframe->getTemplate();
		
		if ($disabled) {
			$link = '#';
		}				
		?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
					<?php echo JHTML::_('image.site',  $image, '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
        <?php
	}
	function getLanguage()
	{
		$language =& JFactory::getLanguage();
		$tag = $language->getTag();
		if (file_exists(JPATH_SITE .DS. 'language' .DS. $tag .DS. $tag .'.com_jce.xml')) {
			return substr($tag, 0, strpos($tag, '-'));
		}
		return 'en';
	}  
}
class jceToolbarHelper extends JToolbarHelper 
{
	function access($alt = 'Plugin Access')
	{
		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton('Popup', 'lock', $alt, "index.php?option=com_jce&tmpl=component&type=plugin&task=access_popup", 400, 150);
	}
	function popup($alt, $icon, $type, $task, $width = 750, $height = 400)
	{		
		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton('Popup', $icon, $alt, "index.php?option=com_jce&tmpl=component&type=".$type."&task=".$task, $width, $height);
	}
	function config($alt = 'Editor Config')
	{
		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton('Popup', 'config', $alt, "index.php?option=com_jce&tmpl=component&type=config&task=view", 700, 560);
	}
	function help($type, $alt = 'Help')
	{
		jimport('joomla.plugin.helper');
		$plugin = JPluginHelper::getPlugin('editors', 'jce');
		
		$url = 'http://www.joomlacontenteditor.net/index.php?option=com_content&tmpl=component&view=article&task=findkey';
				
		if (isset($plugin->params)) {
        	$params = new JParameter($plugin->params);
			$url = $params->get('help', $url);
		}	
		if(strpos($type, '.') === false){
			$type = $type . '.view';
		}
		$type = $type[0];	
		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton('Popup', 'help', $alt, $url. '&lang=' .JCEHelper::getLanguage(). '&keyref=admin.' .$type, 700, 560);
	}
}
?>