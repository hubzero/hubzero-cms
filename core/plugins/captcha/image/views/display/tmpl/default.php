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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = Request::base();
if (Request::isSecure())
{
	$base = str_replace('http://', 'https://', $base);
}

$controller = Request::getCmd('controller', Request::getCmd('view', ''));

$this->css();
?>

<div class="captcha-block">
	<div class="grid">
		<div class="col span8">
			<label for="imgCatchaTxt<?php echo $this->total; ?>">
				<?php echo Lang::txt('PLG_CAPTCHA_IMAGE_ENTER_CAPTCHA_VALUE'); ?>
				<input type="text" name="imgCatchaTxt" id="imgCatchaTxt<?php echo $this->total; ?>" />
			</label>

			<input type="hidden" name="imgCatchaTxtInst" id="imgCatchaTxtInst" value="<?php echo $this->total; ?>" />
		</div>
		<div class="col span4 omega">
			<div class="captcha-wrap">
				<img id="captchaCode<?php echo $this->total; ?>" src="<?php echo $base; ?>index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $controller; ?>&amp;task=<?php echo $this->task; ?>&amp;no_html=1&amp;showCaptcha=True&amp;instanceNo=<?php echo $this->total; ?>" alt="CAPTCHA Image" />

				<script type="text/javascript">
					//<![CDATA[
					function reloadCapthcha<?php echo $this->total; ?>(instanceNo)
					{
						var captchaSrc = "<?php echo $base; ?>index.php?option=<?php echo $this->option; ?>&controller=<?php echo $controller; ?>&task=<?php echo $this->task; ?>&no_html=1&showCaptcha=True&instanceNo="+instanceNo+"&time="+ new Date().getTime();
						document.getElementById('captchaCode'+instanceNo).src = captchaSrc;
					}
					//]]>
				</script>

				<a class="tooltips" href="#" onclick="reloadCapthcha<?php echo $this->total; ?>(<?php echo $this->total; ?>);return false;" title="<?php echo Lang::txt('PLG_CAPTCHA_IMAGE_REFRESH_CAPTCHA'); ?>"><?php echo Lang::txt('PLG_CAPTCHA_IMAGE_REFRESH_CAPTCHA'); ?></a>
			</div><!-- /.captcha-wrap -->
		</div>
	</div><!-- / .grid -->
</div><!-- /.captcha-block -->
