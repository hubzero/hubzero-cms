<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2012-2013 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2012-2013 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License,
 * version 3 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// no direct access
defined('_HZEXEC_') or die();

$this->css();

?>
<div id="plg-header">
	<h3 class="databases"><?php echo Lang::txt('PLG_PROJECTS_DATABASES'); ?></h3>
</div>

<?php if ($this->model->access('content')) { ?>
<ul id="page_options" class="pluginOptions">
	<li>
		<a class="icon-add add btn"  href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=databases&action=create') . '#content'; ?>">
			<?php echo Lang::txt('PLG_PROJECTS_DATA_START'); ?>
		</a>
	</li>
</ul>
<?php } ?>
<div id="confirm-file-delete" class="confirm-file-delete" title="<?php echo Lang::txt('PLG_PROJECTS_DATABASES_DELETE_PROJECT_DATABASE'); ?>">
<p>
	<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0; color: red;"></span>
	<?php echo Lang::txt('Confirm database deletion.'); ?>
</p>
</div>

<div id="prj-db-list">
	<?php if (count($this->list) > 0 ) { ?>
	<table class="listing">
		<thead>
			<tr>
				<th style="width: 40%;"><?php echo Lang::txt('PLG_PROJECTS_DATABASES_TITLE'); ?></th>
				<th><?php echo Lang::txt('Source File'); ?></th>
				<th><?php echo Lang::txt('Created On'); ?></th>
				<th><?php echo Lang::txt('Created By'); ?></th>
				<?php if ($this->model->access('content')) { ?>
				<th><?php echo Lang::txt('Update Database'); ?><span class="update-help-icon" title="<?php echo Lang::txt('Click the \'Update\' link to recreate a database using the updated source CSV file from the file repository. Click the help icon for more information.'); ?>"></span></th>
				<th><?php echo Lang::txt('PLG_PROJECTS_DATABASES_DELETE'); ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->list as $r)
			{
				if ($r['source_dir'] != '')
				{
					$full_path = htmlspecialchars($r['source_dir'] . DS . $r['source_file']);
				}
				else
				{
					$full_path = htmlspecialchars($r['source_file']);
				}

				$file_url = '/projects/' . $this->model->get('alias')
					. '/files/?action=download&amp;case=files&amp;subdir='
					. trim($r['source_dir'], '/') . '&amp;file=' . $r['source_file'];

				$file_name = '<a href="' . $file_url . '">' . $r['source_file'] . '</a>';

				$recreate = '<a href="/projects/' . $this->model->get('alias')
					. '/databases/create/?db_id=' . $r['id']
					. '" class="re-create-db">' . Lang::txt('Update Database') . '</a>';

				$file_extra = 'title="' . $full_path . '"';

				if ($r['source_revision'] != $r['source_revision_curr'])
				{
					if ($r['source_available'])
					{
						$file_extra = 'class="file-updated" title="The file has changed, modified ' . $r['source_revision_date'] . '"';
					}
					else
					{
						$file_extra = 'class="file-deleted" title="The file [' . $full_path . '] has been removed or renamed ' . $r['source_revision_date'] . '"';
						$file_name = '<span style="color: #ddd; cursor: not-allowed;">' . $r['source_file'] . '</span>';
						$recreate = '<span title="The original file has been removed or renamed, please restore the file to enable this functionality" style="color: #ddd; cursor: not-allowed;">Recreate<span>';
					}
				}
		?>
			<tr class="mini faded">
				<td title="<?php echo htmlspecialchars($r['description']); ?>" data-db-title="<?php echo htmlspecialchars($r['title']); ?>"  data-db-id="<?php echo $r['id']; ?>">
					<a target="_blank" href="/<?php echo $this->dataviewer; ?>/view/<?php echo $this->model->get('alias'); ?>:dsl/<?php echo $r['database_name']; ?>/"><?php echo $r['title']; ?></a>
					<?php if ($this->model->access('content')) { ?>
					<span class="db-update" title="<?php echo Lang::txt('PLG_PROJECTS_DATABASES_CLICK_TO_EDIT'); ?>"></span>
					<?php } ?>
				</td>
				<td <?php  echo $file_extra; ?>>
					<?php  echo $file_name; ?>
				</td>
				<td>
					<?php echo $r['created']; ?>
				</td>
				<td>
					<a target="_blank" href="/members/<?php echo $r['created_by']; ?>"><?php echo $r['name']; ?></a>
				</td>
				<?php if ($this->model->access('content')) { ?>
				<td>
					<?php echo $recreate; ?>
				</td>
				<td>
					<a href="/projects/<?php echo $this->model->get('alias'); ?>/databases/delete/?db_id=<?php echo $r['id']; ?>" class="delete-db"><?php echo Lang::txt('PLG_PROJECTS_DATABASES_DELETE'); ?></a>
				</td>
				<?php } ?>
			</tr>
		<?php
		}
		?>
		</tbody>
	</table>

	<div id="update-db-help-dialog" title="How to update a database" style="display: none;">
		<strong>If you want to just update the way the data is displayed and do not want to change the data, skip to <span style="color: blue;">Step 4</span>.</strong>
		<h3 style="margin: 1.2em 0 .4em 0;">Step 1: Download the CSV file used for creating the database.</h3>
		<ul>
			<li>You can either use the "Source File" link or you can download the file from the Files section of the project</li>
			<li>Your spreadsheet will now have the DataStore information included in the first 3 rows</li>
		</ul>
		<h3 style="margin: 1.2em 0 .4em 0;">Step 2: Update the data in your spreadsheet.</h3>
		<ul>
			<li>You can add rows, delete rows and change data in rows</li>
			<li>Only change rows that are <strong>BELOW</strong> the <strong>DATASTART</strong> row</li>
			<li>Make sure your data matches the data types you defined for your columns, <br />the data types are listed in the 2<sup>nd</sup> row for your convenience</li>
			<li>Save your file in the same CSV format</li>
		</ul>
		<h3 style="margin: 1.2em 0 .4em 0;">Step 3: Upload the updated CSV file to the project's File area .</h3>
		<ul>
			<li>Make sure to upload to the same folder. <br />Your uploaded file has to replace the CSV file that is there now</li>
		</ul>
		<h3 style="margin: 1.2em 0 .4em 0;">Step 4: Return to the Database area and click on "Update Database"</h3>
		<ul>
			<li>When you click "Update Database" the data from the updated spreadsheet will be loaded</li>
			<li>At this point you can customize the columns and change any of the column properties</li>
		</ul>
	</div>

	<?php
	}
	else
	{
		$out = '<p class="noresults">' . Lang::txt('PLG_PROJECTS_DATA_NO_DATA_FOUND');
		if ($this->model->access('content'))
		{
			$out .=' <span class="addnew"><a href="' . Route::url('index.php?option=' . $this->option . '&active=databases&alias=' . $this->model->get('alias') . '&action=create#content') . '" >' . Lang::txt('PLG_PROJECTS_DATA_START') . '</a></span>';
		}
		$out.= '</p>';
		echo $out;
	}
	?>
</div>
<div id="prj-db-update-dialog" class="prj-db-update-dialog dialog" title="Update Title &amp; Description">
	<form id="prj-db-update-form" method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=databases&action=update'); ?>">
		<input type="hidden" name="db_id" />
		<label><?php echo Lang::txt('PLG_PROJECTS_DATABASES_TITLE'); ?>:
			<input type="text" name="db_title" style="width: 550px;" />
		</label>
		<label><?php echo Lang::txt('PLG_PROJECTS_DATABASES_DESC'); ?>:
			<textarea name="db_description" style="width: 550px; height: 130px;"></textarea>
		</label>
	</form>
</div>
