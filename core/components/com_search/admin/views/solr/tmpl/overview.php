<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('Solr Search: Overview'));
Toolbar::custom('optimize', 'save', 'optimize', 'COM_SEARCH_SOLR_OPTIMIZE', false);
Toolbar::spacer();
Toolbar::preferences($this->option, '550');
$this->css('solr');
$option = $this->option;

$this->view('_submenu', 'shared')
	->display();
?>
<div id="widget-container" class="com_search">
	<div class="widget">
		<div class="inner">
			<div class="title"><div>Solr Status</div></div>
			<div class="sub-title"><div class="sub-title-inner">Last Document Insert: <?php echo $this->lastInsert; ?></div></div>
			<div class="sub-title"><div class="sub-title-inner">Mechanism: <?php echo ucfirst($this->mechanism); ?></div> </div>
			<div class="content">
				<div class="content-inner">
					<div class="status">
						<?php if ($this->status === true) : ?>
							<div class="status-message">
								<div class="good"></div>
								<p>The search engine is responding.</p>
								<p class="emph">Last insert was <?php echo $this->lastInsert; ?></p>
							</div> <!-- /.status-message -->
						<?php else : ?>
							<div class="alert"></div>
							<div class="status-message">
								<p><?php echo Lang::txt('COM_SEARCH_NOT_RESPONDING'); ?></p>
								<p><?php echo Lang::txt('COM_SEARCH_CHECK_CONFIG'); ?></p>
							</div> <!-- /.status-message -->
						<?php endif; ?>
					</div> <!-- /.status -->
				</div><!-- /.content-inner -->
			</div><!-- /.content -->
		</div><!-- /.inner -->
	</div>
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
		<input type="hidden" name="option" value="com_search" />
		<input type="hidden" name="controller" value="solr" />
		<input type="hidden" name="task" value="searchIndex" autocomplete="" />
		<?php echo Html::input('token'); ?>
	</form>

	<!-- @TODO: Make view -->
	<?php if (isset($this->queueStats) && false): ?>
		<div class="widget">
			<div class="inner">
				<div class="title">
					<div><?php echo Lang::txt('COM_SEARCH_QUEUE_STATUS'); ?></div>
				</div>
				<div class="sub-title">
					<div class="sub-title-inner">
						<?php echo Lang::txt('COM_SEARCH_QUEUE_LAST_SERVICE') . ':' . Date::of($this->queueStats['modified'])->relative(); ?>
					</div>
				</div>
				<div class="sub-title">
					<div class="sub-title-inner">
						<?php echo Lang::txt('COM_SEARCH_QUEUE_AVERAGE_SERVICE') . ':' . $this->queueStats['serviceTime'] . 'minutes'; ?>
					</div>
				</div>
				<div class="content">
					<div class="content-inner">
						<div class="status"></div> <!-- /.status -->
					</div><!-- /.content-inner -->
				</div><!-- /.content -->
			</div><!-- /.inner -->
		</div>
	<?php endif; ?>
</div>
