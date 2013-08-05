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
defined('_JEXEC') or die('Restricted access');

$this->versionlabel = ($this->version == 'current') ? JText::_('COM_TOOLS_CURRENTLY_PUBLISHED') : JText::_('COM_TOOLS_DEVELOPMENT');
if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>

<form action="index.php" name="hubForm" id="screenshots-form" method="post" enctype="multipart/form-data">
	<h3>
		<?php echo JText::_('COM_TOOLS_EXISTING_SS'); ?> 
		<?php if ($this->published) { ?>
			(<?php echo $this->version=='dev' ? JText::_('COM_TOOLS_DEVELOPMENT').' '.strtolower(JText::_('COM_TOOLS_VERSION')) : JText::_('COM_TOOLS_CURRENTLY_PUBLISHED').' '.strtolower(JText::_('COM_TOOLS_VERSION'));  ?>)
		<?php } ?>
	</h3> 

<?php
$d = @dir($this->upath);

$images = array();
$tns = array();
$all = array();
$ordering = array();
$html = '';

// pick images from the upload directory
if ($d) 
{
	while (false !== ($entry = $d->read()))
	{
		$img_file = $entry;
		if (is_file($this->upath . DS . $img_file) && substr($entry, 0, 1) != '.' && strtolower($entry) !== 'index.html') 
		{
			if (preg_match("#bmp|gif|jpg|png|swf#i", $img_file)) 
			{
				$images[] = $img_file;
			}
			if (preg_match("#-tn#i", $img_file)) 
			{
				$tns[] = $img_file;
			}
			$images = array_diff($images, $tns);
		}

	}

	$d->close();
}

// get rid of images without thumbnails
if ($images) 
{
	foreach ($images as $key => $value) 
	{
		$tn = ResourcesHtml::thumbnail($value);
		if (!is_file($this->upath . DS . $tn)) 
		{
			unset($images[$key]);
		}
	}
	$images = array_values($images);
}

// Get screenshot titles and ordering
$b = 0;
if ($images) 
{
	foreach ($images as $ima) 
	{
		$new = array();
		$new['img'] = $ima;
		$new['type'] = explode('.', $new['img']);

		// get title and ordering info from the database, if available
		if (count($this->shots > 0)) 
		{
			foreach ($this->shots as $si) 
			{
				if ($si->filename == $ima) 
				{
					$new['title'] = stripslashes($si->title);
					$new['title'] = preg_replace('/"((.)*?)"/i', "&#147;\\1&#148;", $new['title']);
					$new['ordering'] = $si->ordering;
				}
			}
		}

		$ordering[] = isset($new['ordering']) ? $new['ordering'] : $b;
		$b++;
		$all[]=$new;
	}
}

// Order images
if (count($this->shots > 0)) 
{
	// sort by ordering
	array_multisort($ordering, $all);
}
else 
{
	// sort by name
	sort($all);
}
$images = $all;

// Display screenshots
$els = '';
$k = 0;
$g = 0;
for ($i=0, $n=count($images); $i < $n; $i++)
{
	$tn = ResourcesHtml::thumbnail($images[$i]['img']);

	if (is_file($this->upath . DS . $tn)) 
	{
		if (strtolower(end($images[$i]['type'])) == 'swf') 
		{
			$g++;
			$title = (isset($images[$i]['title']) && $images[$i]['title']!='') ? $images[$i]['title'] : JText::_('COM_TOOLS_DEMO').' #'.$g;
			$els .= '<li>';
				$els .= '<a class="popup" rel="external" href="'.$this->wpath.DS.$images[$i]['img'].'" title="'.$title.'">';
					$els .= '<img src="'.$this->wpath.DS.$tn.'" alt="'.$title.'" id="ss_'.$i.'" />';
				$els .= '</a>';
			$els .= '</li>'."\n";
		} 
		else 
		{
			$k++;
			$title = (isset($images[$i]['title']) && $images[$i]['title']!='') ? $images[$i]['title']: JText::_('COM_TOOLS_SCREENSHOT').' #'.$k;
			$els .= '<li>';
				$els .= '<span class="dev_ss">';
					$els .= '<a href="/index.php?option='.$this->option.'&amp;controller='.$this->controller.'&amp;task=edit&amp;pid='.$this->rid.'&amp;filename='.$images[$i]['img'].'&amp;version='.$this->version.'&amp;tmpl=component" class="edit_ss popup" rel="external">'.JText::_('COM_TOOLS_EDIT').'</a>';
					$els .= '<a href="/index.php?option='.$this->option.'&amp;controller='.$this->controller.'&amp;task=delete&amp;pid='.$this->rid.'&amp;filename='.$images[$i]['img'].'&amp;version='.$this->version.'&amp;tmpl=component" class="delete_ss">'.JText::_('COM_TOOLS_DELETE').'</a>';
				$els .= '</span>';
				$els .= '<a href="/index.php?option='.$this->option.'&amp;controller='.$this->controller.'&amp;task=edit&amp;pid='.$this->rid.'&amp;filename='.$images[$i]['img'].'&amp;version='.$this->version.'&amp;tmpl=component" class="popup" title="'.$title.'">';
					$els .= '<img src="'.$this->wpath.DS.$tn.'" alt="'.$title.'" id="ss_'.$i.'" />';
				$els .= '</a>';
			$els .= '</li>'."\n";
		}
		// add re-ordering option
		if ($i != ($n-1)) 
		{
			$els .= '<li style="width:20px;top:40px;">';
				$els .= '<a href="/index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '&amp;task=order&amp;pid='.$this->rid.'&amp;fl='.$images[$i+1]['img'].'&amp;fr='.$images[$i]['img'].'&amp;ol='.($i+1).'&amp;or='.$i.'&amp;version='.$this->version.'&amp;tmpl=component">';
					$els .= '<img src="components'.DS.$this->option.DS.'assets/img/contribute/reorder.gif" alt="" />';
				$els .= '</a>';
			$els .= '</li>'."\n";
		}
	}
}

if ($els) {
	$html .= '<div class="upload_ss">'."\n";
	$html .= '<ul class="screenshots">'."\n";
	$html .= $els;
	$html .= '</ul>'."\n";
	$html .= '<div class="clear"></div></div>'."\n";
} else {
	// No images available
	$html .= '<p class="upload_ss">'.JText::_('COM_TOOLS_UPLOAD_NO_SS').'</p>';
}
echo $html;
?>
	<div class="clear"></div>

	<h3><?php echo JText::_('COM_TOOLS_UPLOAD_NEW_SS'); ?></h3>

	<fieldset class="uploading">
		<label>
			<input type="file" class="option" name="upload" />
		</label>
		<label class="ss_title" for="title">
			<?php echo JText::_('COM_TOOLS_SS_TITLE').':'; ?>
			<input type="text" name="title"  size="127" maxlength="127" value="" class="input_restricted" />
			<input type="submit" class="upload" value="<?php echo strtolower(JText::_('COM_TOOLS_UPLOAD')); ?>" />
		</label>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="changing_version" value="0" />
		<input type="hidden" name="version" id="version" value="<?php echo $this->version; ?>" />
		<input type="hidden" name="pid" id="pid" value="<?php echo $this->rid; ?>" />
		<input type="hidden" name="path" id="path" value="<?php echo $this->upath; ?>" />
		<input type="hidden" name="task" value="upload" />
	</fieldset>
</form>
<?php if ($this->published && $this->version=='dev') { ?>
	<form action="index.php" name="copySSForm"  method="post" enctype="multipart/form-data">
		<fieldset style="border-top:1px solid #ccc;padding-top:1em;">
			<legend><?php echo JText::_('COM_TOOLS_Copy Screenshots'); ?></legend>
			
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="copy" />
			<input type="hidden" name="rid" value="<?php echo $this->rid; ?>" />
			<input type="hidden" name="tmpl" value="component" />
			<label>
				<?php 
				$v = $this->version=='dev' ? 'current' : 'development';
				echo JText::_('COM_TOOLS_From').' '.$v.' '.strtolower(JText::_('COM_TOOLS_VERSION'));
				?>
				<input type="submit" class="upload" value="<?php echo strtolower(JText::_('COM_TOOLS_COPY')); ?>" />
			</label>
		</fieldset>
	</form>
<?php } ?>