<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Members admin component helper.
 *
 * NOTE: This class isn't namespaced to allow for use by some core
 * components. When those components are rewritten, this
 * class should get updated.
 */
class MembersHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 * @return  void
	 */
	public static function addSubmenu($vName)
	{
		require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'admin.php';

		// Groups and Levels are restricted to core.admin
		$canDo = Components\Members\Helpers\Admin::getActions();

		$controllerName = Request::getCmd('controller', $vName);

		Submenu::addEntry(
			Lang::txt('COM_MEMBERS'),
			Route::url('index.php?option=com_members'),
			$controllerName == 'members'
		);
		Submenu::addEntry(
			Lang::txt('COM_MEMBERS_MENU_NOTES'),
			Route::url('index.php?option=com_members&controller=notes'),
			($controllerName == 'notes' || $vName == 'categories')
		);
		if ($controllerName == 'notes' || $vName == 'categories')
		{
			App::set('subsubmenu', function($app)
			{
				return new Hubzero\Html\Toolbar('subsubmenu');
			});

			App::get('subsubmenu')->appendButton(
				Lang::txt('COM_MEMBERS_SUBMENU_NOTES'),
				Route::url('index.php?option=com_members&controller=notes'),
				$controllerName == 'notes'
			);
			App::get('subsubmenu')->appendButton(
				Lang::txt('COM_MEMBERS_SUBMENU_NOTE_CATEGORIES'),
				Route::url('index.php?option=com_categories&extension=com_members'),
				$vName == 'categories'
			);
		}
		Submenu::addEntry(
			Lang::txt('COM_MEMBERS_MENU_ACCESS'),
			Route::url('index.php?option=com_members&controller=accessgroups'),
			($controllerName == 'accessgroups' || $controllerName == 'accesslevels')
		);
		/*Submenu::addEntry(
			Lang::txt('COM_MEMBERS_MENU_MESSAGING'),
			Route::url('index.php?option=com_members&controller=messages'),
			$controllerName == 'messages'
		);*/
		if (Component::params('com_members')->get('bankAccounts'))
		{
			Submenu::addEntry(
				Lang::txt('COM_MEMBERS_MENU_POINTS'),
				Route::url('index.php?option=com_members&controller=points'),
				$controllerName == 'points'
			);
		}
		if ($canDo->get('core.admin'))
		{
			Submenu::addEntry(
				Lang::txt('COM_MEMBERS_MENU_PASSWORDS'),
				Route::url('index.php?option=com_members&controller=passwordrules'),
				($controllerName == 'passwordrules' || $controllerName == 'passwordblacklist')
			);
		}
		Submenu::addEntry(
			Lang::txt('COM_MEMBERS_MENU_QUOTAS'),
			Route::url('index.php?option=com_members&controller=quotas'),
			$controllerName == 'quotas'
		);
		Submenu::addEntry(
			Lang::txt('COM_MEMBERS_MENU_REGISTRATION'),
			Route::url('index.php?option=com_members&controller=registration'),
			(in_array($controllerName, array('registration', 'organizations', 'employers', 'incremental', 'premis')))
		);

		if ($canDo->get('core.admin'))
		{
			Submenu::addEntry(
				Lang::txt('COM_MEMBERS_MENU_IMPORT'),
				Route::url('index.php?option=com_members&controller=imports'),
				($controllerName == 'imports' || $controllerName == 'importhooks')
			);
		}
		Submenu::addEntry(
			Lang::txt('COM_MEMBERS_MENU_PLUGINS'),
			Route::url('index.php?option=com_members&controller=plugins'),
			$controllerName == 'plugins'
		);
	}
}
