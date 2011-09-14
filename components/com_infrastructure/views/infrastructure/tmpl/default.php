<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2008-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<h2><?php echo $this->forgeName;?>: Infrastructure</h2>
<div id="forge">
<div class="threecolumn farleft">
    <p>
        Welcome to <?php echo $this->forgeName;?>:Infrastructure, the infrastructure
        development area of <a href="<?php echo $this->hubLongURL;?>"><?php echo $this->hubShortURL;?></a>.
        The following pages are maintained by the various owners of each
        project.  Some of these projects are available as Open Source, and
        you can download the code via Subversion from this site.  Some
        projects are closed source at the request of the authors, and only
        a restricted development team has access to the code.  See each
        project page for details.
    </p>
    <p>
        <a href="<?php echo $this->hubLongURL;?>/register">Become a member!</a>
        Sign up for a free <?php echo $this->hubShortName;?> account and use this site to manage
        your software project.
    </p>
	<p>
        <img src="<?php echo $this->image;?>" alt="<?php echo $this->forgeName;?>" />
    </p>
</div>
<div class="threecolumn middleright">

  <!-- Infrastructure -->
  <div>
    <h3>Infrastructure Projects</h3>
    <?php foreach ($this->appTools as $project) { ?>
        <p class="link"><a href="<?php echo $this->forgeURL;?>/infrastructure/<?php echo $project;?>/wiki"><?php echo $project;?></a></p>
    <?php } ?>
  </div>

</div>
<div class="clear"></div>
</div>

