<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
?>
	<li>
		<span class="item-options">
			<?php if ($data->get('viewer') == 'edit') { ?>
			<span>
				<?php if ($data->exists()) { ?>
				<a href="<?php echo $data->get('downloadUrl'); ?>" class="item-download" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DOWNLOAD'); ?>">&nbsp;</a>
				<?php } ?>
				<a href="<?php echo Route::url($pub->link('editversion') . '&action=edititem&aid=' . $data->get('id') . '&p=' . $data->get('props')); ?>" class="showinbox item-edit" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_RELABEL'); ?>">&nbsp;</a>
				<a href="<?php echo Route::url($pub->link('editversion') . '&action=deleteitem&aid=' . $data->get('id') . '&p=' . $data->get('props')); ?>" class="item-remove" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</a>
			</span>
			<?php } elseif ($data->exists()) { ?>
				<span><a href="<?php echo $data->get('downloadUrl'); ?>" class="item-download" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DOWNLOAD'); ?>">&nbsp;</a></span>
			<?php } ?>
		</span>
		<span class="item-title">
			<?php echo $data::drawIcon($data->get('ext')); ?> <?php echo $data->get('title'); ?>
		</span>
		<span class="item-details"><?php echo $details; ?></span>
	</li>