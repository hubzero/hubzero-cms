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
	<title><?php echo JText::_('FEEDBACK_PICTURE'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<style type="text/css" media="screen">@import url(/templates/<?php echo $app->getTemplate(); ?>/css/main.css);</style>
	<link rel="stylesheet" href="/administrator/components/<?php echo $this->_option; ?>/xflash.css" type="text/css" />
 </head>
 <body>
	<form action="index3.php" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data" >
		<fieldset style="border:none;">
			<div id="themanager" class="manager">
				<p style="color:#666666;width:80%;"><?php echo JText::_('UPLOAD_TIPS'); ?></p>
				<h4>dir = <?php echo JPATH_ROOT.$this->config->get('uploadpath'); ?></h4>
				<iframe src="index.php?option=com_xflash&amp;task=list&amp;no_html=1" name="imgManager" id="imgManager" width="95%" height="180"></iframe>
			</div>
		</fieldset>
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>		
		<fieldset style="border:none;">
			<input type="file" name="upload" id="upload" />
			<input type="submit" value="Upload" />

			<input type="hidden" name="option" value="com_xflash" />
			<input type="hidden" name="task" value="upload" />
			
			<?php echo JHTML::_( 'form.token' ); ?>
		</fieldset>
	</form>
 </body>
</html>