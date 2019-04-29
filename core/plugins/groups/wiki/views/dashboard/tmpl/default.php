<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->rows) { ?>
	<table class="activity" id="wiki-list">
		<tbody>
		<?php
		$cls = 'even';
		foreach ($this->rows as $row)
		{
			$name = Lang::txt('WIKI_AUTHOR_UNKNOWN');
			$user = User::getInstance($row->created_by);
			if (is_object($user) && $user->get('name'))
			{
				$name = $user->get('name');
			}

			if ($row->version > 1)
			{
				$t = Lang::txt('WIKI_EDITED');
				$c = 'wiki-edited';
			}
			else
			{
				$t = Lang::txt('WIKI_CREATED');
				$c = 'wiki-created';
			}

			$cls = ($cls == 'even') ? 'odd' : 'even';
			?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><span class="<?php echo $c; ?>"><?php echo $t; ?></span></th>
				<td><a href="<?php echo Route::url('index.php?option='.$this->option.'&pagename='.$row->pagename.'&scope='.$row->scope); ?>"><?php echo stripslashes($row->title); ?></a></td>
				<td class="author"><a href="<?php echo Route::url('index.php?option=com_members&id='.$row->created_by); ?>"><?php echo $name; ?></a></td>
				<td class="date"><?php echo Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
<?php } else { ?>
	<p><?php echo Lang::txt('PLG_GROUPS_WIKI_NO_RESULTS_FOUND'); ?></p>
<?php }