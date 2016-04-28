<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * HTML Article View class for the Content component
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since		1.5
 */
class ContentViewForm extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $return_page;
	protected $state;

	public function display($tpl = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get model data.
		$this->state       = $this->get('State');
		$this->item        = $this->get('Item');
		$this->form        = $this->get('Form');
		$this->return_page = $this->get('ReturnPage');

		if (empty($this->item->id))
		{
			$authorised = User::authorise('core.create', 'com_content') || (count(User::getAuthorisedCategories('com_content', 'core.create')));
		}
		else
		{
			$authorised = $this->item->params->get('access-edit');
		}

		if ($authorised !== true) {
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		if (!empty($this->item) && isset($this->item->id))
		{
			$this->item->images = json_decode($this->item->images);
			$this->item->urls   = json_decode($this->item->urls);

			$tmp = new stdClass;
			$tmp->images = $this->item->images;
			$tmp->urls = $this->item->urls;
			$this->form->bind($tmp);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		// Create a shortcut to the parameters.
		$params	= &$this->state->params;

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->params = $params;
		$this->user   = User::getInstance();

		if ($params->get('enable_category') == 1)
		{
			$this->form->setFieldAttribute('catid', 'default',  $params->get('catid', 1));
			$this->form->setFieldAttribute('catid', 'readonly', 'true');
		}
		$this->_prepareDocument();
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app   = JFactory::getApplication();
		$menus = \App::get('menu');
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Lang::txt('COM_CONTENT_FORM_EDIT_ARTICLE'));
		}

		$title = $this->params->def('page_title', Lang::txt('COM_CONTENT_FORM_EDIT_ARTICLE'));
		if (Config::get('sitename_pagetitles', 0) == 1)
		{
			$title = Lang::txt('JPAGETITLE', Config::get('sitename'), $title);
		}
		elseif (Config::get('sitename_pagetitles', 0) == 2)
		{
			$title = Lang::txt('JPAGETITLE', $title, Config::get('sitename'));
		}
		$this->document->setTitle($title);

		Pathway::append($title, '');

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
