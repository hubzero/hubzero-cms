<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * @param  string  $name  Editor name?
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

		$button = new \Hubzero\Base\Obj();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', Lang::txt('PLG_ARTICLE_BUTTON_ARTICLE'));
		$button->set('name', 'article');
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");

		return $button;
	}
}
