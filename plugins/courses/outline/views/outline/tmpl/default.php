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
$oparams        = new JRegistry($this->course->offering()->get('params'));
$sparams        = new JRegistry($this->course->offering()->section()->get('params'));

$price = 'free';
if ($oparams->get('store_price', false))
{
	$price = 'only $' . $oparams->get('store_price');
}

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

if (!$this->course->offering()->access('view') && !$sparams->get('preview', 0)) { ?>
	<p class="info"><?php echo JText::_('Access to the "Syllabus" section of this course is restricted to members only. You must be a member to view the content.'); ?></p>
<?php } else { ?>

	<?php if ($this->course->access('manage')) { ?>
		<div class="manager-options">
			<span><strong>Manage the content of the outline here.</strong></span> <a class="btn edit icon-edit" href="<?php echo JRoute::_($base . '&active=outline&action=build'); ?>">Build outline</a>
		</div>
	<?php } ?>

	<div id="course-outline">
		<?php if (!$this->course->offering()->access('view') && $sparams->get('preview', 0)) : ?>
			<div class="advertise-enroll">
				<div class="advertise-text">
					<?php echo JText::_('You\'re currently viewing this course in preview mode. Some features may be disabled.'); ?>
				</div>
				<a href="<?php echo JRoute::_($this->course->offering()->link('enroll')); ?>">
					<div class="advertise-action btn">Enroll for <?php echo $price; ?>!</div>
				</a>
				<a target="_blank" class="advertise-popup" href="<?php echo JRoute::_('index.php?option=com_help&component=courses&page=basics#why_enroll'); ?>">
					<div class="advertise-help btn">Why enroll?</div>
				</a>
			</div>
		<?php endif; ?>
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

				$this->member  = $this->course->offering()->section()->member(JFactory::getUser()->get('id'));
				$progress      = ($this->member->get('id')) ? $this->course->offering()->gradebook()->progress($this->member->get('id')) : array();
				if (is_null($this->member->get('section_id')))
				{
					$this->member->set('section_id', $this->course->offering()->section()->get('id'));
				}
				$prerequisites = $this->member->prerequisites($this->course->offering()->gradebook());
			?>
		</div>

<?php
	// Build array of unit titles
	$unitTitles = array();
	foreach ($this->course->offering()->units() as $unit)
	{
		$unitTitles[$unit->get('id')] = $unit->get('title');
	}
?>

<?php if ($this->course->offering()->units()->total() > 0) : ?>

	<?php if (($this->course->offering()->section()->started() && !$this->course->offering()->section()->ended()) || $isManager) { ?>

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

		<?php
			$complete = isset($progress[$this->member->get('id')][$unit->get('id')]['percentage_complete'])
					? $progress[$this->member->get('id')][$unit->get('id')]['percentage_complete']
					: 0;
			$margin   = 100 - $complete;
			$done     = ($complete == 100) ? ' complete' : '';
		?>

		<div class="unit<?php echo ($i == 0) ? ' active' : ''; ?> unit-<?php echo ($i + 1); echo $cls; ?>">
			<div class="unit-fill">
				<div class="unit-fill-inner<?php echo $done; ?>" style="height:<?php echo $complete; ?>%;margin-top:<?php echo $margin; ?>%;"></div>
			</div>
			<div class="unit-wrap">
				<div class="unit-content<?php echo ($unit->isAvailable()) ? ' open' : ''; ?>" data-id="<?php echo $unit->get('id'); ?>">
					<h3 class="unit-content-available">
						<?php echo $this->escape(stripslashes($unit->get('title'))); ?>
					</h3>

					<div class="unit-availability<?php if (!$unit->started()) { echo ' comingSoon'; } ?>">
						<div class="details">
							<div class="unit-description">
								<?php echo $this->escape(stripslashes($unit->get('description'))); ?>
							</div>

				<?php if (!$this->course->offering()->access('view') && $sparams->get('preview', 0) == 2 && $unit->get('ordering') > 1) { ?>
							<div class="grid">
								<p class="info">
									Content for this unit is only available to enrolled students.
									<a href="<?php echo JRoute::_($this->course->offering()->link('enroll')); ?>">
										Enroll for <?php echo $price; ?>!
									</a>
								</p>
							</div>
				<?php } elseif (!$isManager && !$unit->started()) { ?>
							<div class="grid">
								<p class="info">
									Content for this unit will be available starting <?php echo JHTML::_('date', $unit->get('publish_up'), "F j, Y, g:i a T"); ?>.
								</p>
							</div>
				<?php } elseif (!$isManager && $unit->ended()) { ?>
							<div class="grid">
								<p class="info">
									Content for this unit expired on <?php echo JHTML::_('date', $unit->get('publish_down'), "F j, Y, g:i a T"); ?>.
								</p>
							</div>
				<?php } else if (!$isManager && !$prerequisites->hasMet('unit', $unit->get('id'))) { ?>
							<div class="grid">
								<p class="info">
									This unit has prerequisites that have not yet been met. Begin by completing:
									<?php foreach ($prerequisites->get('unit', $unit->get('id')) as $prereq) : ?>
										<?php echo $unitTitles[$prereq['scope_id']]; ?>
									<?php endforeach; ?>
								</p>
							</div>
				<?php } else { ?>
						<?php $k = 0; ?>

						<?php foreach ($unit->assetgroups(null, $filters) as $agt) { ?>
							<?php if ((($agt->isAvailable() && $agt->isPublished()) || $isManager) && count($agt->children()) > 0) { ?>
									<?php
									$cls = '';
									if (!$agt->started())
									{
										$cls = ' pending';
									}
									if ($agt->ended())
									{
										$cls = ' unpublished';
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
									<div class="grid <?php echo $cls; ?>">
										<div class="col span4">
											<h4 class="asset-group-title">
												<?php echo $this->escape(stripslashes($agt->get('title'))); ?>
											</h4>
										<?php if ($agt->get('description')) { ?>
											<p class="asset-group-description">
												<?php echo $this->escape(stripslashes($agt->get('description'))); ?>
											</p>
										<?php } ?>
										</div>

										<div class="col span8 omega">
									<?php foreach ($agt->children() as $ag) { ?>
										<?php if (($ag->isAvailable() && $ag->isPublished()) || $isManager) :
											if ($ag->isDeleted())
											{
												continue;
											}

											$acls = '';
											if ($ag->isDraft())
											{
												$acls = ' draft';
											}
											if (!$ag->started())
											{
												$acls = ' pending';
											}
											if ($ag->ended())
											{
												$acls = ' ended';
											}
											if ($ag->isUnpublished())
											{
												$acls = ' unpublished';
											}
										?>
											<div class="asset-group">
												<ul class="asset-list">
												<?php
												$play = '';
												$found = array();

												if ($ag->assets()->total())
												{
													// Loop through the assets
													$k = 0;
													$hasPrimaryVideo = false;
													foreach ($ag->assets() as $a)
													{
														if ((($a->isAvailable() || $a->get('type') == 'form') && $a->isPublished()) || $isManager)
														{
															if ($a->isDeleted())
															{
																continue;
															}

															$cls = '';

															if (!$a->started())
															{
																$cls = 'pending';
															}
															if ($a->ended())
															{
																$cls = 'ended';
															}
															if ($a->isDraft())
															{
																$cls = 'draft';
															}
															if ($a->isUnpublished())
															{
																$cls = 'unavailable';
															}

															$href = JRoute::_($base . '&asset=' . $a->get('id'));
															$target = '';
															if ($a->get('type') == 'video' && !$hasPrimaryVideo)
															{
																$hasPrimaryVideo = true;
																$href = JRoute::_($base . '&active=outline&unit=' . $unit->get('alias') . '&b=' . $ag->get('alias'));
															}
															else if ($a->get('type') == 'file' || $a->get('type') == 'url')
															{
																$target = ' target="_blank"';
															}

															$link = '<a class="' . $cls . '" href="' . $href . '"' . $target . '>' . $this->escape(stripslashes($a->get('title'))) . '</a>';

															// Finally, make sure prereqs have been met
															if (!$prerequisites->hasMet('asset', $a->get('id')) && !$isManager)
															{
																$info  = "This item has prerequisites that have not yet been met. Begin by completing: ";
																$items = array();
																foreach ($prerequisites->get('asset', $a->get('id')) as $prereq)
																{
																	$reqAsset = new CoursesModelAsset($prereq['scope_id']);
																	$items[] = $reqAsset->get('title');
																}
																$info .= implode(", ", $items);
																$link = '<span title="' . $info . '" class="unavailable hasTip">' . $this->escape(stripslashes($a->get('title'))) . '</span>';
															}

															$found[] = '<li>' . $link . '</li>';

															//if ($a->get('type') == 'video')
															if ($k == 0)
															{
																$play = '<a class="asset-primary ' . $cls . '" href="' . $href . '"' . $target . '>' . $this->escape(stripslashes($ag->get('title'))) . '</a>';
															}
															$k++;
														}
													}
												}
												?>
													<li class="collapsed">
														<?php
														if (count($found) == 1)
														{
															echo $play;
														}
														else
														{
															echo '<span class="asset-primary' . $acls . '">' . $this->escape(stripslashes($ag->get('title'))) . '<span class="asset-more"></span></span>';
														}
														?>
														<?php
														if (count($found) > 1)
														{
															echo '<ul>' . implode("\n", $found) . '</ul>';
														}
														?>
													</li>
												</ul>
											</div><!-- / .asset-group -->
										<?php endif; ?>
									<?php } // foreach ($agt->children() as $ag) ?>

										<?php if ($agt->assets()->total()) { ?>
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

														if (!$a->started())
														{
															$cls = ' pending';
														}
														if ($a->ended())
														{
															$cls = ' unpublished';
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
														$target = '';
														if ($a->get('type') == 'video')
														{
															$href = JRoute::_($base . '&active=outline&unit=' . $unit->get('alias') . '&b=' . $ag->get('alias'));
														}
														else if ($a->get('type') == 'file' || $a->get('type') == 'url')
														{
															$target = ' target="_blank"';
														}
														echo '<li><a class="asset-primary ' . $a->get('subtype') . '" href="' . $href . '"' . $target . '>' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
													}
												}
												?>
											</ul>
											<?php
											$agt->assets()->rewind();
											foreach ($agt->assets() as $a)
											{
												if ($a->isAvailable())
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
										</div><!-- / .col -->
									</div><!--  .grid -->

								<?php $k++; ?>
							<?php } ?>
						<?php } // foreach ($unit->assetgroups() as $agt) ?>

						<?php if ($unit->assets()->total()) { ?>
							<ul class="asset-list">
								<?php
								foreach ($unit->assets() as $a)
								{
									if ($a->isAvailable() || $isManager)
									{
										$href = JRoute::_($base . '&asset=' . $a->get('id')); //$a->path($this->course->get('id'));
										$target = '';
										if ($a->get('type') == 'video')
										{
											$href = JRoute::_($base . '&active=outline&unit=' . $unit->get('alias') . '&b=' . $ag->get('alias'));
										}
										else if ($a->get('type') == 'file' || $a->get('type') == 'url')
										{
											$target = ' target="_blank"';
										}
										echo '<li><a class="asset ' . $a->get('subtype') . '" href="' . $href . '"' . $target . '>' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
										$k++;
									}
								}
								?>
							</ul>
						<?php } ?>

						<?php if (!$k) { ?>
							<div class="grid">
								<p class="info">
									No content found or existing content has expired and is no longer available for this unit.
								</p>
							</div>
						<?php } ?>

				<?php } // close else ?>
						</div><!-- / .details -->
					</div><!-- / .unit-availability -->
				</div><!-- / .unit-content -->
			</div><!-- / .unit-wrap -->
		</div><!-- / .unit -->
		<?php } ?>
	<?php } // close foreach ?>

	<?php } else { ?>
		<p class="warning">The access time for this section has expired and the content is no longer available.</p>
	<?php } ?>

<?php elseif ($this->course->offering()->access('manage')) : ?>
		<p class="info">Your outline is currently empty. Go to the <a href="<?php echo JRoute::_($base . '&active=outline&action=build'); ?>">Outline Builder</a> to begin creating your course outline.</p>
<?php else : ?>
		<p class="info">There is currently no outline available for this course.</p>
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