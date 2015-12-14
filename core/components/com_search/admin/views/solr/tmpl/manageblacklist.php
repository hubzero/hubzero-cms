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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('Solr Search: Index Blacklist'));
Toolbar::preferences($this->option, '550');
$this->css('solr');
$option = $this->option;

\Submenu::addEntry(
	Lang::txt('Overview'),
	'index.php?option='.$option.'&task=configure'
);
\Submenu::addEntry(
	Lang::txt('Search Index'),
	'index.php?option='.$option.'&task=searchindex'
);
\Submenu::addEntry(
	Lang::txt('Index Blacklist'),
	'index.php?option='.$option.'&task=manageBlacklist'
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
<?php endif; ?>
