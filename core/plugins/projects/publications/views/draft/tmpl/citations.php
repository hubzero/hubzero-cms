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
$props    = $this->pub->curation('blocks', $this->step, 'props');
$required = $this->pub->curation('blocks', $this->step, 'required');

$selectUrl = Route::url( $this->pub->link('editversionid') . '&active=links&action=select' . '&p=' . $props);

$elName = "citationsPick";

// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $this->step, 0, 'author');

$citationFormat = $this->pub->config('citation_format', 'apa');

?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php
	echo $required ? ' el-required' : ' el-optional';
	echo $complete == 1 ? ' el-complete' : ' el-incomplete';
	echo $curatorStatus->status == 1 ? ' el-passed' : '';
	echo $curatorStatus->status == 0 ? ' el-failed' : '';
	echo $curatorStatus->updated && $curatorStatus->status != 2 ? ' el-updated' : '';
	?>">
	<div class="element_editing">
		<div class="pane-wrapper">
			<span class="checker">&nbsp;</span>
			<label id="<?php echo $elName; ?>-lbl"> <?php if ($required) { ?><span class="required"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REQUIRED'); ?></span><?php } else { ?><span class="optional"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_OPTIONAL'); ?></span><?php } ?>
				<?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_CITATIONS')); ?>
			</label>
			<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'author', $elName); ?>
			<div class="list-wrapper">
			<?php if (count($this->pub->_citations) > 0) {
				$i= 1;

				$formatter = new \Components\Citations\Helpers\Format;
				$formatter->setTemplate($citationFormat);
				?>
					<ul class="itemlist" id="citations-list">
					<?php foreach ($this->pub->_citations as $cite) {
							$citeText = $cite->formatted
										? '<p>' . $cite->formatted . '</p>'
										: $cite->formatted();
						 ?>
						<li>
							<span class="item-options">
									<a href="<?php echo Route::url($this->pub->link('editversionid') . '&active=links&action=newcite&cid=' . $cite->cid . '&p=' . $props); ?>" class="item-edit showinbox" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_EDIT'); ?>">&nbsp;</a>
									<a href="<?php echo Route::url($this->pub->link('editversionid') . '&action=deleteitem&cid=' . $cite->cid . '&p=' . $props); ?>" class="item-remove" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</a>
							</span>
							<span class="item-title citation-formatted"><?php echo $citeText; ?></span>
						</li>
				<?php	$i++; } ?>
					</ul>
				<?php  }  ?>
					<div class="item-new">
						<span><a href="<?php echo $selectUrl; ?>" class="item-add showinbox"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_ADD_CITATION'); ?></a></span>
					</div>
				</div>
		</div>
	</div>
</div>
