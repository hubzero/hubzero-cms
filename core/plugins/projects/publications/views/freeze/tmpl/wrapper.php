<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div id="pub-editor" class="pane-desc freeze">
 	<div id="c-pane" class="columns">
		 <div class="c-inner">
			<h4><?php echo $this->manifest->title; ?></h4>
			<div class="block-aside">
				<div class="block-info">
					<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LOCKED'); ?>
					<?php if ($this->pub->isPublished()) {
						echo ' <a href="' . Route::url($this->pub->link('edit')) . '/?action=newversion">' . ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')) . '</a>'; } ?>
					</p>
				</div>
			</div>
			<div class="block-subject">
			<?php echo $this->content; ?>
			</div>
		 </div>
	</div>
</div>