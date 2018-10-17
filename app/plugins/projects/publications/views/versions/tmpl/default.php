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

// Get publication properties
$typetitle = \Components\Publications\Helpers\Html::writePubCategory($this->pub->category()->alias, $this->pub->category()->name);

?>
<form action="<?php echo Route::url($this->pub->link('editbase')); ?>" method="post" id="plg-form" >
	<div id="plg-header">
	<?php if ($this->project->isProvisioned()) { ?>
		<h3 class="prov-header"><a href="<?php echo Route::url($this->pub->link('editbase')); ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> &raquo; <a href="<?php echo Route::url($this->pub->link('editversion')); ?>">"<?php echo $this->pub->title; ?>"</a> &raquo; <?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSIONS')); ?></h3>
	<?php } else { ?>
		<h3 class="publications c-header"><a href="<?php echo Route::url($this->pub->link('editbase')); ?>"><?php echo $this->title; ?></a> &raquo; <span class="restype indlist"><?php echo $typetitle; ?></span> <span class="indlist"><a href="<?php echo Route::url($this->pub->link('editversion')); ?>">"<?php echo $this->pub->title; ?>"</a></span> &raquo; <span class="indlist"> &raquo; <?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSIONS')); ?></span>
		</h3>
	<?php } ?>
	</div>
	<div class="list-editing">
	 <p><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_TOTAL_VERSIONS')); ?>: <span class="prominent"><?php echo count($this->versions); ?></span></p>
	</div>
	<?php if ($this->versions) { ?>
		<table class="listing">
		 <thead>
			<tr>
				<th class="tdmini"></th>
				<th class="tdmini"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION'); ?></th>
				<th><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?></th>
				<th><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATUS'); ?></th>
				<th><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DOI').'/'.Lang::txt('PLG_PROJECTS_PUBLICATIONS_ARK'); ?></th>
				<th><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_OPTIONS')); ?></th>
			</tr>
		 </thead>
		 <tbody>
		<?php foreach ($this->versions as $v) {
			// Get DOI
			$doi = $v->doi ? 'doi:' . $v->doi : '';
			$doi_notice = $doi ? $doi : Lang::txt('PLG_PROJECTS_PUBLICATIONS_NA');

			// Version status
			$status = $this->pub->getStatusName($v->state);
			$class  = $this->pub->getStatusCss($v->state);
			$date   = $this->pub->getStatusDate($v);

			$options = '<a href="' . Route::url($this->pub->link('edit') . '&version=' . $v->version_number) . '">'
			. Lang::txt('PLG_PROJECTS_PUBLICATIONS_MANAGE_VERSION') . '</a>';

			$options .= '<span class="block"><a href="' . Route::url('index.php?option=com_publications'
			. '&id=' . $this->pid . '&v=' . $v->version_number) . '">'
			. Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_PAGE') . '</a></span>';

			?>
			<tr class="mini <?php if ($v->main == 1) { echo ' vprime'; } ?>">
				<td class="centeralign"><?php echo $v->version_number ? $v->version_number : ''; ?></td>
				<td><?php echo $v->version_label; ?></td>
				<td><?php echo $v->title; ?></td>
				<td>
					<span class="<?php echo $class; ?>"><?php echo $status; ?></span>
					<?php if ($date) { echo '<span class="block ipadded faded">'.$date.'</span>';  } ?>
				</td>
				<td><?php echo $doi_notice; ?></td>
				<td><?php echo $options; ?></td>
			</tr>
		<?php } ?>
		 </tbody>
		</table>
	<?php } ?>
</form>