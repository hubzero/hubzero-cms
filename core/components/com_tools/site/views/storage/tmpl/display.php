<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('storage.css')
     ->js('storage.js');

$base = rtrim(Request::base(true), '/');

$percent = (int) $this->percentage;
$percent = max(0, min(100, $percent));

// Usage level drives the accent color (ok / warn / critical)
$state = ($percent >= 90) ? 'crit' : (($percent >= 70) ? 'warn' : 'ok');

// Ring geometry: circumference of r=54 circle; the visible arc is (percent) of it
$circ   = 2 * M_PI * 54;
$offset = $circ * (1 - $percent / 100);

$fmt = function ($bytes) {
	return \Hubzero\Utility\Number::formatBytes((float) $bytes);
};

$used = isset($this->usedBytes) ? $this->usedBytes : 0;
$soft = isset($this->softBytes) ? $this->softBytes : 0;
$free = isset($this->freeBytes) ? $this->freeBytes : 0;

$filerUrl = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=filelist&tmpl=component');
$purgeUrl = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=storage');
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_TOOLS_STORAGE'); ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<div id="storage-manager"
	     data-state="<?php echo $state; ?>"
	     data-percent="<?php echo $percent; ?>"
	     data-used="<?php echo (float) $used; ?>"
	     data-soft="<?php echo (float) $soft; ?>"
	     data-free="<?php echo (float) $free; ?>"
	     data-api-base="<?php echo $this->escape($base . '/api/tools/storage'); ?>">

		<?php if ($this->exceeded) : ?>
			<p class="storage-alert warning"><?php echo Lang::txt('COM_TOOLS_ERROR_STORAGE_EXCEEDED'); ?></p>
		<?php endif; ?>

		<?php if ($this->output) : ?>
			<p class="storage-alert passed"><?php echo $this->output; ?></p>
		<?php elseif ($this->getError()) : ?>
			<p class="storage-alert error"><?php echo $this->getError(); ?></p>
		<?php endif; ?>

		<div class="storage-top">
			<!-- Usage overview -->
			<div class="storage-card storage-overview">
				<div class="storage-gauge" role="img"
				     aria-label="<?php echo Lang::txt('COM_TOOLS_STORAGE_PERCENT_USED', $percent); ?>">
					<svg viewBox="0 0 120 120" aria-hidden="true">
						<circle class="gauge-track" cx="60" cy="60" r="54" fill="none" stroke-width="12"></circle>
						<circle class="gauge-value" cx="60" cy="60" r="54" fill="none" stroke-width="12"
						        stroke-linecap="round"
						        transform="rotate(-90 60 60)"
						        stroke-dasharray="<?php echo round($circ, 2); ?>"
						        stroke-dashoffset="<?php echo round($offset, 2); ?>"></circle>
					</svg>
					<div class="gauge-center">
						<span class="gauge-percent" data-value="<?php echo $percent; ?>"><?php echo $percent; ?></span><span class="gauge-unit">%</span>
						<span class="gauge-caption"><?php echo Lang::txt('COM_TOOLS_STORAGE_USED_LABEL'); ?></span>
					</div>
				</div>

				<ul class="storage-stats">
					<li>
						<span class="stat-label"><?php echo Lang::txt('COM_TOOLS_STORAGE_USED_LABEL'); ?></span>
						<span class="stat-value" data-bytes="<?php echo (float) $used; ?>"><?php echo $fmt($used); ?></span>
					</li>
					<li>
						<span class="stat-label"><?php echo Lang::txt('COM_TOOLS_STORAGE_QUOTA_LABEL'); ?></span>
						<span class="stat-value" data-bytes="<?php echo (float) $soft; ?>"><?php echo $fmt($soft); ?></span>
					</li>
					<li>
						<span class="stat-label"><?php echo Lang::txt('COM_TOOLS_STORAGE_FREE_LABEL'); ?></span>
						<span class="stat-value" data-bytes="<?php echo (float) $free; ?>"><?php echo $fmt($free); ?></span>
					</li>
				</ul>
			</div>

			<!-- Clean up -->
			<div class="storage-card storage-cleanup">
				<h3><?php echo Lang::txt('COM_TOOLS_STORAGE_FREE_UP'); ?></h3>
				<p class="storage-muted"><?php echo Lang::txt('COM_TOOLS_STORAGE_WHAT_PURGE_DOES'); ?></p>

				<form action="<?php echo $purgeUrl; ?>" method="post" id="storage-purge-form">
					<label class="storage-field">
						<span><?php echo Lang::txt('COM_TOOLS_STORAGE_CLEAN_UP_DISCK_SPACE'); ?></span>
						<select name="degree">
							<option value="default"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_MINIMALLY'); ?></option>
							<option value="olderthan1"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_OLDER_DAY'); ?></option>
							<option value="olderthan7"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_OLDER_WEEK'); ?></option>
							<option value="olderthan30"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_OLDER_MONTH'); ?></option>
							<option value="all"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_ALL'); ?></option>
						</select>
					</label>

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="task" value="purge" />
					<?php echo Html::input('token'); ?>

					<button type="submit" class="btn btn-primary" id="storage-purge-btn">
						<?php echo Lang::txt('COM_TOOLS_STORAGE_PURGE'); ?>
					</button>
				</form>
				<p class="storage-hint"><?php echo Lang::txt('COM_TOOLS_STORAGE_AUTOMATIC_HINT'); ?></p>
			</div>
		</div>

		<!-- Files -->
		<div class="storage-card storage-files">
			<h3><?php echo Lang::txt('COM_TOOLS_STORAGE_YOUR_FILES'); ?></h3>
			<p class="storage-muted"><?php echo Lang::txt('COM_TOOLS_STORAGE_BROWSE_STORAGE'); ?></p>

			<div class="storage-browser" id="storage-files">
				<div class="sb-head">
					<nav class="sb-crumbs" id="sb-crumbs" aria-label="<?php echo Lang::txt('COM_TOOLS_STORAGE_YOUR_FILES'); ?>"></nav>
					<div class="sb-tools">
						<input type="search" class="sb-filter" id="sb-filter" autocomplete="off"
						       placeholder="<?php echo Lang::txt('COM_TOOLS_STORAGE_FILTER'); ?>"
						       aria-label="<?php echo Lang::txt('COM_TOOLS_STORAGE_FILTER'); ?>" />
						<button type="button" class="sb-tool-btn" id="sb-newfolder">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M12 11v6"/><path d="M9 14h6"/></svg>
							<span><?php echo Lang::txt('COM_TOOLS_STORAGE_NEW_FOLDER'); ?></span>
						</button>
						<button type="button" class="sb-tool-btn" id="sb-upload-btn">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M17 8l-5-5-5 5"/><path d="M12 3v12"/></svg>
							<span><?php echo Lang::txt('COM_TOOLS_STORAGE_UPLOAD'); ?></span>
						</button>
						<input type="file" id="sb-file-input" multiple hidden />
						<button type="button" class="sb-refresh" id="sb-refresh" title="<?php echo Lang::txt('COM_TOOLS_STORAGE_REFRESH'); ?>" aria-label="<?php echo Lang::txt('COM_TOOLS_STORAGE_REFRESH'); ?>">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
						</button>
					</div>
				</div>

				<div class="sb-progress" id="sb-progress" hidden>
					<div class="sb-progress-track"><div class="sb-progress-bar" id="sb-progress-bar"></div></div>
					<span class="sb-progress-text" id="sb-progress-text" aria-live="polite"></span>
				</div>

				<div class="sb-drop" id="sb-drop" aria-hidden="true">
					<span><?php echo Lang::txt('COM_TOOLS_STORAGE_DROP_HINT'); ?></span>
				</div>

				<div class="sb-bulk" id="sb-bulk" hidden>
					<label class="sb-selall">
						<input type="checkbox" id="sb-selall" />
						<span><?php echo Lang::txt('COM_TOOLS_STORAGE_SELECT_ALL'); ?></span>
					</label>
					<span class="sb-count" id="sb-count" aria-live="polite"></span>
					<button type="button" class="btn btn-danger sb-bulk-del" id="sb-bulk-del" disabled>
						<?php echo Lang::txt('COM_TOOLS_STORAGE_DELETE_SELECTED'); ?>
					</button>
				</div>

				<ul class="sb-list" id="sb-list" aria-label="<?php echo Lang::txt('COM_TOOLS_STORAGE_YOUR_FILES'); ?>"></ul>
				<p class="sb-state" id="sb-loading"><?php echo Lang::txt('COM_TOOLS_STORAGE_BROWSER_LOADING'); ?></p>
				<p class="sb-state" id="sb-empty" hidden><?php echo Lang::txt('COM_TOOLS_STORAGE_BROWSER_EMPTY'); ?></p>
				<p class="sb-state" id="sb-nomatch" hidden><?php echo Lang::txt('COM_TOOLS_STORAGE_BROWSER_NOMATCH'); ?></p>
				<p class="sb-state sb-error" id="sb-error" hidden><?php echo Lang::txt('COM_TOOLS_STORAGE_BROWSER_ERROR'); ?></p>

				<noscript>
					<iframe src="<?php echo $filerUrl; ?>" name="filer" id="filer" title="<?php echo Lang::txt('COM_TOOLS_STORAGE_YOUR_FILES'); ?>"></iframe>
				</noscript>
			</div>

			<script type="application/json" id="storage-strings"><?php echo json_encode(array(
				'home'          => Lang::txt('COM_TOOLS_STORAGE_HOME_CRUMB'),
				'delete'        => Lang::txt('COM_TOOLS_STORAGE_DELETE'),
				'empty'         => Lang::txt('COM_TOOLS_STORAGE_BROWSER_EMPTY'),
				'error'         => Lang::txt('COM_TOOLS_STORAGE_BROWSER_ERROR'),
				'selected'      => Lang::txt('COM_TOOLS_STORAGE_SELECTED'),
				'confirmFile'   => Lang::txt('COM_TOOLS_STORAGE_CONFIRM_FILE'),
				'confirmFolder' => Lang::txt('COM_TOOLS_STORAGE_CONFIRM_FOLDER'),
				'confirmBulk'   => Lang::txt('COM_TOOLS_STORAGE_CONFIRM_BULK'),
				'rename'        => Lang::txt('COM_TOOLS_STORAGE_RENAME'),
				'newFolder'     => Lang::txt('COM_TOOLS_STORAGE_NEW_FOLDER_PROMPT'),
				'renamePrompt'  => Lang::txt('COM_TOOLS_STORAGE_RENAME_PROMPT'),
				'uploading'     => Lang::txt('COM_TOOLS_STORAGE_UPLOADING'),
				'quota'         => Lang::txt('COM_TOOLS_STORAGE_QUOTA_EXCEEDED'),
				'uploadFailed'  => Lang::txt('COM_TOOLS_STORAGE_UPLOAD_FAILED'),
				'parent'        => Lang::txt('COM_TOOLS_STORAGE_PARENT')
			)); ?></script>
		</div>
	</div><!-- / #storage-manager -->
</section><!-- / .main section -->
