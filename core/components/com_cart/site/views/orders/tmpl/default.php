<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    HUBzero
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

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