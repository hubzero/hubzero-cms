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

$file = str_replace($this->path . DS, '', $this->item);
$ext = explode('.', $file);
$ext = end($ext);
$ext = strtolower($ext);

$html = '';
if ($this->att->id) 
{
	$html .= $this->att->title ? '"' . $this->att->title . '"' : JText::_('PLG_PROJECTS_PUBLICATIONS_NO_DESCRIPTION');
	$html .= $this->revision ? ' &middot; ' . $this->revision : '';
}
else 
{
	$html .= JText::_('PLG_PROJECTS_PUBLICATIONS_NO_DESCRIPTION');
}

?>
	<img src="<?php echo ProjectsHtml::getFileIcon($ext); ?>" alt="<?php echo $ext; ?>" /><?php echo ProjectsHtml::shortenFileName($file, 50); ?> 
	<?php if($this->canedit && $this->pid) { ?>
	<span class="c-edit"><a href="<?php echo $this->url.'?vid='.$this->vid.a.'item=file::'.urlencode($file).a.'move='.$this->move.a.'action=edititem'.a.'role=' . $this->role; ?>" class="showinbox"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT')); ?></a></span>
	<?php } ?>
	<span class="c-iteminfo"><?php echo $html; ?></span>

