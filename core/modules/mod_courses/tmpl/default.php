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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();

Html::behavior('chart');
?>
<div class="mod_courses">
	<div class="overview-container">
		<div id="container<?php echo $this->module->id; ?>" class="chart" style="min-width: 400px; height: 200px;"></div>
	<?php
		$top = 0;

		$totals = '';
		if ($this->totals)
		{
			$c = array();
			foreach ($this->totals as $year => $data)
			{
				foreach ($data as $k => $v)
				{
					$c[] = '[new Date(' . $year . ',  ' . ($k - 1) . ', 1),' . $v . ']';
				}
			}
			$totals = implode(',', $c);
		}
	?>

	<script type="text/javascript">
		if (!jq) {
			var jq = $;
		}
		if (jQuery()) {
			var $ = jq,
				chart<?php echo $this->module->id; ?>,
				month_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				datasets<?php echo $this->module->id; ?> = [
					{
						color: "#656565", //#CFCFAB
						label: "<?php echo Lang::txt('MOD_COURSES_ENROLLED'); ?>",
						data: [<?php echo $totals; ?>]
					}
				];

			$(document).ready(function() {
				var chart<?php echo $this->module->id; ?> = $.plot($('#container<?php echo $this->module->id; ?>'), datasets<?php echo $this->module->id; ?>, {
					series: {
						bars: {
							show: true,
							fill: true
						}
					},
					grid: {
						color: 'rgba(0, 0, 0, 0.6)',
						borderWidth: 1,
						borderColor: 'transparent',
						hoverable: true,
						clickable: true
					},
					tooltip: true,
						tooltipOpts: {
						content: "%y %s in %x",
						shifts: {
							x: -60,
							y: 25
						},
						defaultTheme: false
					},
					legend: {
						show: true,
						noColumns: 2,
						position: "ne",
						backgroundColor: 'transparent',
						margin: [0, -50]
					},
					xaxis: { mode: "time", tickLength: 0, tickDecimals: 0, <?php if (count($c) <= 12) { echo 'ticks: ' . count($c) . ','; } ?>
						tickFormatter: function (val, axis) {
							var d = new Date(val);
							return month_short[d.getUTCMonth()];//d.getUTCDate() + "/" + (d.getUTCMonth() + 1);
						}
					},
					yaxis: { min: 0 }
				});
			});
		}
	</script>
	</div>
	<div class="overview-container">
		<table class="courses-stats-overview">
			<tbody>
				<tr>
					<td class="published-items">
						<a href="<?php echo Route::url('index.php?option=com_courses&state=1'); ?>" title="<?php echo Lang::txt('MOD_COURSES_PUBLISHED_TITLE'); ?>">
							<?php echo $this->escape($this->published); ?>
							<span><?php echo Lang::txt('MOD_COURSES_PUBLISHED'); ?></span>
						</a>
					</td>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_courses&state=3'); ?>" title="<?php echo Lang::txt('MOD_COURSES_DRAFT_TITLE'); ?>">
							<?php echo $this->escape($this->draft); ?>
							<span><?php echo Lang::txt('MOD_COURSES_DRAFT'); ?></span>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="courses-stats-overview">
			<tbody>
				<tr>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_courses&state=0'); ?>" title="<?php echo Lang::txt('MOD_COURSES_UNPUBLISHED_TITLE'); ?>">
							<?php echo $this->escape($this->unpublished); ?>
							<span><?php echo Lang::txt('MOD_COURSES_UNPUBLISHED'); ?></span>
						</a>
					</td>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_courses&state=2'); ?>" title="<?php echo Lang::txt('MOD_COURSES_ARCHIVED_TITLE'); ?>">
							<?php echo $this->escape($this->archived); ?>
							<span><?php echo Lang::txt('MOD_COURSES_ARCHIVED'); ?></span>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>