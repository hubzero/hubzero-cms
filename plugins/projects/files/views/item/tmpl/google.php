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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$item = $this->item;

$img = \Components\Projects\Helpers\Html::getGoogleIcon($item->remote_format);

$url = $this->url . '/?action=open&amp;subdir='.urlencode($this->subdir) . '&amp;file=' . urlencode(basename($item->local_path));

$when = \Components\Projects\Helpers\Html::formatTime(date ('c', strtotime($item->remote_modified .  ' UTC')));

$ext = \Components\Projects\Helpers\Html::getFileExtension($item->local_path);

$name = basename($item->local_path);

// Is this a duplicate remote?
if (basename($item->local_path) != $item->remote_title)
{
	$append = \Components\Projects\Helpers\Html::getAppendedNumber(basename($item->local_path));
	if ($append > 0)
	{
		$name = \Components\Projects\Helpers\Html::fixFileName($item->remote_title, ' (' . $append . ')', $ext );
	}
}

// Do not display Google native extension
$native = \Components\Projects\Helpers\Google::getGoogleNativeExts();
if (in_array($ext, $native))
{
	$name = preg_replace("/." . $ext . "\z/", "", $name);
}

// LaTeX?
$tex = 0;
$texFormats = array('application/x-tex', 'text/x-tex');
if (in_array($item->remote_format, $texFormats)  || in_array($item->original_format, $texFormats))
{
	$tex = 1;
}

$member = $this->model->member();
$params = new \JParameter($member->params);

$author = $item->remote_author ? utf8_decode($item->remote_author) : '';
$me = ($author == utf8_decode($params->get('google_name')) || $author == User::get('name') ) ? 1 : 0;

?>
<tr class="mini faded mline google-resource">
	<td><input type="checkbox" value="<?php echo urlencode(basename($item->local_path)); ?>" name="asset[]" class="checkasset js remote service-google" /></td>
	<td class="sharing"><img src="<?php echo $img; ?>" alt="<?php echo urlencode($item->remote_title); ?>" />
		<a href="<?php echo $url; ?>"
		<?php if ($item->type == 'file') { ?> class="preview file:<?php echo urlencode(basename($item->local_path)); ?>" <?php } else { echo 'title="' . Lang::txt('PLG_PROJECTS_FILES_GO_TO_DIR') . ' ' . $item->remote_title . '"'; } ?> target="_blank"><?php echo \Components\Projects\Helpers\Html::shortenFileName($name, 50); ?></a>
	</td>
	<td class="shrinked"></td>
	<td class="shrinked"></td>
	<td class="shrinked"><a href="<?php echo $this->url . '/?action=history&amp;subdir=' . urlencode($this->subdir) . '&amp;asset=' . urlencode(basename($item->local_path)); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_HISTORY_TOOLTIP'); ?>"><?php echo $when; ?></a></td>
	<td class="shrinked pale"><?php echo $me ? Lang::txt('PLG_PROJECTS_FILES_ME') : $author; ?></td>
	<td class="shrinked nojs"><a href="<?php echo $this->url . '/?action=delete&amp;subdir=' . urlencode($this->subdir)
	. '&amp;asset=' . urlencode(basename($item->local_path)); ?>"
	 title="<?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE_TOOLTIP'); ?>" class="i-delete">&nbsp;</a></td>
	<?php if ($this->publishing) { ?>
	<td class="shrinked"></td>
	<?php } ?>
</tr>
