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

$elName = "citationsPick";
$citationFormat = $this->pub->config('citation_format', 'apa');
?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
echo $complete ? ' el-complete' : ' el-incomplete'; ?> freezeblock">
<?php if (count($this->pub->_citations) > 0) {
	$i= 1;
	$formatter = new \Components\Citations\Helpers\Format;
	$formatter->setTemplate($citationFormat);
	?>
	<div class="list-wrapper">
		<ul class="itemlist" id="citations-list">
		<?php foreach ($this->pub->_citations as $cite) {

				$citeText = $cite->formatted
							? '<p>' . $cite->formatted . '</p>'
							: \Components\Citations\Helpers\Format::formatReference($cite, '');
			 ?>
			<li>
				<span class="item-title citation-formatted"><?php echo $citeText; ?></span>
			</li>
	<?php	$i++; } ?>
		</ul>
	</div>
	<?php  } else {
		echo '<p class="nocontent">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_NONE') . '</p>';
	} ?>
</div>
