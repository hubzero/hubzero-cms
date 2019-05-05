<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

foreach ($this->items as $item)
{
	$this->view('_item')
		 ->set('option', $this->option)
		 ->set('model', $this->model)
		 ->set('subdir', $this->subdir)
		 ->set('item', $item)
		 ->set('repo', $this->repo)
		 ->set('params', $this->params)
		 ->set('fileparams', $this->fileparams)
		 ->set('publishing', $this->publishing)
		 ->display();
}
