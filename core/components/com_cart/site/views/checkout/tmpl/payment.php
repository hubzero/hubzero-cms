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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

/*$this->css()
     ->js('spin.min.js')
     ->js('autosubmit.js');*/

$this->css('jquery.ui.css', 'system');
?>

<header id="content-header">
	<h2>Payment</h2>
</header>

<section class="section">
	<?php
	/*$view = new \Hubzero\Component\View(array('name'=>'shared', 'layout' => 'messages'));
	$errors = $this->getError();
	$view->setError($this->getError());
	$view->display();

	if (empty($errors))
	{*/
		?>
		<?php
		//print_r($this->paymentCode);

		// load the plugin
		// fire plugin
		$payment_options = Event::trigger('cart.onRenderPaymentOptions', array($this->transaction, User::getRoot()));

		//print_r($payment_options); die;

		if (count($payment_options) > 1)
		{
			echo '<p>Please choose your method of payment.</p>';
		}

		if (count($payment_options))
		{
			$pane = '1';

			echo Html::sliders('start', "pane_$pane");

			foreach ($payment_options as $method)
			{
				echo Html::sliders('panel', $method['title'], $method['title']);
				echo $method['options'];
			}

			echo Html::sliders('end');
		}
		else
		{
			echo Lang::txt('NO_PAYMENT_OPTIONS_AVAILABLE');
		}
	//}
	?>
</section>