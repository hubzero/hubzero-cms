<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get creator name
$creator = $this->pub->creator('name') . ' (' . $this->pub->creator('username') . ')';

// Version status
$status = $this->pub->getStatusName();
$class  = $this->pub->getStatusCss();

// Get block content
$blockcontent = $this->pub->_curationModel->parseBlock('edit');

?>
<?php 
// Write title
echo \Components\Publications\Helpers\Html::showPubTitle( $this->pub, $this->title);

// Draw status bar
echo $this->pub->_curationModel->drawStatusBar();
?>
<div id="pub-body">
	<?php echo $blockcontent; ?>
</div>
<p class="rightfloat">
	<a href="<?php echo Route::url($this->pub->link('version')); ?>" class="public-page" rel="external" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_PUB_PAGE'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_PUB_PAGE'); ?></a>
</p>
<script>
jQuery(document).ready(function($) {
	HUB.ProjectPublicationsDraft.initialize();
});
</script>
