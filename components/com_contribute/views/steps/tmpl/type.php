<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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

$jconfig =& JFactory::getConfig();
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>

<div class="main section withleft">
	<div class="aside">
		<ul>
			<?php
			if ($this->types) {
				foreach ($this->types as $type)
				{
					if ($type->contributable == 1) {
						if ($type->id == 7) {
							$url = '/contribute/tools/register';
						} else {
							$url = JRoute::_('index.php?option='.$this->option.'&step='.$this->step.'&type='.$type->id);
						}
						echo '<li><a class="tooltips" href="'.$url.'" title="'.htmlentities(stripslashes($type->type), ENT_QUOTES).' :: '.htmlentities(stripslashes($type->description), ENT_QUOTES).'">'.stripslashes($type->type).'</a></li>'."\n";
					}
				}
			}
			?>
		</ul>
	</div><!-- /.aside -->
	<div class="subject">
		<p>Select one of the resource types listed to proceed to the next step. The type of resource chosen can affect what information you will need to provide in the following steps.</p>

		<h4>What if I want to contribute a type not listed here?</h4>
		<p>If you feel your contribution does not fit into any of our predefined types, please <a href="feedback/report_problems/">contact us</a> with details of</p>
		<ol>
			<li>what you wish to contribute, including a description and file types</li>
			<li>how you believe it should be categorized</li>
		</ol>
		<p>We will try to accomodate you or provide another suggestion.</p>

		<p>In order for <?php echo $jconfig->getValue('config.sitename'); ?> to display your content, we must be given legal license to do so. At the very least, <?php echo $jconfig->getValue('config.sitename'); ?> must be authorized to 
		hold, copy, distribute, and perform (play back) your material according to <a class="popup" href="/legal/license">this agreement</a>. 
		You will retain any copyrights to the materials and decide how they should be licensed for end-user access. We encourage you to <a class="popup" href="/legal/licensing">license your contributions</a> 
		so that others can build upon them.</p>
	</div><!-- /.subject -->
	<div class="clear"></div>
</div><!-- /.main section -->
