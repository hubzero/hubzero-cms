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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// ---------------
// Course Outline
// ---------------

// Member and manager checks
$isMember       = $this->course->access('view'); //$this->config->get('access-view-course');
$isManager      = $this->course->access('manage'); //$this->config->get('access-manage-course');
$isNowOnManager = ($isManager) ? true : false;

$filters = array();
if ($isManager)
{
	$filters['state'] = -1;
}

if (JRequest::getInt('nonadmin', 0) == 1) 
{ 
	$isNowOnManager = false;
}

$this->database = JFactory::getDBO();

$base = $this->course->offering()->link();

// Get the current time
$now = JFactory::getDate()->toSql();

$i = 0;

if (!$this->course->offering()->access('view')) { ?>
	<p class="info"><?php echo JText::_('Access to the "Syllabus" section of this course is restricted to members only. You must be a member to view the content.'); ?></p>
<?php } else { ?>

	<div id="course-outline">
		<div class="outline-head">
			<?php
				// Trigger event
				$dispatcher = JDispatcher::getInstance();
				$results = $dispatcher->trigger('onCourseBeforeOutline', array(
					$this->course,
					$this->course->offering()
				));
				// Output results
				echo implode("\n", $results);
			?>
		</div>
<?php if ($this->course->offering()->units($filters)->total() > 0) : ?>
	<?php foreach ($this->course->offering()->units() as $i => $unit) { ?>
		<?php if ((!$isManager && $unit->isPublished()) || $isManager) { 
				$cls = '';
				if (!$unit->isAvailable())
				{
					$cls = ' pending';
				}
				if ($unit->isDraft())
				{
					$cls = ' draft';
				}
				
				if ($unit->isUnpublished())
				{
					$cls = ' unpublished';
				}
				if ($unit->isDeleted())
				{
					continue;
				}
				
			?>
		<div class="unit<?php echo ($i == 0) ? ' active' : ''; ?> unit-<?php echo ($i + 1); echo $cls; ?>">
			<div class="unit-wrap">
				<div class="unit-content<?php echo ($unit->isAvailable()) ? ' open' : ''; ?>">
					<h3 class="unit-content-available">
						<span><?php echo $this->escape(stripslashes($unit->get('title'))); ?></span> 
						<?php echo $this->escape(stripslashes($unit->get('description'))); ?>
					</h3>

				<?php if (!$isManager && !$unit->started()) { ?>
					<div class="unit-availability comingSoon">
						<!-- <p class="status">Coming soon</p> -->
						<p class="info">
							Content for this unit will be available starting <?php echo JHTML::_('date', $unit->get('publish_up'), "F j, Y, g:i a T"); ?>.
						</p>
				<?php } elseif (!$isManager && $unit->ended()) { ?>
					<div class="unit-availability comingSoon">
						<!-- <p class="status">Coming soon</p> -->
						<p class="info">
							Content for this unit expired on <?php echo JHTML::_('date', $unit->get('publish_down'), "F j, Y, g:i a T"); ?>.
						</p>
				<?php } else { ?>
					<div class="unit-availability">
						
						<!-- <p class="status posted">Posted</p> -->
					<?php if (!$unit->assetgroups(null, $filters)->total() && !$unit->assets()->total()) { ?>
						<div class="details empty">
							<p class="info">
								No content found for this unit.
							</p>
					<?php } else { ?>
						<div class="details notempty clearfix">
							<div class="detailsWrapper">
								<?php foreach ($unit->assetgroups() as $agt) { ?>
									<?php if ((($agt->isAvailable() && $agt->isPublished()) || $isManager) && count($agt->children()) > 0) { 
											$cls = '';
											if (!$agt->isAvailable())
											{
												$cls = ' pending';
											}
											if ($agt->isDraft())
											{
												$cls = ' draft';
											}
											
											if ($agt->isUnpublished())
											{
												$cls = ' unpublished';
											}
											
											if ($agt->isDeleted())
											{
												continue;
											}
											
										?>
										<div class="weeksection">
											<div class="weeksectioninner<?php echo $cls; ?>">
												<h4>
													<?php echo $this->escape(stripslashes($agt->get('title'))); ?>
												</h4>
											<?php if ($agt->get('description')) { ?>
												<p class="asset-group-description">
													<?php echo $this->escape(stripslashes($agt->get('description'))); ?>
												</p>
											<?php } ?>
											<?php foreach ($agt->children() as $ag) { ?>
												<?php if (($ag->isAvailable() && $ag->isPublished()) || $isManager) : 
													$cls = '';
													if ($ag->isDraft())
													{
														$cls = ' draft';
													}
													if (!$ag->isAvailable())
													{
														$cls = ' pending';
													}
													if ($ag->isUnpublished())
													{
														$cls = ' unpublished';
													}
													
													if ($ag->isDeleted())
													{
														continue;
													}
													
												?>
													<div class="asset-group<?php echo $cls; ?>">
														<?php if (trim($ag->get('title')) !== '--') : ?>
															<h5>
																<?php echo $this->escape(stripslashes($ag->get('title'))); ?>
															</h5>
														<?php endif; ?>
														<?php if ($ag->assets($filters)->total()) { ?>
														<ul class="asset-list">
															<?php
															// Loop through the assets
															foreach ($ag->assets() as $a)
															{
																if ((($a->isAvailable() || $a->get('type') == 'form') && $a->isPublished()) || $isManager)
																{
																	$cls = '';
																	if (!$a->isAvailable())
																	{
																		$cls = ' pending';
																	}
																	if ($a->isDraft())
																	{
																		$cls = ' draft';
																	}
																	
																	if ($a->isUnpublished())
																	{
																		$cls = ' unpublished';
																	}
																	
																	if ($a->isDeleted())
																	{
																		continue;
																	}
																	

																	$href = JRoute::_($base . '&asset=' . $a->get('id'));
																	$target = ' target="_blank"';
																	if ($a->get('type') == 'video')
																	{
																		$href = JRoute::_($base . '&active=outline&unit=' . $unit->get('alias') . '&b=' . $ag->get('alias'));
																		$target = '';
																	}
																	else if ($a->get('type') == 'form')
																	{
																		$target = '';
																	}
																	echo '<li><a class="asset ' . $a->get('subtype') . $cls . '" href="' . $href . '"' . $target . '>' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
																}
															}
															?>
														</ul>
														<?php } ?>
													</div>
												<?php endif; ?>
											<?php } ?>

											<?php if ($agt->assets($filters)->total()) { ?>
												<ul class="asset-list">
													<?php
													foreach ($agt->assets() as $a)
													{
														if ($a->isAvailable() || $isManager)
														{
															if ($a->get('subtype') == 'note')
															{
																continue;
															}

															$cls = '';
															
															if (!$a->isAvailable())
															{
																$cls = ' pending';
															}
															if ($a->isDraft())
															{
																$cls = ' draft';
															}
															if ($a->isUnpublished())
															{
																$cls = ' unpublished';
															}
															
															if ($a->isDeleted())
															{
																continue;
															}
															

															$href = JRoute::_($base . '&asset=' . $a->get('id')); //$a->path($this->course->get('id'));
															$target = ' target="_blank"';
															if ($a->get('type') == 'video')
															{
																$href = JRoute::_($base . '&active=outline&unit=' . $unit->get('alias') . '&b=' . $agt->get('alias'));
																$target = '';
															}
															else if ($a->get('type') == 'form')
															{
																$target = '';
															}
															echo '<li><a class="asset ' . $a->get('subtype') . $cls . '" href="' . $href . '"' . $target . '>' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
														}
													}
													?>
												</ul>
												<?php
												$agt->assets()->rewind();
												foreach ($agt->assets() as $a)
												{
													if ($a->isAvailable() || $isManager)
													{
														if ($a->get('subtype') != 'note')
														{
															continue;
														}
														echo '<p class="info">' . stripslashes($a->get('content')) . '</p>';
													}
												}
												?>
											<?php } ?>
											</div><!-- .weeksectioninner -->
										</div><!-- / .weekSection -->
									<?php } ?>
								<?php } //$i++; ?>
							</div>
							<?php if ($unit->assets()->total()) { ?>
							<ul class="asset-list">
						<?php
						foreach ($unit->assets($filters) as $a)
						{
							if ($a->isAvailable() || $isManager)
							{
								$href = JRoute::_($base . '&asset=' . $a->get('id')); //$a->path($this->course->get('id'));
								$target = ' target="_blank"';
								if ($a->get('type') == 'video')
								{
									$href = JRoute::_($base . '&active=outline&a=' . $unit->get('alias'));
									$target = '';
								}
								else if ($a->get('type') == 'form')
								{
									$target = '';
								}
								echo '<li><a class="asset ' . $a->get('subtype') . '" href="' . $href . '"' . $target . '>' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
							}
						}
						?>
							</ul>
							<?php } ?>
					<?php } ?>
						</div><!-- / .details -->
				<?php } // close else ?>
					</div><!-- / .unit-availability -->
				</div><!-- / .unit-content -->
			</div><!-- / .unit-wrap -->
		</div><!-- / .unit -->
		<?php } ?>
	<?php } // close foreach ?>
<?php elseif($this->course->offering()->access('manage')) : ?>
		<p class="info">Your outline is currently empty. Go to the <a href="<?php echo JRoute::_($base . '&active=outline&action=build'); ?>">Outline Builder</a> to begin creating your course outline.</p>
<?php else : ?>
		<p class="info">There is currently no outline available for this course</p>
<?php endif; ?>
	</div><!-- / #course-outline -->

	<?php
		// Trigger event
		$results = $dispatcher->trigger('onCourseAfterOutline', array(
			$this->course,
			$this->course->offering()
		));
		// Output results
		echo implode("\n", $results);
	?>

<?php } // end if ?>