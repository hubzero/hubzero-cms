<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
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
