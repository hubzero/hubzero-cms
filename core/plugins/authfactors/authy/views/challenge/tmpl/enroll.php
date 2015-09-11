<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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