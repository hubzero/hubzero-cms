<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<h3><?php echo Lang::txt('PLG_RESOURCES_RELATED_HEADER'); ?></h3>

<?php if ($this->related) { ?>
	<table class="related-resources">
		<tbody>
		<?php
		foreach ($this->related as $line)
		{
			if ($line->section != 'Topic')
			{
				// Get the SEF for the resource
				if ($line->alias)
				{
					$sef = Route::url('index.php?option=' . $this->option . '&alias='. $line->alias);
				}
				else
				{
					$sef = Route::url('index.php?option=' . $this->option . '&id='. $line->id);
				}
			}
			else
			{
				if ($line->group_cn != '' && $line->scope != '')
				{
					$sef = Route::url('index.php?option=com_groups&scope=' . $line->scope . '&pagename=' . $line->alias);
				}
				else
				{
					$sef = Route::url('index.php?option=com_wiki&scope=' . $line->scope . '&pagename=' . $line->alias);
				}
			}

			// Make sure we have an SEF, otherwise it's a querystring
			$d = (strstr($sef, 'option=')) ? '&' : '?';

			// Format the ranking
			$line->ranking = round($line->ranking, 1);
			$r = (10*$line->ranking);
			if (intval($r) < 10)
			{
				$r = '0' . $r;
			}
		?>
			<tr>
				<td class="ranking"><?php echo number_format($line->ranking, 1); ?> <span class="rank-<?php echo $r; ?>"><?php echo Lang::txt('PLG_RESOURCES_RELATED_RANKING'); ?></span></td>
				<td>
				<?php if ($line->section != 'Topic') { ?>
					<?php echo Lang::txt('PLG_RESOURCES_RELATED_PART_OF'); ?>
					<a href="<?php echo $sef; ?>" class="fixedResourceTip" title="DOM:rsrce<?php echo $line->id; ?>"><?php echo stripslashes($line->title); ?></a>
					<div class="hide" id="rsrce<?php echo $line->id; ?>">
						<h4><?php echo stripslashes($line->title); ?></h4>
						<div>
							<table>
								<tbody>
									<tr>
										<th><?php echo Lang::txt('PLG_RESOURCES_RELATED_TYPE'); ?></th>
										<td><?php echo $line->section; ?></td>
									</tr>
									<tr>
										<th><?php echo Lang::txt('PLG_RESOURCES_RELATED_DATE'); ?></th>
										<td><?php echo Date::of($line->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						<?php echo \Hubzero\Utility\Str::truncate(stripslashes($line->introtext), 300); ?>
					</div>
				<?php } else { ?>
					<a href="<?php echo $sef; ?>"><?php echo stripslashes($line->title); ?></a>
				<?php } ?>
				</td>
				<td class="type"><?php echo $line->section; ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
<?php } else { ?>
	<p><?php echo Lang::txt('PLG_RESOURCES_RELATED_NO_RESULTS_FOUND'); ?></p>
<?php } 