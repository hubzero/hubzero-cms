<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$browser = new \Hubzero\Browser\Detector();
?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_RESOURCES_WINDOWSTOOLS'); ?>
</h3>

<?php if ($this->isAuthorised) { ?>
	<p class="admin"><a class="btn icon-edit" href="<?php echo Route::url($this->base . '&action=edit'); ?>"><?php echo Lang::txt('JACTION_EDIT'); ?></a></p>
<?php } ?>

<?php if ($browser->name() != 'chrome') { ?>
	<p class="warning">Note: Windows Tools require the use of the Chrome web browser only.</p>
<?php } ?>

<?php if ($content = $this->page->get('content')) { ?>
	<?php echo $content; ?>
<?php } else { ?>
	<ol>
		<li>Click <strong>Launch Tool</strong>
			<ol>
				<li>First time Windows Tools users will be prompted to download and install the Hubzero Application, if you are a first time user follow these extra steps:
					<ol>
						<li>In a new tab you will be taken to the <strong>Chrome Web Store</strong> and directly to the <strong>Hubzero Windows Client for Amazon AppStream</strong></li>
						<li>Click <strong>+ Add to Chrome</strong></li>
						<li>A pop-up will appear stating “Add ‘Hubzero Windows Client for Amazon AppStream’? It can: Exchange data with any device on the local network or internet” and confirm by clicking <strong>Add app</strong></li>
						<li>The app will download and plugin to your Chrome browser</li>
						<li>Navigate back to cdmhub.org and complete steps 1 and 2 again</li>
					</ol>
				</li>
			</ol>
		</li>
		<li>Review the <strong>Terms &amp; Conditions</strong> and click <strong>Accept</strong></li>
		<li>The <strong>Hubzero Windows Client for Amazon AppStream</strong> will then connect to the tool and will be ready to use
			<ol>
				<li>
					Note: If there are too many sessions running, you will be given an error stating, “No available sessions, try again in 10 minutes”<br />
					<img src="/core/plugins/resources/windowstools/assets/img/winappclient.png" alt="Windows App Client" />
				</li>
				<li>Close the <strong>Hubzero Windows Client for Amazon AppStream</strong> and then wait 10 minutes before completing steps 1 – 4 again</li>
			</ol>
		</li>
	</ol>
<?php } 