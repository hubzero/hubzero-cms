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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$nextstep = $this->step+1;
$task = ($nextstep == 5) ? 'preview' : 'start';
$dev = ($this->version == 'dev') ? 1: 0;

if ($this->version == 'dev') {
	$v = ($this->status['version'] && $this->status['version'] != $this->status['currentversion']) ? $this->status['version'] : '';
}
else {
	$v = $this->status['version'];
}

$this->css('resource.css')
     ->js('resource.js');
?>
<header id="content-header">
	<h2><?php echo $this->escape($this->title); ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-status btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=pipeline&task=status&app=' . $this->row->alias); ?>"><?php echo Lang::txt('COM_TOOLS_TOOL_STATUS'); ?></a></li>
			<li><a class="icon-add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=pipeline&task=create'); ?>"><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="section steps-section">
	<?php
	$this->view('stage')
	     ->set('stage', $this->step)
	     ->set('option', $this->option)
	     ->set('controller', $this->controller)
	     ->set('version', $this->version)
	     ->set('row', $this->row)
	     ->set('status', $this->status)
	     ->set('vnum', $v)
	     ->display();
	?>
</section>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>

		<div style="float:left; width:70%;padding:1em 0 1em 0;">
		<?php if ($this->step !=1 ) { ?>
			<span style="float:left;"><input type="button" value=" &lt; <?php echo ucfirst(Lang::txt('COM_TOOLS_PREVIOUS')); ?> " class="btn returntoedit" /></span>
		<?php } ?>
			<span style="float:right;"><input class="btn" type="submit" value="<?php echo ucfirst(Lang::txt('COM_TOOLS_SAVE_AND_GO_NEXT')); ?>" /></span>
		</div>
		<div class="clear"></div>

		<?php
			switch ($this->step)
			{
				//  registered
				case 1:
					$layout = 'compose';
				break;
				case 2:
					$layout = 'authors';
				break;
				case 3:
					$layout = 'attach';
				break;
				case 4:
					$layout = 'tags';
				break;
			}
			$this->view($layout)
			     ->set('step', $this->step)
			     ->set('option', $this->option)
			     ->set('controller', $this->controller)
			     ->set('version', $this->version)
			     ->set('row', $this->row)
			     ->set('status', $this->status)
			     ->set('dev', $dev)
			     ->set('tags', $this->tags)
			     ->set('tagfa', $this->tagfa)
			     ->set('fats', $this->fats)
			     ->set('authors', $this->authors)
			     ->display();
		?>

		<input type="hidden" name="app" value="<?php echo $this->row->alias; ?>" />
		<input type="hidden" name="rid" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="<?php echo $task; ?>" />
		<input type="hidden" name="step" value="<?php echo $nextstep; ?>" />
		<input type="hidden" name="editversion" value="<?php echo $this->version; ?>" />
		<input type="hidden" name="toolname" value="<?php echo $this->status['toolname']; ?>" />
		<?php echo Html::input('token'); ?>

		<div style="float:left; width:70%;padding:1em 0 1em 0;">
			<?php if ($this->step != 1) { ?>
				<span style="float:left;"><input type="button" value=" &lt; <?php echo ucfirst(Lang::txt('COM_TOOLS_PREVIOUS')); ?> " class="btn returntoedit" /></span>
			<?php } ?>
			<span style="float:right;"><input class="btn" type="submit" value="<?php echo ucfirst(Lang::txt('COM_TOOLS_SAVE_AND_GO_NEXT')); ?>" /></span>
		</div>
		<div class="clear"></div>
	</form>
</section>