<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

$this->css();

?>

<header id="content-header">
	<h2>Checkout: Notes/Comments</h2>
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
				<form name="notes" class="full" method="post" id="hubForm">
					<fieldset>

						<?php

						$genericNotesLabel = 'Please add any notes/comments:';

						if (!empty($this->noteFields))
						{
							foreach ($this->noteFields as $sId => $field)
							{
								?>

								<label>
									<?php
									echo '<strong>' . $field['pName'] . ', ' . $field['sSku'] . ':</strong> ' . $field['sCheckoutNotes'];
									if ($field['sCheckoutNotesRequired'])
									{
										echo ' <em>Required</em>';
									}
									?>
									<textarea name="notes-<?php echo $sId; ?>"></textarea>
								</label>

								<?php
							}

							$genericNotesLabel = 'Other notes/comments:';
						}

						?>

						<label><?php echo $genericNotesLabel; ?>
							<textarea name="notes"></textarea>
						</label>

						<div class="submit">
							<input type="submit" value="Next" name="submitNotes" id="submitNotes" class="btn" />
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</section>