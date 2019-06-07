<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// set title
Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_IMPORT_TITLE_RUN'), 'import');

// add import styles and scripts
$this->js('import')
     ->js('handlebars', 'system')
     ->css('import');
?>

<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($this->controller == 'imports') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=imports'); ?>"><?php echo Lang::txt('COM_MEMBERS_IMPORT_TITLE_IMPORTS'); ?></a>
		</li>
		<li>
			<a<?php if ($this->controller == 'importhooks') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=importhooks'); ?>"><?php echo Lang::txt('COM_MEMBERS_IMPORT_HOOKS'); ?></a>
		</li>
	</ul>
</nav>

<?php foreach ($this->getErrors() as $error) : ?>
	<p class="error"><?php echo $error; ?></p>
<?php endforeach; ?>

<form action="<?php echo Route::url('index.php?option=com_members&controller=import&task=dorun'); ?>" method="post" name="adminForm" id="adminForm">

	<fieldset class="adminform import-results">

		<?php if ($this->dryRun) : ?>
			<div class="dryrun-message">
				<strong><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_NOTICE'); ?></strong>
				<p><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_NOTICE_DESC'); ?></p>
			</div>
		<?php endif; ?>

		<div class="countdown" data-timeout="5">
			<?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_START', '<span>5</span>'); ?>
		</div>
		<div class="countdown-actions" data-progress="<?php echo Route::url('index.php?option=com_members&controller=import&task=progress&id=' . $this->import->get('id')); ?>">
			<button type="button" class="start"><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_BUTTON_START'); ?></button>
			<button type="button" class="stop"><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_BUTTON_STOP'); ?></button>

			<button type="button" class="start-over"><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_BUTTON_RERUN'); ?></button>
			<?php if ($this->dryRun) : ?>
				<button type="button" class="start-real"><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_BUTTON_REAL'); ?></button>
			<?php endif; ?>
		</div>

		<hr />

		<strong><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_PROGRESS'); ?><span class="progress-percentage">0%</span></strong>
		<div class="progress"></div>

		<hr />

		<strong><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULTS'); ?><span class="results-stats"></span></strong>
		<div class="results">
			<span class="hint"><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULTS_WAITING'); ?></span>
		</div>
		<script id="entry-template" type="text/x-handlebars-template">
			<h3 class="resource-title">
				{{#if record.errors}}<span class="has-errors"><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_CONTAINSERRORS'); ?></span>{{/if}}
				{{#if record.notices}}<span class="has-notices"><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_CONTAINSNOTICES'); ?></span>{{/if}}
				{{{ record.entry.name }}}
			</h3>

			<div class="resource-data">
				<div class="grid">
					{{#if record.errors}}
						<div class="errors">
							<strong><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_ERRORMESSAGE'); ?></strong>
							<ol>
								{{#each record.errors}}
									<li>{{this}}</li>
								{{/each}}
							</ol>
						</div>
					{{/if}}

					{{#if record.notices}}
						<div class="notices">
							<strong><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_NOTICEMESSAGE'); ?></strong>
							<ol>
								{{#each record.notices}}
									<li>{{{this}}}</li>
								{{/each}}
							</ol>
						</div>
					{{/if}}

					<div class="col span7">
						{{{entry_data record}}}
					</div>
					<div class="col span5">

						<h4><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_DISABILITY'); ?></h4>
						<ul>
							{{#each record.entry.disability}}
								<li>{{{ this }}}</li>
							{{else}}
								<li><span class="hint"><?php echo Lang::txt('COM_MEMBERS_NONE'); ?></span></li>
							{{/each}}
						</ul>

						<hr />

						<h4><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_RACE'); ?></h4>
						<ul>
							{{#each record.entry.race}}
								<li>{{{ this }}}</li>
							{{else}}
								<li><span class="hint"><?php echo Lang::txt('COM_MEMBERS_NONE'); ?></span></li>
							{{/each}}
						</ul>

						<hr />

						<h4><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_TAGS'); ?></h4>
						<ul>
							{{#each record.tags}}
								<li>{{{ this }}}</li>
							{{else}}
								<li><span class="hint"><?php echo Lang::txt('COM_MEMBERS_NONE'); ?></span></li>
							{{/each}}
						</ul>

						<hr />

						<h4><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_GROUPS'); ?></h4>
						<ul>
							{{#each record.groups}}
								<li>{{{ this }}}</li>
							{{else}}
								<li><span class="hint"><?php echo Lang::txt('COM_MEMBERS_NONE'); ?></span></li>
							{{/each}}
						</ul>

					</div>
					<br class="clr" />
					<hr />

					<div class="unused-data">
						<h4><?php echo Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_UNUSED'); ?></h4>
						<pre>{{print_json_data raw._unused}}</pre>
					</div>
				</div>
			</div>
		</script>

	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="dorun" />
	<input type="hidden" name="id" value="<?php echo $this->import->get('id'); ?>" />
	<input type="hidden" name="dryrun" value="<?php echo $this->dryRun; ?>" />

	<?php echo Html::input('token'); ?>
</form>