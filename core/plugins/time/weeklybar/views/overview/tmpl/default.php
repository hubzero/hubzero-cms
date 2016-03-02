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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Components\Time\Models\Proxy;

// No direct access
defined('_HZEXEC_') or die();

\Hubzero\Document\Assets::addSystemScript('jquery.flot.min', 'flot');
\Hubzero\Document\Assets::addSystemScript('jquery.flot.stack.min', 'flot');
\Hubzero\Document\Assets::addSystemScript('moment.min');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui');

$this->css()
     ->js();

HTML::behavior('core');

// Get the day of the week
$today       = Date::of(Request::getVar('week', time()));
$dateofToday = $today->format('Y-m-d');
$dayOfWeek   = $today->format('N') - 1;
$mon         = Date::of(strtotime("{$dateofToday} - {$dayOfWeek}days"))->toLocal('n/j');
$sun         = Date::of(strtotime("{$dateofToday} + " . (6-$dayOfWeek) . 'days'))->toLocal('n/j');

// Build a list of people that should show up on the bar chart
// We do this because the list returned from the records query
// would not otherwise include folks that didn't enter any records
$people = [['name' => User::get('name'), 'user_id' => User::get('id')]];

foreach (Proxy::whereEquals('proxy_id', User::get('id')) as $proxy)
{
	$people[] = ['name' => $proxy->user->name, 'user_id' => $proxy->user_id];
}

// Reverse sort them because the JS is going to reverse them again in processing
usort($people, function ($a, $b)
{
	return strcmp($b['name'], $a['name']);
});

?>

<script>
	var persons = [
		<?php foreach ($people as $person) : ?>
			{name : "<?php echo $person['name']; ?>", id : "<?php echo $person['user_id'] ?>"},
		<?php endforeach; ?>
	];
</script>

<div class="plg_time_weeklybar">
	<div class="datepicker">
		<div class="backweek"></div>
		<div class="daterange" data-week="<?php echo $dateofToday; ?>">
			<?php echo $mon ?> - <?php echo $sun ?>
		</div>
		<div class="forwardweek"></div>
	</div>
	<div class="chart"></div>
	<div class="legend">
		<div class="legent-title"><?php echo Lang::txt('PLG_TIME_WEEKLYBAR_LEGEND'); ?></div>
		<div class="legend-content"></div>
	</div>
</div>