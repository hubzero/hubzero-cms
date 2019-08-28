<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SEARCH_HEADING_BOOSTS'));
Toolbar::addNew();
Toolbar::deleteList();
Toolbar::spacer();
Toolbar::preferences($this->option, '550');

$boosts = $this->boosts;

$this->view('_submenu', 'shared')
	->display();

$this->view('_boosts_list')
	->set('boosts', $boosts)
	->display();
