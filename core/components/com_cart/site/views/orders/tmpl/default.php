<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

$this->css()
	->js()
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_CART_ORDERS') ?></h2>
</header>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" id="ordersform" method="get">
	<section class="main section">
		<div class="section-inner">
			<?php
			if (!$this->transactions)
			{
				echo '<p class="no-results">You have not placed any orders yet. Is it time to <a href="/storefront">start shopping?</a>';
			}
			else {
				echo '<ol class="entries">';
				foreach ($this->transactions as $transaction)
				{
					// Instantiate a new view
					$this->view('transaction', 'orders')
						->set('transaction', $transaction)
						->display();
				}
				echo '</ol>';
			}
			?>
		</div>

		<?php
		if ($this->transactions)
		{
			// Initiate paging
			$pageNav = $this->pagination(
				$this->total,
				$this->filters['start'],
				$this->filters['limit']
			);
			echo $pageNav->render();
		}
		?>
	</section>
</form>