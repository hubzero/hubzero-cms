<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$this->css('enroll')
     ->js('enroll');

\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect');
?>

<form class="authy" action="<?php echo Request::current(); ?>" method="POST">
	<div class="title">Authy</div>
	<div class="img-wrap">
		<img class="logo" src="/core/plugins/authfactors/authy/assets/img/authy_logo.svg" alt="authy logo" />
	</div>
	<div class="grouping">
		<label for="country_code">Country Code</label>
		<select name="country_code" class="country_code <?php echo App::get('client')->name; ?>">
			<option value="1">United States of America (+1)</option>
		</select>
	</div>

	<div class="grouping">
		<label for="phone">Phone:</label>
		<input type="text" name="phone" value="<?php echo User::get('phone'); ?>" placeholder="XXX-XXX-XXXX" />
	</div>

	<div class="grouping">
		<label for="email">Email:</label>
		<input type="text" name="email" value="<?php echo User::get('email'); ?>" />
	</div>

	<input type="hidden" name="action" value="register" />
	<input type="hidden" name="factor" value="authy" />
	<div class="grouping">
		<input type="submit" value="Submit" class="btn btn-success" />
	</div>
</form>
