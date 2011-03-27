<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2008-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2008-2011 Purdue University. All rights reserved.
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
?>
<table cellspacing="36" cellpadding="0" border="0">
 <tr valign="top">
  <td width="20%">
    <p>
    <font size="+1"><b>Welcome to <?php echo $this->forgeName;?></b></font>, the project
    development area of <a href="<?php echo $this->hubLongURL;?>"><?php echo $this->hubShortURL;?></a>.
    The following pages are maintained by the various owners of each
    project.  Many of these tools are available as Open Source, and
    you can download the code via Subversion from this site.  Some
    tools are closed source at the request of the authors, and only
    a restricted development team has access to the code.  See each
    project page for details.
    </p><p>
    <a href="<?php echo $this->hubLongURL;?>/register">Become a member!</a>
    Sign up for a free <?php echo $this->hubShortName;?> account and use this site to manage
    your software project.
    </p>
   <img src="<?php echo $this->image;?>" alt="<?php echo $this->forgeName;?>"/>
  </td>

  <!-- Infrastructure -->
  <td width="20%" bgcolor="#ccccff">
    <p class="title-blue">Infrastructure Projects</p>
    <?php foreach ($this->infrastructureProjects as $project) { ?>
        <p class="link"><a href="<?php echo $this->forgeURL;?>/projects/<?php echo $project;?>"><?php echo $project;?></a></p>
    <?php } ?>
  </td>

  <!-- Applications -->
  <td width="20%" bgcolor="#cccccc">
    <p class="title-gray">Applications</p>
    <?php foreach ($this->appProjects as $project) { ?>
        <p class="link"><a href="<?php echo $this->forgeURL;?>/projects/<?php echo $project;?>"><?php echo $project;?></a></p>
    <?php } ?>
  </td>

  <!-- Groups -->
  <td width="20%" bgcolor="#ffffcc">
    <p class="title-yellow">Groups</p>
    <?php foreach ($this->groupProjects as $project) { ?>
        <p class="link"><a href="<?php echo $this->forgeURL;?>/projects/<?php echo $project;?>"><?php echo $project;?></a></p>
    <?php } ?>
  </td>

  <td width="20%">&nbsp;</td>
</tr>
</table>

