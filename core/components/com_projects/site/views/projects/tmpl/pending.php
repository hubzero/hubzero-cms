<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->js();

?>
<div id="project-wrap">
	<section class="main section">
		<?php
			$this->view('_header')
			     ->set('model', $this->model)
			     ->set('showPic', 1)
			     ->set('showPrivacy', 0)
			     ->set('goBack', 0)
			     ->set('showUnderline', 1)
			     ->set('option', $this->option)
			     ->display();
		?>
		<p class="warning"><?php echo Lang::txt('COM_PROJECTS_PROJECT_PENDING_APPROVAL'); ?></p>
	</section><!-- / .main section -->
</div>