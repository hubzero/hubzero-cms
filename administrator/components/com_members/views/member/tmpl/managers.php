<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$app =& JFactory::getApplication();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('MEMBER_HOSTS'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<style type="text/css" media="screen">@import url(/templates/<?php echo $app->getTemplate(); ?>/css/main.css);</style>
	<style type="text/css" media="screen">
	body { min-width: 20px; background: #fff; margin: 0; padding: 0; }
	</style>
 </head>
 <body>
	<form action="index.php" method="post">
		<table>
		 <tbody>
		  <tr>
		   <td>
		    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="no_html" value="1" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
			<input type="hidden" name="task" value="addmanager" />

			<input type="text" name="manager" value="" /> 
			<input type="submit" value="<?php echo JText::_('Add Manager'); ?>" />
		   </td>
		  </tr>
		 </tbody>
		</table>
		<br />
		<table class="paramlist admintable">
			<tbody>
		<?php
		if (count($this->rows) > 0) {
			foreach ($this->rows as $row)
			{
				?>
				<tr>
					<td class="paramlist_key"><?php echo $row; ?></td>
					<td class="paramlist_value"><a href="index.php?option=<?php echo $this->option; ?>&amp;no_html=1&amp;task=deletemanager&amp;manager=<?php echo $row; ?>&amp;id=<?php echo $this->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1"><?php echo JText::_('DELETE'); ?></a></td>
				</tr>
				<?php
			}
		}
		?>
			</tbody>
		</table>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
 </body>
</html>

