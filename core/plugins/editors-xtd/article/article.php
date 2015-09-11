<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Editor Article buton
 */
class plgButtonArticle extends \Hubzero\Plugin\Plugin
{
	/**
	 * Constructor
	 *
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Display the button
	 *
	 * @return array A four element array of (article_id, article_title, category_id, object)
	 */
	public function onDisplay($name)
	{
		// Javascript to insert the link
		// View element calls jSelectArticle when an article is clicked
		// jSelectArticle creates the link tag, sends it to the editor,
		// and closes the select frame.
		$js = "
		function jSelectArticle(id, title, catid, object, link, lang) {
			var hreflang = '';
			if (lang !== '') {
				var hreflang = ' hreflang = \"' + lang + '\"';
			}
			var tag = '<a' + hreflang + ' href=\"' + link + '\">' + title + '</a>';
			jInsertEditorText(tag, '".$name."');
			$.fancybox.close();
		}";

		Document::addScriptDeclaration($js);

		Html::behavior('modal');

		// Use the built-in element view to select the article.
		// Currently uses blank class.
		$link = 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;' . Session::getFormToken() . '=1';

		$button = new \Hubzero\Base\Object();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', Lang::txt('PLG_ARTICLE_BUTTON_ARTICLE'));
		$button->set('name', 'article');
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");

		return $button;
	}
}
