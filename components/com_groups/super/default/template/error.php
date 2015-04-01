<?php
/**
 * Error Template
 *
 * Template used for Special Groups. Will now be auto-created
 * when admin switches group from type HUB to type Special.
 *
 * @author 		Christopher Smoak
 * @copyright	December 2013
 */

// define base path
$base = str_replace(JPATH_ROOT, '', __DIR__);

// add stylesheets and scripts
JFactory::getDocument()
	->addStyleSheet( $base . DS . 'assets/css/main.css' )
	->addStyleSheet( $base . DS . 'assets/css/error.css' )
	->addScript( $base . DS . 'assets/js/main.js' );
?>
<script>
	jQuery(document).ready(function(jq) {
		HUB.Modules.ReportProblems.initialize('.report');
	});
</script>
<div class="super-group-body-wrap group-<?php echo $this->group->get('cn'); ?>">
	<div class="super-group-body error-page">
		<div class="error-message"><?php echo $this->error->get('message'); ?></div>
		<div class="error-num"><?php echo $this->error->get('code'); ?></div>

		<ul class="error-options cf">
			<li>
				<a class="back" title="Go Back" href="javascript: history.go(-1);">Back</a>
			</li>
			<li>
				<a class="group" title="Go to Group Home Page" href="/groups/<?php echo $this->group->get('cn'); ?>">Go to Group Home Page</a>
			</li>
			<li>
				<a class="report" title="Report a Problem" href="/support/ticket/new">Report a Problem</a>
			</li>
		</ul>
	</div>
</div>

<group:include type="googleanalytics" account="" />
