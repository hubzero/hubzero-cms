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

$app = JFactory::getApplication();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo JText::_('COURSES_FILE_MANAGER'); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" media="screen" href="/templates/<?php echo $app->getTemplate(); ?>/css/main.css" />
		<?php
			$template_css = DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->option.DS.'media.css';
			$component_css = DS.'components'.DS.'com_courses'.DS.'assets'.DS.'css'.DS.'media.css';
		?>
		<?php if (is_file( JPATH_ROOT . $template_css)) { ?>
			<link rel="stylesheet" type="text/css" href="<?php echo $template_css; ?>" />
		<?php } else { ?>
			<link rel="stylesheet" type="text/css" href="<?php echo $component_css; ?>" />
		<?php } ?>

		<?php if (JPluginHelper::isEnabled('system', 'jquery')) : ?>
			<script src="/media/system/js/jquery.js"></script>
			<script src="/media/system/js/jquery.fileuploader.js"></script>
			<script src="/components/com_courses/assets/js/courses.fileupload.jquery.js"></script>
		<?php endif; ?>
	</head>
	<body id="file_browser">
		<form action="index.php" id="adminForm" method="post" enctype="multipart/form-data">
			<fieldset>
				<div id="themanager" class="manager">
					<iframe src="index.php?option=<?php echo $this->option; ?>&amp;no_html=1&amp;task=listfiles&amp;listdir=<?php echo $this->listdir; ?>" name="imgManager" id="imgManager" width="99%" height="180"></iframe>
				</div>
			</fieldset>
			<fieldset>
				<?php if (JPluginHelper::isEnabled('system', 'jquery')) : ?>
					<div id="ajax-uploader" data-action="index.php?option=com_courses&amp;task=ajaxupload&amp;listdir=<?php echo $this->listdir; ?>&amp;no_html=1">
						<noscript>
							<p><input type="file" name="upload" id="upload" /></p>
							<p><input type="submit" value="<?php echo JText::_('UPLOAD'); ?>" /></p>
						</noscript>
					</div>
				<?php else : ?>
					<p><input type="file" name="upload" id="upload" /></p>
					<p><input type="submit" value="<?php echo JText::_('UPLOAD'); ?>" /></p>
				<?php endif; ?>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="listdir" id="listdir" value="<?php echo $this->listdir; ?>" />
				<input type="hidden" name="task" value="upload" />
				<input type="hidden" name="no_html" value="1" />
				<!--<input type="hidden" name="gid" value="<?php //echo $this->course->get('cn'); ?>" />-->
			</fieldset>
		</form>
	</body>
</html>
