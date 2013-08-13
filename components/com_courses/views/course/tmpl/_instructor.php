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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if ($b = $this->instructor->get('bio'))
{
	$bio = stripslashes($b);

	$wikiconfig = array(
		'option'   => 'com_members',
		'scope'    => 'members' . DS . 'profile',
		'pagename' => 'member',
		'pageid'   => 0,
		'filepath' => '',
		'domain'   => '' 
	);
	ximport('Hubzero_Wiki_Parser');
	$p =& Hubzero_Wiki_Parser::getInstance();

	/*$appendmore = false;
	if (strlen($b) > 200) 
	{
		$appendmore = true;
		$b = Hubzero_View_Helper_Html::shortenText($b, 200, 0);
	}*/

	$bio = $p->parse($b, $wikiconfig, false);
	/*if (strlen($b) > 200) 
	{
		$bio .= '<p><a class="more" href="' . JRoute::_('index.php?option=com_members&id=' . $this->instructor->get('uidNumber')) . '">' . JText::_('More...') . '</a></p>';
	}*/
}
?>
<div class="course-instructor">
	<p class="course-instructor-photo">
		<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->instructor->get('uidNumber')); ?>">
			<img src="<?php echo $this->instructor->getPicture(); ?>" alt="<?php echo JText::sprintf('%s\'s photo', $this->escape(stripslashes($this->instructor->get('name')))); ?>" />
		</a>
	</p>

	<div class="course-instructor-content cf">
		<h4>
			<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->instructor->get('uidNumber')); ?>">
				<?php echo $this->escape(stripslashes($this->instructor->get('name'))); ?>
			</a>
		</h4>
		<p class="course-instructor-org">
			<?php echo $this->escape(stripslashes($this->instructor->get('organization'))); ?>
		</p>
	</div><!-- / .course-instructor-content cf -->

	<div class="course-instructor-bio">
		<?php if ($bio) { ?>
			<?php echo $bio; ?>
		<?php } else { ?>
			<em><?php echo JText::_('This instructor has yet to write their bio.'); ?></em>
		<?php } ?>
	</div>
</div><!-- / .course-instructor -->