<?php
/**
 * @package      hubzero-cms-joomla
 * @file         components/com_user/views/logout/tmpl/default_logout.php
 * @author       Louis Landry <louis.landry@joomla.org>
 * @author       Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright    Copyright (c) 2010-2011 Purdue University. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl2.html GPLv2
 *
 * Copyright (c) 2010-2011 Purdue University
 * All rights reserved.
 *
 * This file is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 2 of the License, or (at your
 * option) any later version.
 *
 * This file is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * This file incorporates work covered by the following copyright and  
 * permission notice:  
 *
 *     @version         $Id: view.html.php 14401 2010-01-26 14:10:00Z louis $
 *     @package         Joomla
 *     @subpackage      Login
 *     @copyright       Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 *     @license         GNU/GPL, see LICENSE.php
 *     Joomla! is free software. This version may have been modified pursuant
 *     to the GNU General Public License, and as distributed it includes or
 *     is derivative of works licensed under the GNU General Public License or
 *     other free or open source software licenses.
 *     See COPYRIGHT.php for copyright notices and details.
 */
?>
<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php /** @todo Should this be routed */ ?>
<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post" name="login" id="login">
<?php if ( $this->params->get( 'show_logout_title' ) ) : ?>
<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<?php echo $this->escape($this->params->get( 'header_logout' )); ?>
</div>
<?php endif; ?>
<table border="0" align="center" cellpadding="4" cellspacing="0" class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" width="100%">
<tr>
	<td valign="top">
		<div>
		<?php echo $this->image; ?>
		<?php
			if ($this->params->get('description_logout')) :
				echo $this->escape($this->params->get('description_logout_text'));
			endif;
		?>
		</div>
	</td>
</tr>
<tr>
	<td align="center">
		<div align="center">
			<input type="submit" name="Submit" class="button" value="<?php echo JText::_( 'Logout' ); ?>" />
		</div>
	</td>
</tr>
</table>

<br /><br />

<input type="hidden" name="option" value="com_user" />
<input type="hidden" name="task" value="logout" />
<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
</form>
