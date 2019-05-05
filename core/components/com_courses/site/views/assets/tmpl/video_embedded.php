<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (preg_match('/<iframe(.*?)src="([^"]+)"([^>]*)>(.*?)<\/iframe>/si', $this->asset->content, $matches))
{
	if (stristr($matches[2], 'youtube'))
	{
		$url = $matches[2];

		$prfx = '&';
		if (!stristr($url, '?'))
		{
			$prfx = '?';
		}
		$url = str_replace('http:', 'https:', $url);
		$url .= $prfx . 'enablejsapi=1&origin=' . Request::base();

		$this->asset->content = str_replace($matches[2], $url, $this->asset->content);
		$this->asset->content = str_replace('<iframe', '<iframe id="player"', $this->asset->content);
	}
	else if (stristr($matches[2], 'vimeo'))
	{
		$url = $matches[2];

		$prfx = '&';
		if (!stristr($url, '?'))
		{
			$prfx = '?';
		}
		$url .= $prfx . 'api=1&amp;player_id=player';
		$url = str_replace('http:', 'https:', $url);

		$this->asset->content = str_replace($matches[2], $url, $this->asset->content);
		$this->asset->content = str_replace('<iframe', '<iframe id="player"', $this->asset->content);
	}
	else if (stristr($matches[2], 'blip'))
	{

	}
	else if (stristr($matches[2], 'kaltura'))
	{

	}
}
?>

<?php if ($this->asset->subtype == 'embedded') : ?>
	<div id="video-container" class="embedded-video">
		<?php echo $this->asset->content; ?>
		<?php
		if (stristr($this->asset->content, 'iframe')):
			if (stristr($this->asset->content, 'vimeo')):
				$this->js('//a.vimeocdn.com/js/froogaloop2.min.js?8cca6-1372090955');
				$this->js('video.js');
			endif;
		endif;
		?>
	</div><!-- /#video-container -->
<?php endif;
