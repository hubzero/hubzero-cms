<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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