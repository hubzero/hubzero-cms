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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$url = Route::url($this->publication->link() . '&v=' . $this->publication->versionAlias . '&active=forks&action=fork');
if (User::isGuest())
{
	$url = Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url));
}
else
{
	$this->js();
}
?>
<div class="btn-group item-fork">
	<a class="btn icon-fork" id="fork-this" href="<?php echo $url; ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_FORK'); ?></a>
	<a class="btn" href="<?php echo Route::url($this->publication->link() . '&v=' . $this->publication->versionAlias . '&active=forks'); ?>" title="<?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_FORKED_N_TIMES', $this->forks); ?>"><?php echo $this->forks; ?></a>
</div>
