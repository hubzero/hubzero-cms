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

if ($this->disabled): ?>
	<p id="primary-document">
		<span class="btn disabled <?php echo $this->class; ?>"><?php echo $this->msg; ?></span>
	</p>
<?php else: ?>
	<?php if (isset($this->options) && !empty($this->options)): ?>
		<div class="btn-group btn-primary" id="primary-document">
			<a class="btn <?php echo ($this->class)  ? ' ' . $this->class : ''; ?>" <?php
					echo ($this->href)   ? ' href="' . $this->href . '"' : '';
					echo ($this->title)  ? ' title="' . $this->escape($this->title) . '"' : '';
					echo ($this->action) ? ' ' . $this->action : '';
				?>><?php echo $this->msg; ?></a>
			<span class="btn dropdown-toggle"></span>
			<ul class="dropdown-menu">
				<?php foreach ($this->options as $option): ?>
					<li>
						<a <?php echo ($option->class ? 'class="' . $option->class . '"' : ''); ?> <?php echo (isset($option->attrs) ? $option->attrs : ''); ?> href="<?php echo $option->href; ?>"><?php echo $option->title; ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php else: ?>
		<p id="primary-document">
			<a class="btn btn-primary<?php echo ($this->class)  ? ' ' . $this->class : ''; ?>" <?php
					echo ($this->href)   ? ' href="' . $this->href . '"' : '';
					echo ($this->title)  ? ' title="' . $this->escape($this->title) . '"' : '';
					echo ($this->action) ? ' ' . $this->action : '';
				?>><?php echo $this->msg; ?></a>
		</p>
	<?php endif; ?>
<?php endif; ?>

<?php if ($this->pop): ?>
	<div id="primary-document_pop">
		<div><?php echo $this->pop; ?></div>
	</div>
<?php endif; ?>