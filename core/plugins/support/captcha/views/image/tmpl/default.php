<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$base = Request::base();
if (Request::isSecure())
{
	$base = str_replace('http://', 'https://', $base);
}
?>
<div class="captcha-block">

	<div class="grid">
		<div class="col span8">
			<label for="imgCatchaTxt<?php echo $this->total; ?>">
				<?php echo Lang::txt('PLG_SUPPORT_IMAGECAPTCHA_ENTER_CAPTCHA_VALUE'); ?>
				<input type="text" name="captcha[answer]" id="imgCatchaTxt<?php echo $this->total; ?>" />
			</label>

			<input type="hidden" name="captcha[instance]" id="imgCatchaTxtInst" value="<?php echo $this->total; ?>" />
		</div>
		<div class="col span4 omega">
			<div class="captcha-wrap">
				<img id="captchaCode<?php echo $this->total; ?>" src="<?php echo $base; ?>index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $this->task; ?>&amp;no_html=1&amp;showCaptcha=True&amp;instanceNo=<?php echo $this->total; ?>" alt="CAPTCHA Image" />
				<a class="tooltips" href="#" onclick="reloadCapthcha<?php echo $this->total; ?>(<?php echo $this->total; ?>);return false;" title="<?php echo Lang::txt('PLG_SUPPORT_IMAGECAPTCHA_REFRESH_CAPTCHA'); ?>"><?php echo Lang::txt('PLG_SUPPORT_IMAGECAPTCHA_REFRESH_CAPTCHA'); ?></a>
				<script type="text/javascript">
					//<![CDATA[
					function reloadCapthcha<?php echo $this->total; ?>(instanceNo)
					{
						var captchaSrc = "<?php echo $base; ?>index.php?option=<?php echo $this->option; ?>&controller=<?php echo $this->controller; ?>&task=<?php echo $this->task; ?>&no_html=1&showCaptcha=True&instanceNo="+instanceNo+"&time="+ new Date().getTime();
						document.getElementById('captchaCode'+instanceNo).src = captchaSrc;
					}
					//]]>
				</script>
			</div><!-- /.captcha-wrap -->
		</div><!-- / .col span-third omega -->
	</div><!-- / .grid -->

</div><!-- / .captcha-block -->