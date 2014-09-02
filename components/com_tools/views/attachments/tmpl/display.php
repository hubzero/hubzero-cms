<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css('component.css')
     ->js('create.js');

$base = rtrim(JURI::base(true), '/');

if (!$this->allowupload) { ?>
	<p class="warning">
		<?php echo JText::_('COM_TOOLS_SUPPORTING_DOCS_ONLY_CURRENT'); ?>
	</p>
<?php } ?>

<?php $this->allowupload = true; ?>
	<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" name="hubForm" id="attachments-form" method="post" enctype="multipart/form-data">
		<fieldset>
			<label for="upload">
				<input type="file" class="option" name="upload" id="upload" />
				<input type="submit" class="option" value="<?php echo strtolower(JText::_('COM_TOOLS_UPLOAD')); ?>" />
			</label>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="tmpl" value="component" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
			<input type="hidden" name="path" id="path" value="<?php echo $this->path; ?>" />
		</fieldset>
	</form>

<?php if ($this->getError()) { ?>
	<p class="error">
		<?php echo implode('<br />', $this->getErrors()); ?>
	</p>
<?php } ?>

<?php
$out = '';
// loop through children and build list
if ($this->children)
{
	$base = $this->cparams->get('uploadpath');

	$k = 0;
	$i = 0;
	$files = array(13,15,26,33,35,38);
	$n = count($this->children);

	jimport('joomla.filesystem.file');

	if ($this->allowupload)
	{
		$out .= '<p>'.JText::_('COM_TOOLS_ATTACH_EDIT_TITLE_EXPLANATION').'</p>'."\n";
	}
	$out .= '<table class="list">'."\n";

	foreach ($this->children as $child)
	{
		$k++;

		// figure ou the URL to the file
		switch ($child->type)
		{
			case 12:
				if ($child->path)
				{
					// internal link, not a resource
					$url = $child->path;
				}
				else
				{
					// internal link but a resource
					$url = 'index.php?option=com_resources&id='. $child->id;
				}
			break;

			default:
				$url = $child->path;
			break;
		}

		// figure out the file type so we can give it the appropriate CSS class
		$type = '';
		$liclass = '';

		$type = JFile::getExt($url);
		$type = (strlen($type) > 3) ? substr($type, 0, 3): $type;
		if ($child->type == 12)
		{
			$liclass = ' class="ftitle html';
		}
		else
		{
			$type = ($type) ? $type : 'html';
			$liclass = ' class="ftitle '.$type;
		}

		$out .= ' <tr>';
		$out .= '  <td width="100%">';
		if ($this->allowupload)
		{
			$out .= '<span'.$liclass.' item:name id:'.$child->id.'" data-id="' . $child->id . '">'.$this->escape($child->title).'</span><br /><span class="caption">(<a href="'.JRoute::_('index.php?option=com_resources&task=download&id='.$child->id).'" title="'.$child->title.'">'.ContribtoolHtml::getFileAttribs($url, $base).'</a>)</span>';
		}
		else
		{
			$out .= '<span><a href="'.JRoute::_('index.php?option=com_resources&task=download&id=' . $child->id).'">'.$this->escape($child->title).'</a></span>';
		}
		$out .='</td>';
		if ($this->allowupload)
		{
			$out .= '  <td class="d">';

			if ($i > 0 || ($i+0 > 0))
			{
				$out .= '<a href="'. $base . '/index.php?option='.$this->option.'&amp;controller='.$this->controller.'&amp;tmpl=component&amp;pid='.$this->resource->id.'&amp;id='.$child->id.'&amp;task=reorder&amp;move=up" class="order up" title="'.JText::_('COM_TOOLS_MOVE_UP').'"><span>'.JText::_('COM_TOOLS_MOVE_UP').'</span></a>';
			}
			else
			{
				$out .= '&nbsp;';
			}
			$out .= '</td>';

			$out .= '  <td class="u">';

			if ($i < $n-1 || $i+0 < $n-1)
			{
				$out .= '<a href="'. $base . '/index.php?option='.$this->option.'&amp;controller='.$this->controller.'&amp;tmpl=component&amp;pid='.$this->resource->id.'&amp;id='.$child->id.'&amp;task=reorder&amp;move=down" class="order down" title="'.JText::_('COM_TOOLS_MOVE_DOWN').'"><span>'.JText::_('COM_TOOLS_MOVE_DOWN').'</span></a>';
			}
			else
			{
				$out .= '&nbsp;';
			}
			$out .= '</td>';
			$out .= '  <td class="t"><a href="'. $base . '/index.php?option='.$this->option.'&amp;controller='.$this->controller.'&amp;task=delete&amp;tmpl=component&amp;id='.$child->id.'&amp;pid='.$this->resource->id.'" class="icon-delete delete"><span> ' . JText::_('COM_TOOLS_DELETE') . '</span></a></td>';
		}
		$out .= ' </tr>';

		$i++;
	}
	$out .= '</table>';
}
else
{
	$out .= '<p>'.JText::_('COM_TOOLS_ATTACH_NONE_FOUND').'</p>';
}
echo $out;