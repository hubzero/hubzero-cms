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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<section class="main section">
	<h3>Would you like to completely log out of your <?php echo $this->display_name; ?> account?</h2>
	<p>
		Your <?php echo $this->sitename; ?> session has ended.
	</p>
	<p>
		If you would like to end all <?php echo $this->display_name; ?> account shared sessions as well, you may do so now.
	</p>
	<p>
		<a class="logout btn" href="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&authenticator=' . $this->authenticator); ?>">
			End all <?php echo $this->display_name; ?> account sessions!
		</a>
	</p>
	<p>
		<a class="home btn" href="<?php echo JURI::base(); ?>">
			Leave other <?php echo $this->display_name; ?> account sessions untouched.
		</a>
	</p>
</section>
