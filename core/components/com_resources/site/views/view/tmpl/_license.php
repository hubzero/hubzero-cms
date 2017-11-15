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

// No direct access.
defined('_HZEXEC_') or die();

$name = $this->license->name;

if (substr($name, 0, 6) != 'custom')
{
	?>
	<p class="<?php echo $name; ?> license">
		<?php if ($this->license->url) { ?>
			Licensed according to <a rel="license" href="<?php echo $this->license->url; ?>" title="<?php echo $this->escape($this->license->title); ?>">this deed</a>.
		<?php } else { ?>
			Licensed under <?php echo $this->license->title; ?>
		<?php } ?>
	</p>
	<?php
} else {
	?>
	<p class="<?php echo $name; ?> license">
		Licensed according to <a rel="license" class="popup" href="<?php echo Route::url('index.php?option=com_resources&task=license&resource=' . substr($name, 6) . '&no_html=1'); ?>">this deed</a>.
	</p>
	<?php
}
