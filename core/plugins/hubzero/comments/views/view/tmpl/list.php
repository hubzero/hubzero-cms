<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->comments) { ?>
	<ol class="comments">
		<?php
		$cls = 'odd';
		if (isset($this->cls))
		{
			$cls = ($this->cls == 'odd') ? 'even' : 'odd';
		}

		$this->depth++;

		foreach ($this->comments as $comment)
		{
			$this->view('item')
			     ->set('option', $this->option)
			     ->set('comment', $comment)
			     ->set('obj_type', $this->obj_type)
			     ->set('obj_id', $this->obj_id)
			     ->set('obj', $this->obj)
			     ->set('params', $this->params)
			     ->set('depth', $this->depth)
			     ->set('cls', $cls)
			     ->set('url', $this->url)
			     ->display();
		}
		?>
	</ol>
<?php }
