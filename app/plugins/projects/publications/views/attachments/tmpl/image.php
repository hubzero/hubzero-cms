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

$data = $this->data;
$pub  = $data->get('pub');

$details = $data->get('localPath');
$details.= $data->getSize() ? ' | ' . $data->getSize('formatted') : '';
if ($data->get('viewer') != 'freeze')
{
	$details.= !$data->exists() ? ' | ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_MISSING_FILE') : '';
}

// Get settings
$suffix = isset($this->config->params->thumbSuffix) && $this->config->params->thumbSuffix
		? $this->config->params->thumbSuffix : '-tn';

$format = isset($this->config->params->thumbFormat) && $this->config->params->thumbFormat
		? $this->config->params->thumbFormat : 'png';

$thumbName = \Components\Projects\Helpers\Html::createThumbName(basename($data->get('fpath')), $suffix, $format);

$filePath = Route::url($pub->link('versionid')) . '/Image:' . urlencode(basename($data->get('fpath')));
$thumbSrc = Route::url($pub->link('versionid')) . '/Image:' . urlencode($thumbName);

// Is this image used for publication thumbail?
$class = $data->get('pubThumb') == 1 ? ' starred' : '';
$over  = $data->get('pubThumb') == 1 ? ' title="' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_IMAGE_DEFAULT') . '"' : '';

?>
	<li class="image-container">
		<span class="item-options">
			<?php if ($data->get('viewer') == 'edit') { ?>
			<span>
				<?php if (!$data->get('pubThumb')) { ?>
				<a href="<?php echo Route::url($pub->link('editversion') . '&action=saveitem&aid=' . $data->get('id') . '&p=' . $data->get('props') . '&makedefault=1'); ?>" class="item-default" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_IMAGE_MAKE_DEFAULT'); ?>">&nbsp;</a>
				<?php } ?>
				<a href="<?php echo Route::url($pub->link('editversion') . '&action=edititem&aid=' . $data->get('id') . '&p=' . $data->get('props')); ?>" class="showinbox item-edit" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_RELABEL'); ?>">&nbsp;</a>
				<a href="<?php echo Route::url($pub->link('editversion') . '&action=deleteitem&aid=' . $data->get('id') . '&p=' . $data->get('props')); ?>" class="item-remove" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</a>
			</span>
			<?php } ?>
		</span>
		<span class="item-image<?php echo $class; ?>" <?php echo $over; ?>><a class="more-content" href="<?php echo $filePath; ?>"><img alt="" src="<?php echo $thumbSrc; ?>" /></a></span>
		<span class="item-title">
			<?php echo $data->get('title'); ?></span>
		<span class="item-details"><?php echo $details; ?></span>
	</li>