<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<div id="plg-header">
	<h3 class="newsupdate"><?php echo $this->title; ?></h3>
</div>

<?php
	// New update form
	$this->view('default', 'addupdate')
	     ->set('option', $this->option)
	     ->set('model', $this->model)
	     ->display();
?>

<div id="latest_activity" class="infofeed" data-frequency="60" data-base="<?php echo Route::url($this->model->link() . '&active=feed'); ?>">
	<?php
	// Display item list
	$this->view('default', 'activity')
	     ->set('option', $this->option)
	     ->set('model', $this->model)
	     ->set('activities', $this->activities)
	     ->set('limit', $this->limit)
	     ->set('total', $this->total)
	     ->set('filters', $this->filters)
	     ->set('uid', $this->uid)
	     ->set('database', $this->database)
	     ->display();
	?>

	<form id="hubForm" method="post" action="<?php echo Route::url($this->model->link()); ?>">
		<div>
			<input type="hidden" id="pid" name="id" value="<?php echo $this->model->get('id'); ?>" />
			<input type="hidden" name="task" value="view" />
			<input type="hidden" name="action" value="" />
		</div>
	</form>
</div>