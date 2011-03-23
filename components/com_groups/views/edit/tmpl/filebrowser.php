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
		<title><?php echo JText::_('GROUPS_FILE_MANAGER'); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" media="screen" href="/templates/<?php echo $app->getTemplate(); ?>/css/main.css" />
		<?php
			$template_css = DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->option.DS.'media.css';
			$component_css = DS.'components'.DS.'com_groups'.DS.'assets'.DS.'css'.DS.'media.css';
		?>
		<?php if(is_file( JPATH_ROOT . $template_css)) { ?>
			<link rel="stylesheet" type="text/css" href="<?php echo $template_css; ?>" />
		<?php } else { ?>
			<link rel="stylesheet" type="text/css" href="<?php echo $component_css; ?>" />
		<?php } ?>
	</head>
	<body id="file_browser">
		<?php
			foreach($this->notifications as $notification) {
				echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
			}
		?>
		<form action="index.php" id="adminForm" method="post" enctype="multipart/form-data">
			<fieldset>
				<div id="themanager" class="manager">
					<iframe src="index.php?option=<?php echo $this->option; ?>&amp;no_html=1&amp;task=listfiles&amp;listdir=<?php echo $this->listdir; ?>" name="imgManager" id="imgManager" width="99%" height="180"></iframe>
				</div>
			</fieldset>
			<fieldset>
				<p><input type="file" name="upload" id="upload" /></p>
				<p><input type="submit" value="<?php echo JText::_('UPLOAD'); ?>" /></p>
				
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="listdir" id="listdir" value="<?php echo $this->listdir; ?>" />
				<input type="hidden" name="task" value="upload" />
				<input type="hidden" name="no_html" value="1" />
			</fieldset>
		</form>
	</body>
</html>