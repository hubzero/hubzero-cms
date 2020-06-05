<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get block properties
$complete = $this->pub->curation('blocks', $this->step, 'complete');
$props    = $this->pub->curation('blocks', $this->step, 'props');
$required = $this->pub->curation('blocks', $this->step, 'required');

$elName = "tagsPick";

?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
echo $complete ? ' el-complete' : ' el-incomplete'; ?> freezeblock">
<?php  // Show tags
	if ($this->pub->getTagsForEditing()) {
			$this->pub->getTagCloud( 1 );
			echo $this->pub->_tagCloud;
	}
	else {
		echo '<p class="nocontent">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_NONE') . '</p>';
	} ?>
</div>