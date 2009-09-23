<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2008-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2008-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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
