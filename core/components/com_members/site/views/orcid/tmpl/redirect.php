<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2017 HUBzero Foundation, LLC.
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
 * @author    Jerry Kuang <kuang5@purdue.edu>
 * @copyright Copyright 2005-2017 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<section class="main section">
      <div class="grid nobreak">
		<?php if (Request::getVar('code')) { ?>
		<h1><?php echo Lang::txt('COM_MEMBERS_REDIRECT_ORCID_THANK_YOU'); ?><?php echo $this->userName; ?><?php echo Lang::txt('COM_MEMBERS_REDIRECT_ORCID_EXCLAMATION_MARK'); ?></h1>
		<br>
		<p><?php echo Lang::txt('COM_MEMBERS_REDIRECT_ORCID_YOUR_ORCID'); ?><img src="<?php echo Request::root()?>/core/components/com_members/site/assets/img/orcid_16x16.png" class="logo" width='16' height='16' alt="iD"/> <?php echo Lang::txt('COM_MEMBERS_REDIRECT_ORCID_IS'); ?> <?php echo $this->userORCID; ?></p>
		<?php } elseif (Request::getVar('error') && Request::getVar('error_description')) { ?>
		<p><?php echo Lang::txt('COM_MEMBERS_REDIRECT_ORCID_DENY'); ?><a class="btn" href="https://orcid.org/signin" target="_blank"><?php echo Lang::txt('COM_MEMBERS_REDIRECT_ORCID_SIGN_IN_OR_REGISTER'); ?></a></p>
		<?php } ?>
      </div>
</section>