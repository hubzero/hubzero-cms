<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if ($this->getError()) { ?>
	<p><?php echo $this->getError(); ?></p>
<?php } else { ?>
	<table class="adminform">
		<thead>
			<tr>
				<th colspan="3"><?php echo Lang::txt('COM_RESOURCES_RATINGS_TITLE'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if (count($this->rows) > 0)
		{
			foreach ($this->rows as $row)
			{
				if (intval($row->created) <> 0)
				{
					$thedate = Date::of($row->created)->toLocal();
				}
				$user = User::getInstance($row->user_id);
				?>
				<tr>
					<th><?php echo Lang::txt('COM_RESOURCES_RATING_USER'); ?>:</th>
					<td><?php echo $this->escape($user->get('name')); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_RESOURCES_RATING_VALUE'); ?>:</th>
					<td><?php
					switch ($row->rating)
					{
						case 0.5:
							$class = ' half';
							break;
						case 1:
							$class = ' one';
							break;
						case 1.5:
							$class = ' onehalf';
							break;
						case 2:
							$class = ' two';
							break;
						case 2.5:
							$class = ' twohalf';
							break;
						case 3:
							$class = ' three';
							break;
						case 3.5:
							$class = ' threehalf';
							break;
						case 4:
							$class = ' four';
							break;
						case 4.5:
							$class = ' fourhalf';
							break;
						case 5:
							$class = ' five';
							break;
						case 0:
						default:
							$class = ' none';
							break;
					}

					echo '<p class="avgrating' . $class . '"><span>Rating: ' . $row->rating . ' out of 5 stars</span></p>';
					?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_RESOURCES_RATING_CREATED'); ?>:</th>
					<td><?php echo $thedate; ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_RESOURCES_RATING_COMMENT'); ?>:</th>
					<td class="aLeft"><?php
						if ($row->comment) {
							echo $this->escape(stripslashes($row->comment));
						} else {
							echo Lang::txt('COM_RESOURCES_NONE');
						}
						?></td>
				</tr>
				<?php
			}
		}
		else
		{
			?>
			<tr>
				<td colspan="2"><?php echo Lang::txt('COM_RESOURCES_NONE'); ?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
<?php } 