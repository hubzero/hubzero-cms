<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
						<a <?php echo $option->class ? 'class="' . $option->class . '"' : ''; ?> <?php echo isset($option->attrs) ? $option->attrs : ''; ?> href="<?php echo $option->href; ?>"><?php echo $option->title; ?></a>
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
<?php endif; 