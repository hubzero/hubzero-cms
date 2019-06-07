<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
// no direct access
defined('_HZEXEC_') or die();

$this->css('introduction.css', 'system')
     ->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<nav id="content-header-extra">
		<ul id="useroptions">
			<li><a class="btn icon-browse" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_BROWSE') . ' ' . Lang::txt('COM_PUBLICATIONS_PUBLICATIONS'); ?></a></li>
		</ul>
	</nav><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<?php if ($this->getError()) { ?>
	<div class="status-msg">
		<?php
		// Display error or success message
		echo '<p class="witherror">' . $this->getError() . '</p>';
		?>
	</div>
<?php } ?>

<section class="section intropage">
	<div class="grid">
		<div class="col <?php echo (!User::isGuest() && $this->contributable) ? 'span4' : 'span6';  ?>">
			<h3><?php echo Lang::txt('Recent Publications'); ?></h3>
			<?php
			if ($this->results && count($this->results) > 0)
			{
				// Display List of items
				$this->view('_list')
				     ->set('results', $this->results)
				     ->set('config', $this->config)
				     ->display();
			}
			else
			{
				echo '<p class="noresults">' . Lang::txt('COM_PUBLICATIONS_NO_RELEVANT_PUBS_FOUND') . '</p>';
			}
			?>
		</div>
		<div class="col <?php echo (!User::isGuest() && $this->contributable) ? 'span4' : 'span6 omega';  ?>">
			<h3><?php echo Lang::txt('COM_PUBLICATIONS_PUPULAR'); ?></h3>
			<?php
			if ($this->best && count($this->best) > 0)
			{
					// Display List of items
					$this->view('_list')
					     ->set('results', $this->best)
					     ->set('config', $this->config)
					     ->display();
			}
			else
			{
				echo '<p class="noresults">' . Lang::txt('COM_PUBLICATIONS_NO_RELEVANT_PUBS_FOUND') . '</p>';
			}
			?>
		</div>
		<?php  if (!User::isGuest() && $this->contributable) { ?>
			<div class="col span4 omega">
				<h3><?php echo Lang::txt('COM_PUBLICATIONS_WHO_CAN_SUBMIT'); ?></h3>
				<p><?php echo Lang::txt('COM_PUBLICATIONS_WHO_CAN_SUBMIT_ANYONE'); ?></p>
				<p><a href="<?php echo Route::url('index.php?option=com_publications&task=submit'); ?>" class="btn"><?php echo Lang::txt('COM_PUBLICATIONS_START_PUBLISHING'); ?> &raquo;</a></p>
			</div>
		<?php } ?>
	</div>
</section><!-- / .section -->
