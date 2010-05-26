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
		<title><?php echo JText::_('COM_BLOG_FILE_MANAGER'); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" media="screen" href="/templates/<?php echo $app->getTemplate(); ?>/css/main.css" />
<?php if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->option.DS.'blof.css')) { ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->option.DS; ?>blog.css" />
<?php } else { ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo DS.'components'.DS.$this->option.DS; ?>blog.css" />
<?php } ?>
	</head>
	<body id="attachments">
		<form action="index.php" id="adminForm" method="post" enctype="multipart/form-data">
			<fieldset>
				<div id="themanager" class="manager">
					<iframe src="index.php?option=<?php echo $this->option; ?>&amp;no_html=1&amp;task=listfiles&amp;scope=<?php echo $this->scope; ?>&amp;id=<?php echo $this->id; ?>" name="imgManager" id="imgManager" width="98%" height="180"></iframe>
				</div>
			</fieldset>
			<fieldset>
				<p><input type="file" name="upload" id="upload" /></p>
				<p><input type="submit" value="<?php echo JText::_('COM_BLOG_UPLOAD'); ?>" /></p>
				
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="upload" />
				<input type="hidden" name="scope" value="<?php echo $this->scope; ?>" />
				<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
				<input type="hidden" name="no_html" value="1" />
			</fieldset>
		</form>
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	</body>
</html>