<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

$this->css();

// Get the name of the product downloaded
$product = $this->productInfo->pName;
$product .= ', ' . $this->productInfo->oName;

?>

<header id="content-header">
	<h2>Checkout: user agreement</h2>
</header>

<?php

if (!empty($this->notifications))
{
	$view = new \Hubzero\Component\View(array('name'=>'shared', 'layout' => 'notifications'));
	$view->notifications = $this->notifications;
	$view->display();
}

?>

<section class="main section">
	<div class="section-inner">
		<?php
		$errors = $this->getError();
		if (!empty($errors))
		{
			foreach ($errors as $error)
			{
				echo '<p class="error">' . $error . '</p>';
			}
		}
		?>
		<div class="grid">
			<div class="col span12">
				<p>In order to continue downloading <strong><?php echo $product; ?></strong> you must agree to the user agreement:</p>

				<form name="eula" class="full" method="post" id="hubForm">
					<fieldset>
						<label>Please read the user agreement:
							<div class="eula"><?php echo $this->productEula; ?></div>
						</label>

						<fieldset>
							<legend>Please confirm that you accept the user agreement</legend>
							<label for="acceptEula"><input type="checkbox" class="option" name="acceptEula" id="acceptEula" /> I Accept</label>

							<p>If you don't accept the user agreement, <a href="<?php echo Route::url('index.php?option=com_cart'); ?>">cancel and return to cart</a></p>
						</fieldset>

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

						<div class="submit">
							<input type="submit" value="Next" name="submitEula" id="submitEula" class="btn" />
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</section>