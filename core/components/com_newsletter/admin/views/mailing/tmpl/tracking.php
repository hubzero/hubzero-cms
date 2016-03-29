<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//set title
Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_STATS'), 'stats.png');

//add buttons
Toolbar::custom('cancel', 'back', '', 'COM_NEWSLETTER_TOOLBAR_BACK', false);

// add css & js to view
$this->css();
$this->js()
     ->js('jvectormap/jquery.jvectormap.min.js', 'system')
     ->js('jvectormap/maps/jquery.jvectormap.us.js', 'system')
     ->js('jvectormap/maps/jquery.jvectormap.world.js', 'system');
?>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_STATISTICS'); ?></legend>

		<table class="adminlist">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_OPENRATE'); ?>:</th>
					<td>
						<?php
							if ($this->recipients > 0)
							{
								echo number_format(($this->opens / $this->recipients) * 100) . '% ';
								echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_OPENED', $this->opens, $this->recipients);
							}
						?>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_BOUNCERATE'); ?>:</th>
					<td>
						<?php
							if ($this->recipients > 0)
							{
								echo ($this->bounces / $this->recipients) * 100 . '% ';
							}
						?>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_FORWARDS'); ?>:</th>
					<td>
						<?php echo $this->forwards; ?>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_PRINTS'); ?>:</th>
					<td>
						<?php echo $this->prints; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>

	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_OPENS_BY_LOCATION'); ?></legend>
		<div class="col width-30">
			<table class="adminlist">
				<thead>
					<tr>
						<th colspan="2"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_TOP_LOCATIONS'); ?></th>
						<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_TOP_LOCATIONS_OPENS_COUNT'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->opensGeo['country'] as $country => $count) : ?>
						<tr>
							<td>
								<?php if ($country != 'undetermined') : ?>
									<img src="<?php echo '/core/assets/images/flags/' . strtolower($country); ?>.gif" alt="<?php echo $country; ?>" />
								<?php endif; ?>
							</td>
							<td><?php echo strtoupper($country); ?></td>
							<td><?php echo $count; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="col width-70 fltrt">
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
				<div class="jvectormap-world"><?php echo Lang::txt('COM_NEWSLETTER_WORLD_MAP'); ?></div>
			</div>
		</div>
	</fieldset>

	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_CLICK_THROUGHS'); ?></legend>

		<table class="adminlist">
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_CLICK_THROUGHS_URL'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_CLICK_THROUGHS_COUNT'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($this->clicks) > 0) : ?>
					<?php foreach ($this->clicks as $url => $count) : ?>
						<tr>
							<td><?php echo '<a target="_blank" href="' . $url . '">' . $url . '</a>'; ?></td>
							<td><?php echo number_format($count); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="2">
							<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_NO_CLICK_THROUGHS'); ?>
						</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
