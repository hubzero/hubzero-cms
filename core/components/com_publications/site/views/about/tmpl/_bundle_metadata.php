<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div class="grid bundle-data">
	<div class="grid bundle-meta">
		<div class="col span4">
			<ul class="bundle-info">
				<li><?php echo Lang::txt('COM_PUBLICATIONS_BUNDLE_CONTENT'); ?></li>
				<li><span class="bundle-size"><?php echo Hubzero\Utility\Number::formatBytes($this->bundle->getSize()); ?></span></li>
			</ul>
		</div>
		<div class="col span8 omega">
			<div class="bundle-checksum">
				<span class="bundle-checksum-value">md5:<?php echo $this->bundle->getMd5(); ?></span>
				<span class="bundle-checksum-help icon-help tooltips" title="<?php echo Lang::txt('COM_PUBLICATIONS_BUNDLE_CHECKSUM'); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_BUNDLE_CHECKSUM'); ?></span>
			</div>
		</div>
	</div>
	<div class="bundle-files">
		<ul class="filelist">
			<?php
				$this->view('_bundle_contents')
					->set('contents', $this->bundle->getContents())
					->display();
			?>
		</ul>
	</div>
</div>
