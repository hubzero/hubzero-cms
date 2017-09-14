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

// No direct access
defined('_HZEXEC_') or die();

$privacyTxt = !$this->model->isPublic()
	? Lang::txt('COM_PROJECTS_PRIVATE')
	: Lang::txt('COM_PROJECTS_PUBLIC');

if (!$this->model->isPublic())
{
	$privacy = '<span class="private">' . ucfirst($privacyTxt) . '</span>';
}
else
{
	$privacy = '<a href="' . Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&preview=1') . '" title="' . Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE') . '">' . ucfirst($privacyTxt) . '</a>';
}

$start = ($this->showPrivacy == 2 && $this->model->access('member')) ? '<span class="h-privacy">' . $privacy . '</span> ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) : ucfirst(Lang::txt('COM_PROJECTS_PROJECT'));
?>
<div id="content-header" <?php if (!$this->showPic) { echo 'class="nopic"'; } ?>>
	<?php if ($this->showPic)
	{
		// Check if there is a picture
		$thumbClass = '';
		if (!$this->model->get('picture'))
		{
			$thumbClass = ' no-picture';
		}
	?>
		<div class="pthumb<?php echo $thumbClass; ?>"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>" title="<?php echo Lang::txt('COM_PROJECTS_VIEW_UPDATES'); ?>"><img src="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&controller=media&media=thumb'); ?>" alt="<?php echo $this->escape($this->model->get('title')); ?>" /></a></div>
	<?php } ?>
	<div class="ptitle">
	<h2>
		<a href="<?php echo Route::url($this->model->link()); ?>"><?php echo \Hubzero\Utility\String::truncate($this->escape($this->model->get('title')), 50); ?></a>
	</h2>
	<?php // Member options
	if (!empty($this->showOptions))
	{
		$this->view('_options', 'projects')
		     ->set('model', $this->model)
		     ->set('option', $this->option)
		     ->display();
	}
	?>
	<?php if ($this->model->groupOwner())
	{
		echo '<p class="groupowner">';
		echo ucfirst(Lang::txt('COM_PROJECTS_PROJECT'));
		echo ' ' . Lang::txt('COM_PROJECTS_BY') . ' ';
		if ($cn = $this->model->groupOwner('cn'))
		{
			echo ' ' . Lang::txt('COM_PROJECTS_GROUP')
				. ' <a href="/groups/' . $cn . '">' . $cn . '</a>';
		}
		else
		{
			echo Lang::txt('COM_PROJECTS_UNKNOWN') . ' ' . Lang::txt('COM_PROJECTS_GROUP');
		}
		echo '</p>';

	 } ?>
	</div>
</div>
