<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

$document =& JFactory::getDocument();
$document->setTitle( $this->forgeName );

$app =& JFactory::getApplication();
$pathway =& $app->getPathway();
if (count($pathway->getPathWay()) <= 0) {
	$pathway->addItem( $this->forgeName, 'index.php?option=com_tools' );
}
?>

<div class="full" id="content-header">
	<h2><?php echo $this->forgeName;?></h2>
</div>
<div class="main section" id="forge">
<div class="threecolumn farleft">
    <p>
        Welcome to <?php echo $this->forgeName;?>, the tool
        development area of <a href="<?php echo $this->hubLongURL;?>"><?php echo $this->hubShortURL;?></a>.
        The following pages are maintained by the various owners of each
        tool.  Many of these tools are available as Open Source, and
        you can download the code via Subversion from this site.  Some
        tools are closed source at the request of the authors, and only
        a restricted development team has access to the code.  See each
        tool page for details.
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

  <!-- Tools -->
  <div>
    <h3>Tools</h3>
    <?php foreach ($this->appTools as $project) { ?>
        <p class="link"><a href="<?php echo $this->forgeURL;?>/tools/<?php echo $project;?>/wiki"><?php echo $project;?></a></p>
    <?php } ?>
  </div>

</div>
<div class="clear"></div>
</div>
