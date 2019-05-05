<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$now  = Date::toSql();
$week = Date::add('1 week')->toSql();

$database = App::get('db');

$query = "SELECT sd.*
		FROM `#__courses_offering_section_dates` AS sd
		WHERE sd.section_id=" . $this->offering->section()->get('id') . "
		AND (sd.publish_up >= " . $database->Quote($now) . " AND sd.publish_up <= " . $database->Quote($week) . ")
		AND sd.scope!='asset'
		ORDER BY sd.publish_up LIMIT 20";

$database->setQuery($query);
$rows = $database->loadObjectList();

$base = $this->offering->link();
?>

	<h3 class="heading">
		<a name="dashboard"></a>
		<?php echo Lang::txt('PLG_COURSES_DASHBOARD'); ?>
	</h3>

	<div class="sub-section">
		<div class="sub-section-overview">
			<h3>
				<?php echo Lang::txt('PLG_COURSES_DASHBOARD_OVERVIEW'); ?>
			</h3>
			<p><?php echo Lang::txt('PLG_COURSES_DASHBOARD_OVERVIEW_ABOUT'); ?></p>
		</div>
		<div class="sub-section-content">
			<div class="grid">
			<div class="col span3">
				<table class="breakdown">
					<tbody>
						<tr>
							<td>
								<span>
									<?php echo Lang::txt('PLG_COURSES_DASHBOARD_ENROLLED', '<strong>' . $this->offering->members(array('count' => true, 'student'=>1)) . '</strong>'); ?>
								</span>
							</td>
						</tr>
						<tr>
							<td class="gradebook-passing">
								<span>
									<?php echo Lang::txt('PLG_COURSES_DASHBOARD_PASSING', '<strong>' . $this->offering->gradebook()->countPassing() . '</strong>'); ?>
								</span>
							</td>
						</tr>
						<tr>
							<td class="gradebook-failing">
								<span>
									<?php echo Lang::txt('PLG_COURSES_DASHBOARD_FAILING', '<strong>' . $this->offering->gradebook()->countFailing() . '</strong>'); ?>
								</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col span9 omega">
				<div class="dashboard-timeline-start">
					<p><?php echo Date::of($now)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></p>
				</div>
				<?php if ($rows) { ?>
					<ul class="dashboard-timeline">
					<?php
					foreach ($rows as $i => $row)
					{
						switch ($row->scope)
						{
							case 'unit':
								$obj = \Components\Courses\Models\Unit::getInstance($row->scope_id);
								$url = $base . '&active=outline';
							break;
							case 'asset_group':
								$obj = new \Components\Courses\Models\AssetGroup($row->scope_id);
								$unit = \Components\Courses\Models\Unit::getInstance($obj->get('unit_id'));
								$url = $base . '&active=outline&unit=' . $unit->get('alias') . '&b=' . $obj->get('alias');
							break;
							case 'asset':
								$obj = new \Components\Courses\Models\Asset($row->scope_id);
								$url = $base . '&active=outline&unit=&b=&c=';
							break;
						}
						if (!$obj->exists() || !$obj->isPublished() || ($row->scope == 'asset_group' && !$obj->get('parent')))
						{
							// skip containers
							continue;
						}
						?>
						<li>
							<a href="<?php echo Route::url($url); ?>">
								<?php echo $this->escape(stripslashes($obj->get('title'))); ?>
							</a>
							<span class="details">
								<time datetime="<?php echo $row->publish_up; ?>"><?php echo Date::of($row->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
							</span>
						</li>
						<?php
						if ($i > 0 && $row->scope == 'unit')
						{
							break;
						}
					}
					?>
					</ul>
				<?php } else { ?>
					<ul class="dashboard-timeline">
						<li class="noresults"><?php echo Lang::txt('PLG_COURSES_DASHBOARD_NOTHING_HAPPENING'); ?></li>
					</ul>
				<?php } ?>
				<div class="dashboard-timeline-start">
					<p><?php echo Date::of($week)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></p>
				</div>
			</div>
			</div><!-- / .grid -->
		</div>
		<div class="clear"></div>
	</div>

<?php
	$after = Event::trigger('courses.onCourseDashboard', array($this->course, $this->offering));
	echo implode("\n", $after);
