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

$dateFormat  = '%d %b, %Y';
$timeFormat  = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat  = 'd M, Y';
	$timeFormat  = 'h:i a';
	$tz = true;
}

$rows = $this->offering->announcements(array('limit' => $this->params->get('display_limit', 1), 'published' => true));

$wikiconfig = array(
	'option'   => 'com_courses',
	'scope'    => 'courses',
	'pagename' => $this->offering->get('alias'),
	'pageid'   => 0,
	'filepath' => JPATH_ROOT . DS . 'site' . DS . 'courses' . DS . $this->course->get('id'),
	'domain'   => '' 
);
ximport('Hubzero_Wiki_Parser');
$p =& Hubzero_Wiki_Parser::getInstance();

if ($rows->total() > 0) 
{
	$announcements = array();

	foreach ($rows as $row)
	{
		if ($this->params->get('allowClose', 1))
		{
			if (!($hide = JRequest::getWord('ancmnt' . $row->get('id'), '', 'cookie')))
			{
				$announcements[] = $row;
			}
		}
	}

	if (count($announcements))
	{
?>
	<div class="announcements">
		<?php
		foreach ($announcements as $row)
		{
			?>
			<div class="announcement<?php if ($row->get('priority')) { echo ' high'; } ?>">
				<?php echo $p->parse(stripslashes($row->get('content')), $wikiconfig); ?>
				<dl class="entry-meta">
					<dt class="entry-id"><?php echo $row->get('id'); ?></dt> 
					<dd class="time">
						<time datetime="<?php echo $row->get('created'); ?>">
							<?php echo JHTML::_('date', $row->get('created'), $timeFormat, $tz); ?>
						</time>
					</dd>
					<dd class="date">
						<time datetime="<?php echo $row->get('created'); ?>">
							<?php echo JHTML::_('date', $row->get('created'), $dateFormat, $tz); ?>
						</time>
					</dd>
				</dl>
				<?php 
					$page = JRequest::getVar('REQUEST_URI', '', 'server');
					if ($page && $this->params->get('allowClose', 1)) 
					{
						$page .= (strstr($page, '?')) ? '&' : '?';
						$page .= 'ancmnt' . $row->get('id') . '=closed';
				?>
						<a class="close" href="<?php echo $page; ?>" data-id="<?php echo $row->get('id'); ?>" data-duration="<?php echo $this->params->get('closeDuration', 30); ?>" title="<?php echo JText::_('Close this announcement'); ?>">
							<span><?php echo JText::_('close'); ?></span>
						</a>
				<?php
					}
				?>
			</div>
			<?php
		}
		?>
	</div>
	<?php
	}
}