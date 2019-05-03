<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

jimport('joomla.application.component.view');

/**
 * User factors view class
 */
class UsersViewFactors extends JViewLegacy
{
	function display($tpl = null)
	{
		$this->factors = Event::trigger('authfactors.onRenderChallenge');

		parent::display($tpl);
	}
}
