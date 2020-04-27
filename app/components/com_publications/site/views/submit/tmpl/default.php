<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('jquery.fancybox.css', 'system')
     ->css('intro')
     ->js();

// Add projects stylesheet
\Hubzero\Document\Assets::addComponentStylesheet('com_projects');
\Hubzero\Document\Assets::addComponentScript('com_projects');
\Hubzero\Document\Assets::addPluginStylesheet('projects', 'files', 'uploader');
\Hubzero\Document\Assets::addPluginScript('projects', 'files', 'jquery.fileuploader.js');
\Hubzero\Document\Assets::addPluginScript('projects', 'files', 'jquery.queueuploader.js');

?>

<div class="submit-resource-wrap">
  <aside>
    <div class="software-instructions-wrapper">
      <div class="arrow-right"></div>
      <div class="software-instructions">
        <h4>Got a Shiny App?</h4>
        <p>Please read these <a href="https://docs.google.com/document/d/1TtRbOhlUfkD2a-Ii35py-55mlYsNhHzyexSJ9FsA70M/edit?usp=sharing" target="_blank">special instructions</a>. For other types of software, <span class="helpme"><a href="/support/">contact us</a></span> for help.</p>
    </div>

    <div class="kb-links">
      <h5>Related Articles</h5>
      <ul>
        <li>[Article] <a href="/kb/resources/postresource">Using Publications to Post Resources on QUBES</a></li>
      </ul>
    </div>
  </aside>

  <div class="submit-a-resource-wrap">
    <h3>Quick and Easy. Get started today!</h3>
    <a href="#" class="btn submit-resource-btn">Submit a Resource</a>
  </div>

  <div class="submit-partner-resource-wrap">
    <h5>Submit a Partner Resource</h5>

    <div class="resource-type-wrap">
      <a href="/qubesresources/publications/submit?action=publication&base=niblseresource" class="resource-type">
        <img src="/app/site/media/images/partners/NIBLSEGraphic.png" alt="Partner logo" class="partner-resource-logo">
      </a>

      <a href="/qubesresources/publications/submit?action=publication&base=mmhubresource" class="resource-type">
        <img src="http://192.168.33.10/app/site/media/images/partners/math_modeling_hub_logotype.png" alt="Partner logo" class="partner-resource-logo">
      </a>
    </div>
  </div>
</div>

<?php if ($this->pid && !empty($this->project) && $this->project->get('created_by_user') == User::get('id')) { ?>
	<p class="contrib-options">
		<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEED_A_PROJECT'); ?>
		<a href="<?php echo Route::url('index.php?option=com_projects&alias=' . $this->project->get('alias') . '&action=activate'); ?>">
		<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LEARN_MORE'); ?> &raquo;</a>
	</p>
<?php } ?>

<?php
	// Display status message
	$view = new \Hubzero\Component\View(array(
		'base_path' => Component::path('com_projects') . DS . 'site',
		'name'      => 'projects',
		'layout'    => '_statusmsg',
	));
	$view->error = $this->getError();
	$view->msg   = $this->msg;
	echo $view->loadTemplate();
?>

<section id="contrib-section" class="section">
	<?php echo $this->content; ?>
</section><!-- / .section -->
