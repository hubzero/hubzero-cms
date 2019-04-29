<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get block properties
$complete = $this->pub->curation('blocks', $this->step, 'complete');
$required = $this->pub->curation('blocks', $this->step, 'required');

$elName = "licensePick";

$defaultText = $this->license ? $this->license->text : null;
$text = $this->pub->license_text ? $this->pub->license_text : $defaultText;

?>
<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php
	echo $required ? ' el-required' : ' el-optional';
	echo $complete ? ' el-complete' : ' el-incomplete';
	?> freezeblock">
	<?php if ($this->license) {
		$info = $this->license->info;
		if ($this->license->url)
		{
			$info .= ' <a href="' . $this->license->url . '" class="popup">' . Lang::txt('Read license terms') . ' &rsaquo;</a>';
		}
		?>
		<div class="chosenitem">
			<p class="item-title">
				<?php if ($this->license) { echo '<img src="' . $this->license->icon . '" alt="' . htmlentities($this->license->title) . '" />'; } ?>
				<?php echo $this->license->title; ?>
				<span class="item-details"><?php echo $info; ?></span>
			</p>
			<?php if ($text) { ?>
				<pre><?php echo $text; ?></pre>
			<?php } ?>
		</div>
	<?php } else { ?>
		<?php echo '<p class="nocontent">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_NONE') . '</p>'; ?>
	<?php } ?>
</div>