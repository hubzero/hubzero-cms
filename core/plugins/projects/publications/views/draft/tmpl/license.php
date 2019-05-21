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

// Build url
$selectUrl = Route::url( $this->pub->link('editversionid') . '&active=publications&action=select' . '&p=' . $props);

$elName = "licensePick";

// Get version params and extract agreement
$versionParams = array_filter(explode(PHP_EOL, $this->pub->version->params));
$versionParams = array_reduce($versionParams, function($carry, $item){
	$keyValueSplit = explode('=', $item);
	$key = trim(array_shift($keyValueSplit));
	$value = trim(array_shift($keyValueSplit));
	if ($key && $value)
	{
		$carry[$key] = $value;
		return $carry;
	}
});
$agreed = $this->pub->params->get('licenseagreement', 0);
$agreed = !$agreed && isset($versionParams['licenseagreement']) ? $versionParams['licenseagreement'] : $agreed;
// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $this->step, 0, 'author');

$defaultText = $this->license ? $this->license->text : null;
$text = $this->pub->get('license_text', $defaultText);

?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php
	echo $required ? ' el-required' : ' el-optional';
	echo $complete == 1 ? ' el-complete' : ' el-incomplete';
	echo ($complete == 0 && $this->license) ? ' el-partial' : '';
	echo $curatorStatus->status == 1 ? ' el-passed' : '';
	echo $curatorStatus->status == 0 ? ' el-failed' : '';
	echo $curatorStatus->updated && $curatorStatus->status != 2 ? ' el-updated' : '';
	?>">
	<div class="element_editing">
		<div class="pane-wrapper">
			<span class="checker">&nbsp;</span>
			<label id="<?php echo $elName; ?>-lbl">
				<?php if ($required && ($complete == 0 && !$this->license)) { ?>
					<span class="required"><?php echo Lang::txt('Required'); ?></span>
				<?php } ?>
				<?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_LICENSE')); ?>
				<?php if ($this->license && count($this->selections) > 1) { ?>
					<span class="edit-choice"><a href="<?php echo $selectUrl; ?>" class="showinbox"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_EDIT_LICENSE_CHOICE'); ?></a></span>
				<?php } ?>
			</label>
			<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'author', $elName); ?>
			<?php if ($this->license) {
				$info = $this->license->info;
				if ($this->license->url)
				{
					 $info .= ' <a href="' . $this->license->url . '" class="popup">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_READ_LICENSE_TERMS') . '</a>';
				}
				elseif ($this->license->text)
				{
					$info .= ' <a href="#more-lic" class="more-content">'
					. Lang::txt('PLG_PROJECTS_PUBLICATIONS_READ_LICENSE_TERMS')
					. '</a>';
					$info .= ' <div class="hidden">';
					$info .= ' 	<div class="full-content" id="more-lic"><pre>' . preg_replace("/\r\n/", "\r", $text) . '</pre></div>';
					$info .= ' </div>';
				}
				$icon = $this->license->icon;
				$icon = str_replace('/components/com_publications/assets/img/', '/core/components/com_publications/site/assets/img/', $icon);
				?>
				<div class="chosenitem">
					<p class="item-title">
						<?php if ($this->license) { echo '<img src="' . $icon . '" alt="' . htmlentities($this->license->title) . '" />'; } ?>
						<?php echo $this->license->title; ?>
						<span class="item-sub-details"><?php echo $info; ?></span>
					</p>
					<input type="hidden" name="license" id="license" value="<?php echo $this->license->id; ?>" />
					<?php if ($this->license->customizable) { ?>
						<div class="agreements">
							<label><span class="required"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REQUIRED'); ?></span>
								<?php echo $this->license->text ? Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_WRITE') : Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_WRITE_AND_CUSTOMIZE'); ?>
								<textarea name="license_text" id="license-text" cols="50" rows="10" class="pubinput"><?php echo preg_replace("/\r\n/", "\r", trim($text)); ?></textarea>
							</label>
							<p class="hidden" id="license-template"><?php echo preg_replace("/\r\n/", "\r", $this->license->text); ?></p>
							<p class="hint"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_REMOVE_DEFAULTS'); ?></p>
							<span class="mini pub-edit" id="reload"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_RELOAD_TEMPLATE_TEXT'); ?></span>
						</div>
					<?php } else {
						// Word replacements required?
						preg_match_all('/\[([^\]]*)\]/', $this->license->text, $substitutes);
						preg_match_all('/\[([^]]+)\]/', $this->pub->license_text, $matches);
						$i = 0;

						if ($this->license->text && isset($substitutes[1]) && !empty($substitutes[1])) { ?>
							<div class="replacements">
								<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_REPLACE_DEFAULTS'); ?></p>
								<?php
								$subs = array_unique($substitutes[1]);
								foreach ($subs as $sub)
								{
									$customKey = 'licensecustom' . strtolower($sub);
									$customValue = isset($versionParams[$customKey]) ? $versionParams[$customKey] : '';
									?>
									<label>
										[<?php echo $sub; ?>]<span class="required"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REQUIRED'); ?></span>
										<input name="substitute[<?php echo $sub; ?>]" type="text" value="<?php echo $customValue; ?>" class="customfield" />
									</label>
									<?php
									$i++;
								}
								?>
							</div>
						<?php } ?>
					<?php } ?>
					<?php if ($this->license->agreement == 1) {
						$txt = Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_AGREED') . ' ' . $this->license->title . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE');
						if ($this->license->url)
						{
							$txt = preg_replace("/license terms/", '<a href="' . $this->license->url . '" rel="external">license terms</a>', $txt);
						}
						$txt = preg_replace("/" . $this->license->title . "/", '<strong>' . $this->license->title . '</strong>', $txt);
						?>
						<div class="agreements">
							<label for="agreement">
								<span class="required"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REQUIRED'); ?></span>
								<input type="checkbox" name="agree" value="1" class="check-required" id="agreement" <?php echo $agreed ? 'checked="checked"' : '';  ?> /><?php echo $txt; ?>.
							</label>
						</div>
					<?php } ?>
				</div>
			<?php } else { ?>
				<div class="list-wrapper">
					<ul class="itemlist" id="license-list">
						<li class="item-new">
							<span><a href="<?php echo $selectUrl; ?>" class="item-add showinbox"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CHOOSE_LICENSE'); ?></a></span>
						</li>
					</ul>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
