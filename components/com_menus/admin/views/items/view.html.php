<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * The HTML Menus Menu Items View.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @version		1.6
 */
class MenusViewItems extends JViewLegacy
{
	protected $f_levels;
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$lang 		= Lang::getRoot();
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->ordering = array();

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as $item) {
			$this->ordering[$item->parent_id][] = $item->id;

			// item type text
			switch ($item->type) {
				case 'url':
					$value = Lang::txt('COM_MENUS_TYPE_EXTERNAL_URL');
					break;

				case 'alias':
					$value = Lang::txt('COM_MENUS_TYPE_ALIAS');
					break;

				case 'separator':
					$value = Lang::txt('COM_MENUS_TYPE_SEPARATOR');
					break;

				case 'component':
				default:
					// load language
						$lang->load($item->componentname . '.sys', JPATH_ADMINISTRATOR, null, false, true)
					||	$lang->load($item->componentname . '.sys', JPATH_ADMINISTRATOR . '/components/' . $item->componentname, null, false, true);

					if (!empty($item->componentname)) {
						$value	= Lang::txt($item->componentname);
						$vars	= null;

						parse_str($item->link, $vars);
						if (isset($vars['view'])) {
							// Attempt to load the view xml file.
							$file = JPATH_SITE.'/components/'.$item->componentname.'/views/'.$vars['view'].'/metadata.xml';
							if (Filesystem::exists($file) && $xml = simplexml_load_file($file)) {
								// Look for the first view node off of the root node.
								if ($view = $xml->xpath('view[1]')) {
									if (!empty($view[0]['title'])) {
										$vars['layout'] = isset($vars['layout']) ? $vars['layout'] : 'default';

										// Attempt to load the layout xml file.
										// If Alternative Menu Item, get template folder for layout file
										if (strpos($vars['layout'], ':') > 0)
										{
											// Use template folder for layout file
											$temp = explode(':', $vars['layout']);
											$file = JPATH_SITE.'/templates/'.$temp[0].'/html/'.$item->componentname.'/'.$vars['view'].'/'.$temp[1].'.xml';
											// Load template language file
												$lang->load('tpl_' . $temp[0] . '.sys', JPATH_SITE, null, false, true)
											||	$lang->load('tpl_' . $temp[0] . '.sys', JPATH_SITE . '/templates/' . $temp[0], null, false, true);

										}
										else
										{
											// Get XML file from component folder for standard layouts
											$file = JPATH_SITE.'/components/'.$item->componentname.'/views/'.$vars['view'].'/tmpl/'.$vars['layout'].'.xml';
										}
										if (Filesystem::exists($file) && $xml = simplexml_load_file($file)) {
											// Look for the first view node off of the root node.
											if ($layout = $xml->xpath('layout[1]')) {
												if (!empty($layout[0]['title'])) {
													$value .= ' » ' . Lang::txt(trim((string) $layout[0]['title']));
												}
											}
											if (!empty($layout[0]->message[0])) {
												$item->item_type_desc = Lang::txt(trim((string) $layout[0]->message[0]));
											}
										}
									}
								}
								unset($xml);
							}
							else {
								// Special case for absent views
								$value .= ' » ' . Lang::txt($item->componentname.'_'.$vars['view'].'_VIEW_DEFAULT_TITLE');
							}
						}
					}
					else {
						if (preg_match("/^index.php\?option=([a-zA-Z\-0-9_]*)/", $item->link, $result)) {
							$value = Lang::txt('COM_MENUS_TYPE_UNEXISTING', $result[1]);
						}
						else {
							$value = Lang::txt('COM_MENUS_TYPE_UNKNOWN');
						}
					}
					break;
			}
			$item->item_type = $value;
		}

		// Levels filter.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', Lang::txt('J1'));
		$options[]	= JHtml::_('select.option', '2', Lang::txt('J2'));
		$options[]	= JHtml::_('select.option', '3', Lang::txt('J3'));
		$options[]	= JHtml::_('select.option', '4', Lang::txt('J4'));
		$options[]	= JHtml::_('select.option', '5', Lang::txt('J5'));
		$options[]	= JHtml::_('select.option', '6', Lang::txt('J6'));
		$options[]	= JHtml::_('select.option', '7', Lang::txt('J7'));
		$options[]	= JHtml::_('select.option', '8', Lang::txt('J8'));
		$options[]	= JHtml::_('select.option', '9', Lang::txt('J9'));
		$options[]	= JHtml::_('select.option', '10', Lang::txt('J10'));

		$this->f_levels = $options;

		parent::display($tpl);
		$this->addToolbar();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/menus.php';

		$canDo = MenusHelper::getActions($this->state->get('filter.parent_id'));

		Toolbar::title(Lang::txt('COM_MENUS_VIEW_ITEMS_TITLE'), 'menumgr.png');

		if ($canDo->get('core.create')) {
			Toolbar::addNew('item.add');
		}
		if ($canDo->get('core.edit')) {
			Toolbar::editList('item.edit');
		}
		if ($canDo->get('core.edit.state')) {
			Toolbar::divider();
			Toolbar::publish('items.publish', 'JTOOLBAR_PUBLISH', true);
			Toolbar::unpublish('items.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}
		if (User::authorise('core.admin')) {
			Toolbar::divider();
			Toolbar::checkin('items.checkin', 'JTOOLBAR_CHECKIN', true);
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			Toolbar::deleteList('', 'items.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state')) {
			Toolbar::trash('items.trash');
		}

		if ($canDo->get('core.edit.state')) {
			Toolbar::makeDefault('items.setDefault', 'COM_MENUS_TOOLBAR_SET_HOME');
			Toolbar::divider();
		}
		if (User::authorise('core.admin')) {
			Toolbar::custom('items.rebuild', 'refresh.png', 'refresh_f2.png', 'JToolbar_Rebuild', false);
			Toolbar::divider();
		}
		Toolbar::help('items');
	}
}
