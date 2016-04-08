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

Pathway::append(
	Lang::txt('COM_WIKI_SPECIAL_ALL_PAGES'),
	$this->page->link()
);

$dir = strtoupper(Request::getVar('dir', 'ASC'));
if (!in_array($dir, array('ASC', 'DESC')))
{
	$dir = 'ASC';
}

$filters = array('state' => \Components\Wiki\Models\Page::STATE_PUBLISHED);

$namespace = urldecode(Request::getVar('namespace', ''));
if ($namespace)
{
	$filters['namespace'] = $namespace;
}

$rows = $this->book->pages($filters)
	->order('title', $dir)
	->ordered()
	->rows();

$namespaces = \Components\Wiki\Models\Page::all()
	->select('namespace')
	->whereEquals('state', \Components\Wiki\Models\Page::STATE_PUBLISHED)
	->whereEquals('scope', $this->book->get('scope'))
	->whereEquals('scope_id', $this->book->get('scope_id'))
	->group('namespace')
	->order('namespace', 'asc')
	->rows();
?>
<form method="get" action="<?php echo Route::url($this->page->link()); ?>">
	<fieldset class="filters">
		<legend><?php echo Lang::txt('COM_WIKI_FILTER_LIST'); ?></legend>

		<label for="field-namespace">
			<?php echo Lang::txt('COM_WIKI_FIELD_NAMESPACE'); ?>
			<select name="namespace" id="field-namespace">
				<option value=""<?php if ($namespace == '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_WIKI_ALL'); ?></option>
				<?php foreach ($namespaces as $space) {
					if (!trim($space->get('namespace')))
					{
						continue;
					}
					?>
					<option value="<?php echo $space->get('namespace'); ?>"<?php if ($namespace == $space->get('namespace')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($space->get('namespace')); ?></option>
				<?php } ?>
			</select>
		</label>

		<input type="submit" value="<?php echo Lang::txt('COM_WIKI_GO'); ?>" />
	</fieldset>

	<div class="grid">
		<?php
		if ($rows->count())
		{
			$data = array();
			foreach ($rows as $row)
			{
				$data[] = $row;
			}
			$rows = $data;

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
								?>
								<li>
									<a href="<?php echo Route::url($row->link()); ?>">
										<?php echo $this->escape(stripslashes($row->title)); ?>
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
			if ($page == strtolower($this->page->stripNamespace()))
			{
				continue;
			}
			?>
				<li>
					<a href="<?php echo Route::url($this->page->link('base') . '&pagename=Special:' . ucfirst($page)); ?>">
						<?php echo 'Special:' . ucfirst($this->escape(stripslashes($page))); ?>
					</a>
				</li>
			<?php
		}
		?>
	</ul>
</form>
