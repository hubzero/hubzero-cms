<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Editor Image buton
 */
class plgButtonImage extends \Hubzero\Plugin\Plugin
{
	/**
	 * Constructor
	 *
	 * @param   object  $subject  The object to observe
	 * @param   array   $config   An array that holds the plugin configuration
	 * @since   1.5
	 * @return  void
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Display the button
	 *
	 * @param   string   $name
	 * @param   string   $asset
	 * @param   integer  $author
	 * @return  array    A two element array of (imageName, textToInsert)
	 */
	public function onDisplay($name, $asset, $author)
	{
		$params = Component::params('com_media');
		$extension = Request::getCmd('option');

		if ($asset == '')
		{
			$asset = $extension;
		}

		if (User::authorise('core.edit', $asset)
			|| User::authorise('core.create', $asset)
			|| (count(User::getAuthorisedCategories($asset, 'core.create')) > 0)
			|| (User::authorise('core.edit.own', $asset) && $author == User::get('id'))
			|| (count(User::getAuthorisedCategories($extension, 'core.edit')) > 0)
			|| (count(User::getAuthorisedCategories($extension, 'core.edit.own')) > 0 && $author == User::get('id'))
		)
		{
			$link = 'index.php?option=com_media&amp;layout=list&amp;tmpl=component&amp;e_name=' . $name . '&amp;asset=' . $asset . '&amp;author=' . $author;
			Html::behavior('modal');

			$button = new \Hubzero\Base\Obj;
			$button->set('modal', true);
			$button->set('link', $link);
			$button->set('text', Lang::txt('PLG_IMAGE_BUTTON_IMAGE'));
			$button->set('name', 'image');
			$button->set('options', "{handler: 'iframe', size: {x: 800, y: 500}}");

			return $button;
		}

		return false;
	}
}
