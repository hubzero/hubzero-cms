<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

$default = DS . trim($this->config->get('defaultpic'), DS);
$file = '';

if ($this->row->picture)
{
	// Build upload path
	$file  = DS . trim($this->config->get('uploadpath', '/site/quotes'), DS) . DS . \Hubzero\Utility\String::pad($this->user->get('id'));
	$file .= DS . trim($this->row->picture, DS);
}

$ow = 0;
$oh = 0;
$base = rtrim(JURI::getInstance()->base(true), '/');

if ($file && file_exists(JPATH_ROOT . $file))
{
	list($ow, $oh) = getimagesize(JPATH_ROOT . $file);
	$img = $base . $file;
}
else if ($default && file_exists(JPATH_ROOT . $default))
{
	list($ow, $oh) = getimagesize(JPATH_ROOT . $default);
	$img = $base . $default;
}

//scale if image is bigger than 120w x120h
$num = max($ow/120, $oh/120);
if ($num > 1)
{
	$mw = round($ow/$num);
	$mh = round($oh/$num);
}
else
{
	$mw = $ow;
	$mh = $oh;
}

$this->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul>
			<li>
				<a class="main-page btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
					<?php echo JText::_('COM_FEEDBACK_MAIN'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<p class="passed"><?php echo JText::_('COM_FEEDBACK_STORY_THANKS'); ?></p>

	<table class="storybox">
		<tbody>
			<tr>
				<td><img src="<?php echo $img; ?>" width="<?php echo $mw; ?>" height="<?php echo $mh; ?>" alt="" /></td>
				<td>
					<blockquote cite="<?php echo$this->escape($this->row->fullname); ?>" class="quote">
						<?php echo $this->escape(stripslashes($this->row->quote)); ?>
					</div>
					<div class="quote">
						<strong><?php echo $this->escape($this->row->fullname); ?></strong><br />
						<em><?php echo $this->escape($this->row->org); ?></em>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</section><!-- / .main section -->
