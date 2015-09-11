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

$subdirlink = $this->subdir ? '&amp;subdir=' . urlencode($this->subdir) : '';

?>
<div id="abox-content">
<?php
// Display error or success message
if ($this->getError()) { ?>
	<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_COMPILED_PREVIEW'); ?></h3>
	<?php
	echo ('<p class="witherror">' . $this->getError() . '</p>');

	echo '<div class="witherror"><pre>';
	if (!empty($this->log))
	{
		echo $this->log;
	}
	echo '</pre></div>';
}
?>
<?php
if (!$this->getError()) {
?>
<ul class="sample">
	<?php

		$extras = NULL;
		if ($this->file->get('ext') == 'tex' && is_file(PATH_APP . $this->outputDir . DS . $this->embed))
		{
			$extras  = '<span class="rightfloat">';
			$extras .= '<a href="' . $this->url . '/?action=compile' . $subdirlink . '&amp;download=1&amp;asset=' . $this->file->get('name') . '" class="i-download">' . Lang::txt('PLG_PROJECTS_FILES_DOWNLOAD') . ' PDF</a> ';
			$extras .= '<a href="' . $this->url . '/?action=compile' . $subdirlink . '&amp;commit=1&amp;asset=' . $this->file->get('name') . '" class="i-commit">' . Lang::txt('PLG_PROJECTS_FILES_COMMIT_INTO_REPO') . '</a>';
			$extras .= '</span>';
		}

		// Display list item with file data
		$this->view('default', 'selected')
		     ->set('skip', false)
		     ->set('file', $this->file)
		     ->set('action', 'compile')
		     ->set('multi', NULL)
		     ->set('extras', $extras)
		     ->display();
	?>
</ul>
<?php } ?>
<?php if (!empty($this->data) && $this->cType != 'application/pdf') {
	// Clean up data from Windows characters - important!
	$this->data = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $this->data);
?>
	<pre><?php echo htmlentities($this->data); ?></pre>
<?php } elseif ($this->embed && file_exists(PATH_APP . $this->outputDir . DS . $this->embed)) {
		$source = Route::url('index.php?option=' . $this->option . '&controller=media&alias=' . $this->model->get('alias') . '&media=Compiled:' . $this->embed );
	?>
	<div id="compiled-doc" embed-src="<?php echo $source; ?>" embed-width="<?php echo $this->oWidth; ?>" embed-height="<?php echo $this->oHeight; ?>">
	  <object width="<?php echo $this->oWidth; ?>" height="<?php echo $this->oHeight; ?>" type="<?php echo $this->cType; ?>" data="<?php echo $source; ?>" id="pdf_content">
		<embed src="<?php echo $source; ?>" type="application/pdf" />
		<p><?php echo Lang::txt('PLG_PROJECTS_FILES_PREVIEW_NOT_LOAD'); ?> <a href="<?php echo $this->url . '/?' . 'action=compile' . $subdirlink . '&amp;download=1&amp;file=' . $this->file->get('name'); ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_DOWNLOAD_FILE'); ?></a>
		<?php if ($this->image) { ?>
			<img alt="" src="<?php echo Route::url('index.php?option=' . $this->option . '&task=media&alias=' . $this->model->get('alias') . '&media=Compiled:' . $this->image ); ?>" />
		<?php } ?>
		</p>
	  </object>
	</div>
<?php } ?>
</div>