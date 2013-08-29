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
$src = $this->thumb && file_exists( $this->file_path.DS.$this->thumb ) ? $this->path.DS.$this->thumb :  $this->default_picture;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo JText::_('COM_PROJECTS_THUMB'); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" media="screen" href="/templates/<?php echo $app->getTemplate(); ?>/css/main.css" />
<?php if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->option.DS.'projects.css')) { ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->option.DS; ?>projects.css" />
<?php } else { ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo DS.'components'.DS.$this->option.DS; ?>assets/css/projects.css" />
<?php } ?>
	</head>
	<body id="project-picture">
		<form action="index.php" method="post" enctype="multipart/form-data" name="imaForm" id="imaForm">
			<div id="project-thumb" class="project-thumb">
				<img src="<?php echo $src; ?>" alt="<?php echo JText::_('COM_PROJECTS_THUMB'); ?>" />
				<?php if ($this->thumb && file_exists( $this->file_path.DS.$this->thumb )) { ?>
				<p class="actionlink"><a href="index.php?option=<?php echo $this->option; ?>&amp;no_html=1&amp;task=deleteimg&amp;file=<?php echo $this->file; ?>&amp;imaid=<?php echo $this->id; ?>&amp;tempid=<?php echo $this->tempid; ?>">[ <?php echo JText::_('DELETE'); ?> ]</a></p>
				<?php } ?>
			</div>
			<div>
				<h4><?php echo JText::_('COM_PROJECTS_UPLOAD_NEW_IMAGE'); ?> <span class="hint"><?php echo JText::_('COM_PROJECTS_WILL_REPLACE_EXISTING_IMAGE'); ?></span></h4>

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="no_html" value="1" />
				<input type="hidden" name="task" value="upload" />
				<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
				<input type="hidden" name="tempid" value="<?php echo $this->tempid; ?>" />
				<input type="file" name="upload" id="upload" size="20" /> 
				<input type="hidden" name="currentfile" value="<?php echo $this->file; ?>" />
				<input type="submit" value="<?php echo JText::_('COM_PROJECTS_UPLOAD'); ?>" />
				<?php if ($this->getError()) { ?>
							<p class="error_s"><?php echo JText::_('COM_PROJECTS_ERROR'); ?>: <?php echo $this->getError(); ?></p>
				<?php } else { ?>
					<p class="hint block ipadded"><?php echo JText::_('Accepted formats: .jpg, .gif and .png'); ?></p>
				<?php } ?>
			</div>
	   </form>
	</body>
</html>