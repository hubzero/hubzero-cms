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
defined('_JEXEC') or die('Restricted access');

$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => '',
	'pagename' => $this->course->get('alias'),
	'pageid'   => $this->course->get('id'),
	'filepath' => DS . ltrim($this->course->config()->get('uploadpath', '/site/courses'), DS),
	'domain'   => $this->course->get('alias')
);

ximport('Hubzero_Wiki_Parser');
$parser = Hubzero_Wiki_Parser::getInstance();

	echo $parser->parse(stripslashes($this->course->get('description')), $wikiconfig);

	/*$instructors = $this->course->instructors();
	if (count($instructors) > 0) 
	{
?>
		<h3><?php echo (count($instructors) > 1) ? JText::_('About the instructors') : JText::_('About the instructor'); ?></h3>
<?php
		ximport('Hubzero_View_Helper_Html');
		ximport('Hubzero_User_Profile_Helper');

		foreach ($instructors as $i)
		{
			$instructor = Hubzero_User_Profile::getInstance($i->get('user_id'));
?>
		<div class="course-instructor">
			<p class="course-instructor-photo">
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($instructor, 0); ?>" alt="" />
			</p>
			<div class="course-instructor-content">
				<h4>
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $instructor->get('id')); ?>">
						<?php echo $this->escape(stripslashes($instructor->get('name'))); ?>
					</a>
				</h4>
				<p class="course-instructor-bio">
				<?php if ($instructor->get('bio')) { ?>
					<?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($instructor->get('bio')), 300, 0); ?>
				<?php } else { ?>
					<em><?php echo JText::_('This instructor has yet to write their bio.'); ?></em>
				<?php } ?>
				</p>
				<div class="clearfix"></div>
			</div>
		</div>
<?php
		}
	}*/
