<?php
/**
 * @package		Joomla.Administrator
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Renders an article element
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @deprecated	JParameter is deprecated and will be removed in a future version. Use JForm instead.
 * @since		1.5
 */
class JElementArticle extends JElement
{
	/**
	 * Element name
	 *
	 * @var	string
	 */
	var $_name = 'Article';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$template  = App::get('template')->template;
		$fieldName = $control_name.'['.$name.']';

		$article = JTable::getInstance('content');
		if ($value)
		{
			$article->load($value);
		}
		else
		{
			$article->title = Lang::txt('COM_CONTENT_SELECT_AN_ARTICLE');
		}

		$js = "
		function jSelectArticle_".$name."(id, title, catid, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			SqueezeBox.close();
		}";
		Document::addScriptDeclaration($js);

		$link = 'index.php?option=com_content&amp;task=element&amp;tmpl=component&amp;function=jSelectArticle_'.$name;

		Html::behavior('modal', 'a.modal');
		$html = "\n".'<div class="fltlft"><input type="text" id="'.$name.'_name" value="'.htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		//$html .= "\n &#160; <input class=\"inputbox modal-button\" type=\"button\" value=\"".Lang::txt('JSELECT')."\" />";
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.Lang::txt('COM_CONTENT_SELECT_AN_ARTICLE').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.Lang::txt('JSELECT').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}
}
