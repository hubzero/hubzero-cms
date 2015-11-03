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

// No direct access.
defined('_HZEXEC_') or die();

Pathway::append(
	Lang::txt('COM_WIKI_SPECIAL_ALL_PAGES'),
	$this->page->link()
);

$dir = strtoupper(Request::getVar('dir', 'ASC'));
if (!in_array($dir, array('ASC', 'DESC')))
{
	$dir = 'ASC';
}

$database = App::get('db');

$where = '';
$namespace = urldecode(Request::getVar('namespace', ''));
if ($namespace)
{
	$where .= "AND LOWER(wp.pagename) LIKE " . $database->quote(strtolower($namespace) . '%');
}

if ($scp = $this->page->get('scope'))
{
	$scope = "AND (wp.scope LIKE " . $database->quote($scp . '%');
	if ($this->page->get('group_cn') && (!$namespace || $namespace == 'Help:'))
	{
		$scope .= " OR LOWER(wp.pagename) LIKE " . $database->quote('Help:%');
	}
	$scope .= ") ";
}
else
{
	$scope = "AND (wp.scope='' OR wp.scope IS NULL) ";
}

$query = "SELECT COUNT(*)
			FROM `#__wiki_version` AS wv
			INNER JOIN `#__wiki_page` AS wp
				ON wp.version_id = wv.id
			WHERE wv.approved = 1
				$scope
				AND wp.state < 2
				$where";

$database->setQuery($query);
$total = $database->loadResult();

$query = "SELECT wv.pageid, (CASE WHEN (wp.`title` IS NOT NULL AND wp.`title` !='') THEN wp.`title` ELSE wp.`pagename` END) AS `title`, wp.pagename, wp.scope, wp.group_cn, wp.access, wv.version, wv.created_by, wv.created
			FROM `#__wiki_version` AS wv
			INNER JOIN `#__wiki_page` AS wp
				ON wp.version_id = wv.id
			WHERE wv.approved = 1
				$scope
				AND wp.state < 2
				$where
			ORDER BY title $dir";

$database->setQuery($query);
$rows = $database->loadObjectList();
?>
	<form method="get" action="<?php echo Route::url($this->page->link()); ?>">
		<fieldset class="filters">
			<legend><?php echo Lang::txt('COM_WIKI_FILTER_LIST'); ?></legend>

			<label for="field-namespace">
				<?php echo Lang::txt('COM_WIKI_FIELD_NAMESPACE'); ?>
				<select name="namespace" id="field-namespace">
					<option value=""<?php if ($namespace == '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_WIKI_ALL'); ?></option>
					<option value="Help:"<?php if ($namespace == 'Help:') { echo ' selected="selected"'; } ?>>Help:</option>
					<option value="Template:"<?php if ($namespace == 'Template:') { echo ' selected="selected"'; } ?>>Template:</option>
				</select>
			</label>

			<input type="submit" value="<?php echo Lang::txt('COM_WIKI_GO'); ?>" />
		</fieldset>

		<div class="grid">
<?php
if ($rows)
{
	$columns = array_chunk($rows, ceil(count($rows) / 3 ), true /* preserve keys */ );

	$index = '';
	$i = 0;
	foreach ($columns as $column)
	{
		switch ($i)
		{
			case 0: $cls = ''; break;
			case 1: $cls = ''; break;
			case 2: $cls = 'omega'; break;
		}
?>
			<div class="col span4 <?php echo $cls; ?>">
				<?php
			if (count($column) > 0)
			{
				$k = 0;
				foreach ($column as $row)
				{
					if ($this->page->get('group_cn') && !$row->scope)
					{
						$row->scope = $this->page->get('scope');
					}

					if (strtoupper(substr($row->title, 0, 1)) != $index)
					{
						$index = strtoupper(substr($row->title, 0, 1));
						if ($k != 0) {
						?>
						</ul>
						<?php } ?>
						<h3><?php echo $index; ?></h3>
						<ul>
						<?php
					}
					else if ($k == 0)
					{
						?>
						<h3><?php echo Lang::txt('COM_WIKI_INDEX_CONTINUED', $index); ?></h3>
						<ul>
						<?php
					}

					$row = new \Components\Wiki\Models\Page($row);
				?>
					<li>
						<a href="<?php echo Route::url($row->link()); ?>">
							<?php echo $this->escape(stripslashes($row->get('title'))); ?>
						</a>
					</li>
				<?php
					$k++;
				}
				?>
				</ul>
			<?php
			}
			?>
			</div>
<?php
		$i++;
	}
}
?>
		</div>

		<hr />

		<h3><?php echo Lang::txt('COM_WIKI_SPECIAL_PAGES'); ?></h3>
		<ul>
		<?php
		foreach ($this->book->special() as $key => $page)
		{
			if ($page == strtolower($this->page->denamespaced()))
			{
				continue;
			}
			?>
				<li>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&pagename=Special:' . ucfirst($page) . '&scope=' . $this->page->get('scope')); ?>">
						<?php echo 'Special:' . ucfirst($this->escape(stripslashes($page))); ?>
					</a>
				</li>
			<?php
		}
		?>
		</ul>
	</form>