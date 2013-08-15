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
?>
	<div id="small-page">
		<form action="index.php" name="hubForm" id="attachments-form" method="post" enctype="multipart/form-data">
			<fieldset>
				<label>
					<input type="file" class="option" name="upload" />
				</label>
				<input type="submit" class="option" value="<?php echo JText::_('COM_CONTRIBUTE_UPLOAD'); ?>" />

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="tmpl" value="component" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
				<input type="hidden" name="path" id="path" value="<?php echo $this->path; ?>" />
				<input type="hidden" name="task" value="save" />
			</fieldset>
		</form>
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
		<?php
		$out = '';
		// loop through children and build list
		if ($this->children) {
			$base = $this->config->get('uploadpath');

			$k = 0;
			$i = 0;
			$files = array(13,15,26,33,35,38);
			$n = count( $this->children );
?>
		<p><?php echo Jtext::_('COM_CONTRIBUTE_ATTACH_EDIT_TITLE_EXPLANATION'); ?></p>
		<table class="list">
			<tbody>
<?php
			foreach ($this->children as $child)
			{
				$k++;

				// figure ou the URL to the file
				switch ($child->type)
				{
					case 12:
						if ($child->path) {
							// internal link, not a resource
							$url = $child->path;
						} else {
							// internal link but a resource
							$url = '/index.php?option=com_resources&id='. $child->id;
						}
						break;
					default:
						$url = $child->path;
						break;
				}

				// figure out the file type so we can give it the appropriate CSS class
				$type = '';
				$liclass = '';
				$file_name_arr = explode('.',$url);
	    		$type = end($file_name_arr);
				$type = (strlen($type) > 3) ? substr($type, 0, 3): $type;
				if ($child->type == 12) {
					$liclass = 'html';
				} else {
					$type = ($type) ? $type : 'html';
					$liclass = $type;
				}
?>			
				<tr>
					<td width="100%" class="<?php echo $liclass; ?>"><span class="ftitle item:name id:<?php echo $child->id; ?>" data-id="<?php echo $child->id; ?>"><?php echo $child->title; ?></span> <?php echo Hubzero_View_Helper_Html::getFileAttribs( $url, $base ); ?></td>
					<td class="u"><?php
					if ($i > 0 || ($i+0 > 0)) {
					    echo '<a href="index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '&amp;tmpl=component&amp;pid='.$this->id.'&amp;id='.$child->id.'&amp;task=reorder&amp;move=up" class="order up" title="'.JText::_('COM_CONTRIBUTE_MOVE_UP').'"><span>'.JText::_('COM_CONTRIBUTE_MOVE_UP').'</span></a>';
			  		} else {
			  		    echo '&nbsp;';
					}
					?></td>
					<td class="d"><?php
					if ($i < $n-1 || $i+0 < $n-1) {
						echo '<a href="index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '&amp;tmpl=component&amp;pid='.$this->id.'&amp;id='.$child->id.'&amp;task=reorder&amp;move=down" class="order down" title="'.JText::_('COM_CONTRIBUTE_MOVE_DOWN').'"><span>'.JText::_('COM_CONTRIBUTE_MOVE_DOWN').'</span></a>';
			  		} else {
			  		    echo '&nbsp;';
					}
					?></td>
					<td class="t"><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=delete&amp;tmpl=component&amp;id=<?php echo $child->id; ?>&amp;pid=<?php echo $this->id; ?>"><img src="/components/<?php echo $this->option; ?>/assets/img/trash.gif" alt="<?php echo JText::_('COM_CONTRIBUTE_DELETE'); ?>" /></a></td>
				</tr>
<?php
				$i++;
			}
?>
			</tbody>
		</table>
<?php } else { ?>
		<p><?php echo JText::_('COM_CONTRIBUTE_ATTACH_NONE_FOUND'); ?></p>
<?php } ?>
	</div>