<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */
// no direct access
defined('_HZEXEC_') or die();

$url = Route::url($this->publication->link('edit'));

?>
<div id="abox-content" class="handler-wrap">
	<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_HANDLER') . ' - ' . $this->editor->configs->label; ?></h3>
	<?php
	// Display error  message
	if ($this->getError()) {
		echo ('<p class="error">' . $this->getError() . '</p>');
	} else { // No error
	?>
	<form id="<?php echo $this->ajax ? 'hubForm-ajax' : 'plg-form'; ?>" method="post" action="<?php echo $url; ?>">
	<div id="handler-status" class="handler-status">
		<?php echo $this->editor->drawStatus(); ?>
	</div>
	<div id="handler-content" class="handler-content">
		<?php echo $this->editor->drawEditor(); ?>
	</div>
	</form>
	<?php } ?>
</div>