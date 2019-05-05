<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Editor Readmore buton
 */
class plgButtonReadmore extends \Hubzero\Plugin\Plugin
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
	 * readmore button
	 *
	 * @param  string  $name  Value of name
	 * @return  array  A two element array of (imageName, textToInsert)
	 */
	public function onDisplay($name)
	{
		$template = App::get('template')->template;

		// button is not active in specific content components
		$getContent = $this->_subject->getContent($name);
		$present = Lang::txt('PLG_READMORE_ALREADY_EXISTS', true);
		$js = "
			function insertReadmore(editor) {
				var content = $getContent
				if (content.match(/<hr\s+id=(\"|')system-readmore(\"|')\s*\/*>/i)) {
					alert('$present');
					return false;
				} else {
					jInsertEditorText('<hr id=\"system-readmore\" />', editor);
				}
			}
			";

		Document::addScriptDeclaration($js);

		$button = new \Hubzero\Base\Obj;
		$button->set('modal', false);
		$button->set('onclick', 'insertReadmore(\''.$name.'\');return false;');
		$button->set('text', Lang::txt('PLG_READMORE_BUTTON_READMORE'));
		$button->set('name', 'readmore');
		// TODO: The button writer needs to take into account the javascript directive
		//$button->set('link', 'javascript:void(0)');
		$button->set('link', '#');

		return $button;
	}
}
