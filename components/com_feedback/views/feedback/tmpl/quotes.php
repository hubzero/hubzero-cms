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

$this->css()
     ->js('quotes.js');

$base = rtrim(JURI::getInstance()->base(true), '/');
?>

<header id="content-header">
	<h2><?php echo JText::_('COM_FEEDBACK'); ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li>
				<a class="icon-add btn add" href="<?php echo JRoute::_('index.php?option=com_feedback&task=success_story'); ?>">
					<?php echo JText::_('COM_FEEDBACK_ADD_YOUR_STORY'); ?>
				</a>
			</li>
		</ul>
	</div>
</header><!-- / #content-header -->

<section class="main section">
	<div class="section-inner">
		<?php if (count($this->quotes) > 0) { ?>
			<?php foreach ($this->quotes as $quote) { ?>
				<div class="quote" id="<?php echo $quote->id; ?>">
					<div class="grid">
						<div class="col span2 omega">
							<p class="cite">
								<?php
								$quote->org = str_replace('<br>', '<br />', $quote->org);
								$user = $quote->user_id ? \Hubzero\User\Profile::getInstance($quote->user_id) : new \Hubzero\User\Profile();
								$userPicture = $user ? $user->getPicture() : $user->getPicture(true);
								echo '<img src="' . $userPicture . '" alt="' . $user->get('name') . '" width="75" height="75" /><br />';
								?>
								<cite><?php echo $this->escape(stripslashes($quote->fullname)); ?></cite>
								<br /><?php echo $this->escape(stripslashes($quote->org)); ?>
							</p>
						</div>
						<div class="col span10 omega">
						<?php if (isset($this->filters['id']) && $this->filters['id'] != '') { ?>
							<div class="breadcrumbs">
								<p>
									<a href="<?php echo $base; ?>/about/quotes" class="breadcrumbs"><?php echo JText::_('MOD_QUOTES_NOTABLE_QUOTES'); ?></a>
									&rsaquo;
									<strong><?php echo $this->escape(stripslashes($quote->fullname)); ?></strong>
								</p>
							</div>
						<?php } ?>
						<?php if (isset($this->filters['id']) && $this->filters['id'] != '') { ?>
							<blockquote cite="<?php echo $this->escape(stripslashes($quote->fullname)); ?>">
								<p>
									<?php echo $this->escape(stripslashes($quote->quote)); ?>
								</p>
							</blockquote>
						<?php } else { ?>
							<?php if ($quote->short_quote != $quote->quote) { ?>
								<div class="quote-short" id="<?php echo $quote->id; ?>-short" style="display: none">
									<blockquote cite="<?php echo $this->escape(stripslashes($quote->fullname)); ?>">
										<p>
											<?php echo $this->escape(rtrim(stripslashes($quote->short_quote), '.')); ?>
											&#8230;
											<a href="#" id="<?php echo $quote->id; ?>" class="show-more" title="<?php echo JText::sprintf('MOD_QUOTES_VIEW_QUOTE_BY', $this->escape(stripslashes($quote->fullname))); ?>">
												<?php echo JText::_('COM_FEEDBACK_MORE'); ?>
											</a>
										</p>
									</blockquote>
								</div>
								<div class="quote-long" id="<?php echo $quote->id; ?>-long">
									<blockquote cite="<?php echo $this->escape(stripslashes($quote->fullname)); ?>">
										<p>
											<?php echo $this->escape(stripslashes($quote->quote)); ?>
										</p>
									</blockquote>
								</div>
							<?php } else { ?>
								<blockquote cite="<?php echo $this->escape(stripslashes($quote->fullname)); ?>">
									<p>
										<?php echo $this->escape(stripslashes($quote->short_quote)); ?>
									</p>
								</blockquote>
							<?php } ?>
						<?php } ?>
						<?php
						if (is_dir(JPATH_ROOT . DS . $this->path . $quote->id))
						{
							$pictures = scandir(JPATH_ROOT . DS . $this->path . $quote->id);
							array_shift($pictures);
							array_shift($pictures);

							foreach ($pictures as $picture)
							{
								$file = JPATH_ROOT . DS . $this->path . $quote->id . DS . $picture;
								if (file_exists($file))
								{
									$this_size = filesize($file);

									list($ow, $oh, $type, $attr) = getimagesize($file);

									// scale if image is bigger than 120w x120h
									$num = max($ow/120, $oh/120);
									if ($num > 1)
									{
										$mw = round($ow/$num);
										$mh = round($oh/$num);
									}
									else
									{
										$mw = $ow;
										$mh = $oh;
									}
									echo '<a class="fancybox-inline" href="' . $this->path . $quote->id . DS . $picture . '">';
									echo '<img  src="' . $this->path . $quote->id . DS . $picture . '" height="' . $mh . '" width="' . $mw . '" alt=""/> ';
									echo '</a>';
								}
							}
						}
						?>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } else { ?>
			<p><?php echo JText::_('COM_FEEDBACK_NO_QUOTES_FOUND'); ?></p>
		<?php } ?>

		<input type="hidden" id="quoteid" name="quoteid" value="<?php echo $this->quoteId; ?>" />
	</div>
</section>
