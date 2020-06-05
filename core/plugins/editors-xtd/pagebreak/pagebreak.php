<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Editor Pagebreak buton
 */
class plgButtonPagebreak extends \Hubzero\Plugin\Plugin
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
	 * @param  string  $name  Value of name
	 * @return array A two element array of (imageName, textToInsert)
	 */
	public function onDisplay($name)
	{
		$template = App::get('template')->template;

		$link = 'index.php?option=com_content&amp;view=article&amp;layout=pagebreak&amp;tmpl=component&amp;e_name='.$name;

		Html::behavior('modal');

		$button = new \Hubzero\Base\Obj;
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', Lang::txt('PLG_EDITORSXTD_PAGEBREAK_BUTTON_PAGEBREAK'));
		$button->set('name', 'pagebreak');
		$button->set('options', "{handler: 'iframe', size: {x: 400, y: 100}}");

		return $button;
	}
}
