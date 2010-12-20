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

$app =& JFactory::getApplication();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('COM_CONTRIBUTE'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link rel="stylesheet" type="text/css" media="screen" href="/templates/<?php echo $app->getTemplate(); ?>/css/main.css" />
	<?php
		if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->option.DS.'contribute.css')) {
			echo '<link rel="stylesheet" type="text/css" media="screen" href="'.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->option.DS.'contribute.css" />'."\n";
		} else {
			echo '<link rel="stylesheet" type="text/css" media="screen" href="'.DS.'components'.DS.$this->option.DS.'contribute.css" />'."\n";
		}
	?>
	
    <script type="text/javascript" src="/media/system/js/mootools.js"></script>
	<script type="text/javascript" src="/components/<?php echo $this->option; ?>/contribute.js"></script>
 </head>
 <body id="small-page">
			
			<form action="index.php" name="hubForm" id="attachments-form" method="post" enctype="multipart/form-data">
			<fieldset>
			<p> Files:</p>
				<label>
					<input type="file" class="option" name="upload" />
				</label>
				<input type="submit" class="option" value="<?php echo JText::_('COM_CONTRIBUTE_UPLOAD'); ?>" />
				<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="no_html" value="1" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
				<input type="hidden" name="path" id="path" value="<?php echo $this->path; ?>" />
				<input type="hidden" name="task" value="saveattach" />
			</fieldset>
			</form>
			<div class="clear"></div>
			<form action="index.php" name="hubForm" id="link-attachments-form" method="post" enctype="multipart/form-data">
			<p><br/> External Links: </p>
			<fieldset>
				<label>
					<input type="text" class="option" name="link-address" />
				</label>
				
				<input type="submit" class="option" value="Add External Link" />
				<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="no_html" value="1" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
				<input type="hidden" name="path" id="path" value="<?php echo $this->path; ?>" />
				<input type="hidden" name="task" value="savelink" />
			</fieldset>
			</form>
			<div class="clear"></div>
			<?php if($this->type == 31) {?> 
			<p>NEESHub Resource ID: </p>
			<form action="index.php" name="hubForm" id="link-attachments-form" method="post" enctype="multipart/form-data">
			<fieldset>
				<label>
					<input type="text" class="option" name="resourceID" />
				</label>
				<input type="submit" class="option" value="Add Resource" />
				<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="no_html" value="1" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
				<input type="hidden" name="path" id="path" value="<?php echo $this->path; ?>" />
				<input type="hidden" name="task" value="saveresource" />
			</fieldset>
			</form>
			<?php  } ?>
			<div class="clear"></div>
			
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
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
				if (($child->type == 12) or ($child->type == 13)) { //link type
					$liclass = 'html';
				} else {
					$type = ($type) ? $type : 'html';
					$liclass = $type;
				}
?>			
				<tr>
					<td width="100%" class="<?php echo $liclass; ?>"><span class="ftitle item:name id:<?php echo $child->id; ?>"><?php echo $child->title; ?></span> <?php echo Hubzero_View_Helper_Html::getFileAttribs( $url, $base ); ?></td>
					<td class="u"><?php
					if ($i > 0 || ($i+0 > 0)) {
					    echo '<a href="index.php?option=com_contribute&amp;no_html=1&amp;pid='.$this->id.'&amp;id='.$child->id.'&amp;task=orderupa&amp;type='.$this->type.'" class="order up" title="'.JText::_('COM_CONTRIBUTE_MOVE_UP').'"><span>'.JText::_('COM_CONTRIBUTE_MOVE_UP').'</span></a>';
			  		} else {
			  		    echo '&nbsp;';
					}
					?></td>
					<td class="d"><?php
					if ($i < $n-1 || $i+0 < $n-1) {
						echo '<a href="index.php?option=com_contribute&amp;no_html=1&amp;pid='.$this->id.'&amp;id='.$child->id.'&amp;task=orderdowna&amp;type='.$this->type.'" class="order down" title="'.JText::_('COM_CONTRIBUTE_MOVE_DOWN').'"><span>'.JText::_('COM_CONTRIBUTE_MOVE_DOWN').'</span></a>';
			  		} else {
			  		    echo '&nbsp;';
					}
					?></td>
					<td class="t"><a href="index.php?option=<?php echo $this->option; ?>&amp;task=deleteattach&amp;no_html=1&amp;id=<?php echo $child->id; ?>&amp;pid=<?php echo $this->id; ?>&type=<?php echo $this->type?>"><img src="/components/<?php echo $this->option; ?>/images/trash.gif" alt="<?php echo JText::_('COM_CONTRIBUTE_DELETE'); ?>" /></a></td>
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
 </body>
</html>