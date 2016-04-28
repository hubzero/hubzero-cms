<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * HTML View class for the Content component
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class ContentViewArchive extends JViewLegacy
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;

	function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$state = $this->get('State');
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');

		// Get the page/component configuration
		$params = &$state->params;

		foreach ($items as $item)
		{
			$item->catslug = ($item->category_alias) ? ($item->catid . ':' . $item->category_alias) : $item->catid;
			$item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;
		}

		$form = new stdClass();
		// Month Field
		$months = array(
			''   => Lang::txt('COM_CONTENT_MONTH'),
			'01' => Lang::txt('JANUARY_SHORT'),
			'02' => Lang::txt('FEBRUARY_SHORT'),
			'03' => Lang::txt('MARCH_SHORT'),
			'04' => Lang::txt('APRIL_SHORT'),
			'05' => Lang::txt('MAY_SHORT'),
			'06' => Lang::txt('JUNE_SHORT'),
			'07' => Lang::txt('JULY_SHORT'),
			'08' => Lang::txt('AUGUST_SHORT'),
			'09' => Lang::txt('SEPTEMBER_SHORT'),
			'10' => Lang::txt('OCTOBER_SHORT'),
			'11' => Lang::txt('NOVEMBER_SHORT'),
			'12' => Lang::txt('DECEMBER_SHORT')
		);
		$form->monthField = Html::select(
			'genericlist',
			$months,
			'month',
			array(
				'list.attr' => 'size="1" class="inputbox"',
				'list.select' => $state->get('filter.month'),
				'option.key' => null
			)
		);
		// Year Field
		$years = array();
		$years[] = Html::select('option', null, Lang::txt('JYEAR'));
		for ($i = 2000; $i <= 2020; $i++)
		{
			$years[] = Html::select('option', $i, $i);
		}
		$form->yearField = Html::select(
			'genericlist',
			$years,
			'year',
			array('list.attr' => 'size="1" class="inputbox"', 'list.select' => $state->get('filter.year'))
		);
		$form->limitField = $pagination->getLimitBox();

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->filter = $state->get('list.filter');
		$this->assignRef('form', $form);
		$this->assignRef('items', $items);
		$this->assignRef('params', $params);
		$this->assignRef('user', User::getInstance());
		$this->assignRef('pagination', $pagination);

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$menus = \App::get('menu');
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', Lang::txt('JGLOBAL_ARTICLES'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title))
		{
			$title = Config::get('sitename');
		}
		elseif (Config::get('sitename_pagetitles', 0) == 1)
		{
			$title = Lang::txt('JPAGETITLE', Config::get('sitename'), $title);
		}
		elseif (Config::get('sitename_pagetitles', 0) == 2)
		{
			$title = Lang::txt('JPAGETITLE', $title, Config::get('sitename'));
		}
		$this->document->setTitle($title);

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

