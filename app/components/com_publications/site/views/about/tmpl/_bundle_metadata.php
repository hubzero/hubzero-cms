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
 * @author    Anthony Fuentes <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div class="grid bundle-data">
	<div class="grid bundle-meta">
		<div class="col span4">
			<ul class="bundle-info">
				<li><?php echo Lang::txt('COM_PUBLICATIONS_BUNDLE_CONTENT'); ?></li>
				<li><span class="bundle-size"><?php echo Hubzero\Utility\Number::formatBytes($this->bundle->getSize()); ?></span></li>
			</ul>
		</div>
		<div class="col span8 omega">
			<div class="bundle-checksum">
				<span class="bundle-checksum-value">md5:<?php echo $this->bundle->getMd5(); ?></span>
				<span class="bundle-checksum-help icon-help tooltips" title="<?php echo Lang::txt('COM_PUBLICATIONS_BUNDLE_CHECKSUM'); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_BUNDLE_CHECKSUM'); ?></span>
			</div>
		</div>
	</div>
	<div class="bundle-files">
		<ul class="filelist">
			<?php
				$this->view('_bundle_contents')
					->set('contents', $this->bundle->getContents())
					->display();
			?>
		</ul>
	</div>
</div>
