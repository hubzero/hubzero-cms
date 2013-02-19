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

$dateFormat  = '%d %b, %Y';
$timeFormat  = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat  = 'd M, Y';
	$timeFormat  = 'h:i a';
	$tz = true;
}

$juser = JFactory::getUser();
//$offering = $this->course->offering();
$filters = $this->filters;
$filters['count'] = true;

$total = $this->offering->announcements($filters);

$filters['count'] = false;

$rows = $this->offering->announcements($filters);
$manager = $this->offering->access('manage');

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

$base = 'index.php?option='.$this->option.'&gid='.$this->course->get('alias').'&offering=' . $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : '') . '&active=announcements';
?>
<div class="course_members">
	<h3 class="heading">
		<a name="members"></a>
		<?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS'); ?>
	</h3>

	<form action="<?php echo JRoute::_($base); ?>" method="post">
		<div class="subject">

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('Search announcements'); ?></legend>
					<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" />
				</fieldset>
			</div><!-- / .container -->
			<?php if ($manager) { ?>
				<p class="btn-container">
					<a class="add btn" href="<?php echo JRoute::_($base . '&action=new'); ?>">
						<?php echo JText::_('New announcement'); ?>
					</a>
				</p>
			<?php } ?>
			<div class="container">

<?php if ($rows->total() > 0) { ?>
	<?php foreach ($rows as $row) { ?>
						<div class="announcement<?php if ($row->get('priority')) { echo ' high'; } ?>">
							<?php echo $p->parse(stripslashes($row->get('content')), $wikiconfig); ?>
							<dl class="entry-meta">
								<dt class="entry-id"><?php echo $row->get('id'); ?></dt> 
								<?php if ($manager) { ?>
									<dd class="entry-author">
										<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>
									</dd>
								<?php } ?>
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
						<?php if ($manager) { ?>
								<dd class="entry-options">
								<?php if ($juser->get('id') == $row->get('created_by')) { ?>
									<a class="edit" href="<?php echo JRoute::_($base . '&action=edit&entry=' . $row->get('id')); ?>" title="<?php echo JText::_('Edit'); ?>">
										<?php echo JText::_('Edit'); ?>
									</a>
									<a class="delete" href="<?php echo JRoute::_($base . '&action=delete&entry=' . $row->get('id')); ?>" title="<?php echo JText::_('Delete'); ?>">
										<?php echo JText::_('Delete'); ?>
									</a>
								<?php } ?>
								</dd>
						<?php } ?>
							</dl>
						</div>
	<?php } ?>
<?php } else { ?>
					<p><?php echo JText::_('PLG_COURSES_MEMBERS_NO_RESULTS'); ?></p>
<?php } ?>

			<?php 
			jimport('joomla.html.pagination');
			$pageNav = new JPagination(
				$total, 
				$this->filters['start'], 
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('gid', $this->course->get('alias'));
			$pageNav->setAdditionalUrlParam('offering', $this->offering->get('alias'));
			$pageNav->setAdditionalUrlParam('active', 'announcements');
			echo $pageNav->getListFooter();
			?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<div class="clear"></div>

		<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
		<input type="hidden" name="offering" value="<?php echo $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : ''); ?>" />
		<input type="hidden" name="active" value="announcements" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="filter" value="<?php echo $this->filter; ?>" />
	</form>
</div><!--/ #course_members -->
