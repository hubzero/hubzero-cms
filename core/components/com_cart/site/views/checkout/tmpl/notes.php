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