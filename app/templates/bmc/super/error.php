<?php
/**
 * Error Template
 *
 * Template used for Special Groups. Will now be auto-created
 * when admin switches group from type HUB to type Special.
 *
 * @author     HUBzero
 * @copyright  December 2015
 */

// define base path
$base = str_replace(PATH_ROOT, '', __DIR__);

// add stylesheets and scripts
Document::addStyleSheet($base . DS . 'assets/css/main.css');
Document::addStyleSheet($base . DS . 'assets/css/error.css');
Document::addScript($base . DS . 'assets/js/main.js');
?>
<script>
	jQuery(document).ready(function(jq) {
		HUB.Modules.ReportProblems.initialize('.report');
	});
</script>
<div class="super-group-body-wrap group-<?php echo $this->group->get('cn'); ?>">
	<div class="super-group-body error-page">
		<div class="error-message"><?php echo $this->error->getMessage(); ?></div>
		<div class="error-num"><?php echo $this->error->getCode(); ?></div>

		<ul class="error-options cf">
			<li>
				<a class="back" title="Go Back" href="javascript: history.go(-1);">Back</a>
			</li>
			<li>
				<a class="group" title="Go to Group Home Page" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>">Go to Group Home Page</a>
			</li>
			<li>
				<a class="report" title="Report a Problem" href="<?php echo Route::url('index.php?option=com_support&controller=tickets&task=new'); ?>">Report a Problem</a>
			</li>
		</ul>
	</div>
</div>

<group:include type="googleanalytics" account="" />
