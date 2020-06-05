<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Pathway::append(
	Lang::txt('COM_WIKI_SPECIAL_ALL_PAGES'),
	$this->page->link()
);

$dir = strtoupper(Request::getString('dir', 'ASC'));
if (!in_array($dir, array('ASC', 'DESC')))
{
	$dir = 'ASC';
}

$filters = array('state' => \Components\Wiki\Models\Page::STATE_PUBLISHED);

$namespace = urldecode(Request::getString('namespace', ''));
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
					case 0:
						$cls = '';
						break;
					case 1:
						$cls = '';
						break;
					case 2:
						$cls = 'omega';
						break;
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
