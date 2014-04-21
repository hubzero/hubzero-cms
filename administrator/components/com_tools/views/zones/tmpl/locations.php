<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$text = JText::_('LOCATIONS');

JToolBarHelper::title(JText::_('Tools').': <small><small>[ ' . $text . ' ]</small></small>', 'tools.png');
JToolBarHelper::addNew();
JToolBarHelper::spacer();
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();
?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">

        <nav role="navigation" class="sub-navigation">
                <div id="submenu-box">
                        <div class="submenu-box">
                                <div class="submenu-pad">
                                        <ul id="submenu" class="member">
                                                <li><a href="index.php?option=com_tools&controller=zones&task=edit&id=<?php echo $this->row->id;?>" id="profile">Profile</a></li>
                                                <li><a href="#" onclick="return false;" id="locations" class="active">Locations</a></li>
                                        </ul>
                                        <div class="clr"></div>
                                </div>
                        </div>
                        <div class="clr"></div>
                </div>
        </nav><!-- / .sub-navigation -->

	<div id="zone-document">
		<div id="page-locations" class="tab">
			<p>hello</p>
		</div>
		<div class="clr"></div>
	</div>
</form>
