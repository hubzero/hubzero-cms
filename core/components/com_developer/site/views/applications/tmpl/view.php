<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('applications')
     ->css()
     ->js();

// get active var
$active = isset($this->active) ? $this->active : Request::getCmd('active');
?>

<header id="content-header">
	<h2><?php echo $this->escape($this->application->get('name')); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="btn icon-browse" href="<?php echo Route::url('index.php?option=com_developer&controller=applications'); ?>">
				<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_ALL'); ?>
			</a>
		</p>
	</div>
</header>

<?php
	echo $this->view('_menu')
			  ->set('active', $active)
			  ->set('application', $this->application)
			  ->display();
?>

<section class="main section">
	<div class="section-inner">
		<?php
		echo $this->view($active)
					->set('application', $this->application)
					->set('accesstoken', isset($this->accesstoken) ? $this->accesstoken : null)
				  ->display();

		echo $this->view('_sidebar')
				  ->set('active', $active)
				  ->display();
		?>
	</div>
</section>
