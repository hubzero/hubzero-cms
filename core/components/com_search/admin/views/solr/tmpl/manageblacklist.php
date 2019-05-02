<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('Solr Search: Index Blacklist'));
Toolbar::preferences($this->option, '550');
$this->css('solr');
$option = $this->option;

Submenu::addEntry(
	Lang::txt('Overview'),
	'index.php?option='.$option.'&task=configure'
);
Submenu::addEntry(
	Lang::txt('Searchable Components'),
	'index.php?option='.$option.'&task=display&controller=searchable'
);
Submenu::addEntry(
	Lang::txt('Index Blacklist'),
	'index.php?option='.$option.'&task=manageBlacklist',
	true
);
$header = clone $this->blacklist;
$header = $header->toArray();
?>

<?php if (($this->blacklist->count() > 0)): ?>
	<table class="adminlist">
		<thead>
			<tr>
				<?php foreach (array_keys($header[0]) as $key): ?>
					<th><?php echo $key; ?></th>
				<?php endforeach; ?>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->blacklist as $entry): ?>
				<tr>
					<?php foreach (array_keys($header[0]) as $key): ?>
						<?php if ($key == 'created_by'): ?>
							<td>
								<a href="<?php echo Route::url('index.php?option=com_members&controller=members&search_field=uidNumber&search='. $entry->$key);?>">
									<?php echo User::getInstance($entry->$key)->name; ?>
								</a>
							</td>
						<?php else: ?>
							<td><?php echo $entry->$key; ?></td>
						<?php endif; ?>
					<?php endforeach; ?>
					<td>
						<a href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&task=removeBlacklistEntry&entryID='.$entry->get('id')); ?>" class="button">Remove entry</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else: ?>
	<div class="warning message"><?php echo Lang::txt('COM_SEARCH_NO_BLACKLIST_ENTRIES'); ?></div>
<?php endif; 