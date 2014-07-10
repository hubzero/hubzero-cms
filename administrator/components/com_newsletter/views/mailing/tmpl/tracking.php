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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//set title
JToolBarHelper::title(JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_STATS'), 'stats.png');

//add buttons
JToolBarHelper::custom('cancel', 'back', '', 'COM_NEWSLETTER_TOOLBAR_BACK', false);
?>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_STATISTIC'); ?></th>
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_VALUE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_OPENRATE'); ?>:</th>
				<td>
					<?php
						if (count($this->recipients) > 0)
						{
							echo number_format((count($this->opens) / count($this->recipients)) * 100) . '% ';
							echo JText::sprintf('COM_NEWSLETTER_NEWSLETTER_MAILING_OPENED', count($this->opens), count($this->recipients));
						}
					?>
				</td>
			</tr>
			<tr>
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_BOUNCERATE'); ?>:</th>
				<td>
					<?php
						if (count($this->recipients) > 0)
						{
							echo (count($this->bounces) / count($this->recipients)) * 100 . '% ';
						}
					?>
				</td>
			</tr>
			<tr>
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_FORWARDS'); ?>:</th>
				<td>
					<?php echo count($this->forwards); ?>
				</td>
			</tr>
			<tr>
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_PRINTS'); ?>:</th>
				<td>
					<?php echo count($this->prints); ?>
				</td>
			</tr>
		</tbody>
	</table>
	<br />
	<hr />

	<h3><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_OPENS_BY_LOCATION'); ?></h3>
	<div class="col width-30">
		<table class="adminlist">
			<thead>
				<tr>
					<th colspan="2"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_TOP_LOCATIONS'); ?></th>
					<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_TOP_LOCATIONS_OPENS_COUNT'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->opensGeo['country'] as $country => $count) : ?>
					<tr>
						<td width="20px">
							<?php if ($country != 'undetermined') : ?>
								<img src="/media/system/images/flags/<?php echo strtolower($country); ?>.gif" style="vertical-align:middle;">
							<?php endif; ?>
						</td>
						<td><?php echo strtoupper($country); ?></td>
						<td width="40px"><?php echo $count; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="col width-70">
		<?php
			//removed undertermined as we cant put that on the map
			//json encode so we can get value with js
			unset($this->opensGeo['country']['undetermined']);
			unset($this->opensGeo['state']['undetermined']);
			$countryGeo = strtoupper(json_encode( $this->opensGeo['country'] ));
			$stateGeo = strtoupper(json_encode( $this->opensGeo['state'] ));
		?>
		<div id="world-map-data" data-src='<?php echo $countryGeo; ?>'></div>
		<div id="us-map-data" data-src='<?php echo $stateGeo; ?>'></div>
		<div id="location-map-container">
			<div id="us-map"></div>
			<div id="world-map"></div>
			<div class="jvectormap-world">World Map</div>
		</div>
	</div>
	<br class="clear" />
	<br />
	<hr />

	<h3><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_CLICK_THROUGHS'); ?></h3>
	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_CLICK_THROUGHS_URL'); ?></th>
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_CLICK_THROUGHS_COUNT'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->clicks) > 0) : ?>
				<?php foreach ($this->clicks as $url => $count) : ?>
					<tr>
						<td>
							<?php
								echo '<a target="_blank" href="' . $url . '">' . $url . '</a>';
							?>
						</td>
						<td><?php echo number_format($count); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="2">
						<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_NO_CLICK_THROUGHS'); ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
</form>