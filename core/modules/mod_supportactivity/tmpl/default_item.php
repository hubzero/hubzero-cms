<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();
?>
				<li data-parent="activity-list<?php echo $this->module->id; ?>" data-time="<?php echo $result->created; ?>" class="<?php echo $this->escape($result->category); ?>">
					<a href="<?php echo Route::url('index.php?option=com_support&controller=tickets&task=edit&id=' . $result->ticket . ($result->id ? '#c' . $result->id : '')); ?>">
						<span class="activity-event">
							<?php echo Lang::txt('MOD_SUPPORTACTIVITY_' . strtoupper($result->category), $result->ticket); ?>
						</span>
						<span class="activity-details">
							<span class="activity-time"><time datetime="<?php echo $result->created; ?>"><?php echo Date::of($result->created)->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time></span>
							<span class="activity-date"><time datetime="<?php echo $result->created; ?>"><?php echo Date::of($result->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></span>
						</span>
					</a>
				</li>