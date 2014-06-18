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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$base = rtrim(JURI::getInstance()->base(true), '/');
?>
<?php if ($this->params->get('button', 0) == 1) { ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_feedback&task=success_story'); ?>" class="icon-add btn add">
					<?php echo JText::_('MOD_QUOTES_ADD_YOUR_STORY'); ?>
				</a>
			</li>
		</ul>
	</div>
<?php } ?>

<div id="quotes-container">
	<?php if ($this->params->get('cycle', 0) == 1) { ?>
		<div id="shuffle">
	<?php } ?>
	<?php if (count($this->quotes) > 0) { ?>
		<?php foreach ($this->quotes as $quote) { ?>
			<div class="quote">
				<?php if (isset($this->filters['id']) && $this->filters['id'] != '') { ?>
					<div class="breadcrumbs">
						<p>
							<a href="<?php echo $base; ?>/about/quotes" class="breadcrumbs"><?php echo JText::_('MOD_QUOTES_NOTABLE_QUOTES'); ?></a>
							&rsaquo;
							<strong><?php echo $this->escape(stripslashes($quote->fullname)); ?></strong>
						</p>
					</div>
				<?php } ?>
				<blockquote cite="<?php echo $this->escape(stripslashes($quote->fullname)); ?>">
				<?php if (isset($this->filters['id']) && $this->filters['id'] != '') { ?>
					<p>
						<?php echo $this->escape(stripslashes($quote->quote)); ?>
					</p>
				<?php } else { ?>
					<p>
					<?php if ($quote->short_quote != $quote->quote) { ?>
						<?php echo $this->escape(rtrim(stripslashes($quote->short_quote), '.')); ?>
						 &#8230;
						<a href="<?php echo $base; ?>/about/quotes/?quoteid=<?php echo $quote->id; ?>" title="<?php echo JText::sprintf('MOD_QUOTES_VIEW_QUOTE_BY', $this->escape(stripslashes($quote->fullname))); ?>">
							<?php echo JText::_('MOD_QUOTES_MORE'); ?>
						</a>
					<?php } else { ?>
						<?php echo $this->escape(stripslashes($quote->short_quote)); ?>
					<?php } ?>
					</p>
				<?php } ?>
				</blockquote>
				<p class="cite">
					<?php
					$user = JFactory::getUser($quote->user_id);
					$userPicture = \Hubzero\User\Profile::getInstance($quote->user_id)->getPicture();
					echo '<img src="' . $userPicture . '" alt="' . $user->get('name') . '" width="30" height="30" />';
					?>
					<cite><?php echo $this->escape(stripslashes($quote->fullname)); ?></cite>
					<br /><?php echo $this->escape(stripslashes($quote->org)); ?>
				</p>
				<?php
				if (is_dir(JPATH_ROOT . DS .$this->path . $quote->id))
				{
					$pictures = scandir(JPATH_ROOT . DS .$this->path . $quote->id);
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
							?>
							<a class="fancybox-inline" href="<?php echo $this->path . $quote->id . DS . $picture; ?>">
								<img  src="<?php echo $this->path . $quote->id . DS . $picture . '" height="' . $mh . '" width="' . $mw; ?>" alt="" />
							</a>
							<?php
						}
					}
				}
				?>
				<?php if ($this->params->get('cycle', 0) == 0) { ?>
					<hr />
				<?php } ?>
			</div>
		<?php } ?>
	<?php } else { ?>
		<p><?php echo JText::_('MOD_QUOTES_NO_QUOTES_FOUND'); ?></p>
	<?php } ?>
	<?php if ($this->params->get('cycle', 0) == 1) { ?>
		</div>
	<?php } ?>
</div>
<script type="text/javascript">
jQuery(document).ready(function() {
	$('.fancybox-inline').fancybox();
	$('#shuffle').cycle({
		fx: '<?php echo $this->params->get('cycle_fx', 'fade'); ?>',
		timeout: '<?php echo $this->params->get('cycle_speed', 1000); ?>',
	});
});
</script>