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