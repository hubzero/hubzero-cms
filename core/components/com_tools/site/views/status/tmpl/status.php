<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('status.css')
     ->js('status.js');

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status'); ?>" >
			<div class="options row no-gutters">
			
				<div class="option col-lg-3 col-md-6">
					<div class="inner">
						<header><h3>Last Session</h3></header>
						<div class="content">
							<div >Session Number: <?php echo $this->lastsession->sessnum; ?></div>
							<div >Username: <?php echo $this->lastsession->username; ?></div>
							<div >Started: <?php echo $this->lastsession->start; ?></div>
							<div >Last Accessed: <?php echo $this->lastsession->accesstime; ?></div>
							<div >Tool Name: <?php echo $this->lastsession->sessname; ?></div>
							<div >Tool Revision: <?php echo $this->lastsession->appname; ?></div>
						</div>
					</div>
				</div>

				<div class="option col-lg-3 col-md-6">
					<div class="inner">
						<header><h3>Current Sessions</h3></header>
						<div class="content"><?php echo $this->sessions; ?></div>
					</div>
				</div>

				<div class="option col-lg-3 col-md-6">
					<div class="inner">
						<header><h3>Used Displays</h3></header>
						<div class="content"><?php echo $this->used_displays; ?></div>
					</div>
				</div>

				<div class="option col-lg-3 col-md-6">
					<div class="inner">
						<header><h3>Ready Displays</h3></header>
						<div class="content"><?php echo $this->ready_displays; ?></div>
					</div>
				</div>

				<div class="option col-lg-3 col-md-6">
					<div class="inner">
						<header><h3>Absent Displays</h3></header>
						<div class="content"><?php echo $this->absent_displays; ?></div>
					</div>
				</div>
				
				<div class="option col-lg-3 col-md-6">
					<div class="inner">
						<header><h3>Broken Displays</h3></header>
						<div class="content"><?php echo $this->broken_displays; ?></div>
					</div>
				</div>

			</div>
	</form>
</section><!-- /.main section -->