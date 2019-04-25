<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$current = Request::current();

$append = '?';
if (strstr($current, '?'))
{
	$append = '&';
}

$this->css();
?>

<div class="captcha-block">
	<div class="grid">
		<div class="col span8">
			<div class="form-group">
				<label for="imgCatchaTxt<?php echo $this->total; ?>">
					<?php echo Lang::txt('PLG_CAPTCHA_IMAGE_ENTER_CAPTCHA_VALUE'); ?>
					<input type="text" class="form-control" name="imgCatchaTxt" id="imgCatchaTxt<?php echo $this->total; ?>" />
				</label>
			</div>

			<input type="hidden" name="imgCatchaTxtInst" id="imgCatchaTxtInst" value="<?php echo $this->total; ?>" />
		</div>
		<div class="col span4 omega">
			<div class="captcha-wrap">
				<img id="captchaCode<?php echo $this->total; ?>" src="<?php echo $current . htmlentities($append); ?>showCaptcha=True&amp;instanceNo=<?php echo $this->total; ?>" alt="<?php echo Lang::txt('PLG_CAPTCHA_IMAGE_ALT'); ?>" />

				<script type="text/javascript">
					//<![CDATA[
					function reloadCapthcha<?php echo $this->total; ?>(instanceNo)
					{
						var captchaSrc = "<?php echo $current . $append; ?>showCaptcha=True&instanceNo="+instanceNo+"&time="+ new Date().getTime();
						document.getElementById('captchaCode'+instanceNo).src = captchaSrc;
					}
					//]]>
				</script>

				<a class="tooltips" href="#" onclick="reloadCapthcha<?php echo $this->total; ?>(<?php echo $this->total; ?>);return false;" title="<?php echo Lang::txt('PLG_CAPTCHA_IMAGE_REFRESH_CAPTCHA'); ?>"><?php echo Lang::txt('PLG_CAPTCHA_IMAGE_REFRESH_CAPTCHA'); ?></a>
			</div>
		</div>
	</div>
</div>
