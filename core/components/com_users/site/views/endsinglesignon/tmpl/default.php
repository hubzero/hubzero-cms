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

// No direct access.
defined('_HZEXEC_') or die();
?>

<section class="main section">
	<h3>Would you like to completely log out of your <?php echo $this->display_name; ?> account?</h2>
	<p>
		Your <?php echo $this->sitename; ?> session has ended.
	</p>
	<p>
		If you would like to end all <?php echo $this->display_name; ?> account shared sessions as well, you may do so now.
	</p>
	<p>
		<a class="logout btn" href="<?php echo Route::url('index.php?option=com_users&task=user.logout&sso=all&authenticator=' . $this->authenticator); ?>">
			End all <?php echo $this->display_name; ?> account sessions!
		</a>
	</p>
	<p>
		<a class="home btn" href="<?php echo Route::url('index.php?option=com_users&task=user.logout&sso=none&authenticator=' . $this->authenticator .'&return=' . Request::base()); ?>">
			Leave other <?php echo $this->display_name; ?> account sessions untouched.
		</a>
	</p>
</section>
