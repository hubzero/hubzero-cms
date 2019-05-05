<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title( Lang::txt( 'MEMBERS' ).': Manage Points', 'user.png' );

?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span>Find User Details</span></legend>

				<table class="admintable">
					<tbody>
						<tr>
							<td><label for="uid">UID:</label></td>
							<td><input type="text" name="uid" id="uid" size="30" maxlength="250" value="" /> <input type="submit" value="Go" /></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="col span6">
			<p class="info">Enter a user ID to view their point history and balance.</p>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="edit" />

	<?php echo Html::input('token'); ?>
</form>