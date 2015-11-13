<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$numaff = 0;
$numnon = 0;

// Did we get any results back?
if ($this->citations) {
	// Get a needed library
	include_once( PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php' );

	// Set some vars
	$affiliated = '';
	$nonaffiliated = '';

	$formatter = new \Components\Citations\Helpers\Format;
	$formatter->setTemplate($this->format);

	// Loop through the citations and build the HTML
	foreach ($this->citations as $cite)
	{

		$showLinks = ($cite->title && $cite->author && $cite->publisher) ? true : false;

		$item  = "\t".'<li>'."\n";
		$formatted = $cite->formatted ? $cite->formatted : \Components\Citations\Helpers\Format::formatReference($cite, '');

		if ($cite->doi && $cite->url)
		{
			$formatted = str_replace('doi:' . $cite->doi, '<a href="' . $cite->url . '" rel="external">'
				. 'doi:' . $cite->doi . '</a>', $formatted);
		}

		$item .= $formatted;
		$item .= "\t\t".'<p class="details">'."\n";
		if ($showLinks)
		{
			$item .= "\t\t\t".'<a href="' . Route::url('index.php?option=com_citations&task=download&id=' . $cite->id . '&citationFormat=bibtex&no_html=1') . '" title="' . Lang::txt('PLG_PUBLICATION_CITATIONS_DOWNLOAD_BIBTEX').'">BibTex</a> <span>|</span> '."\n";
			$item .= "\t\t\t".'<a href="' . Route::url('index.php?option=com_citations&task=download&id=' . $cite->id . '&citationFormat=endnote&no_html=1') . '" title="' . Lang::txt('PLG_PUBLICATION_CITATIONS_DOWNLOAD_ENDNOTE') . '">EndNote</a>'."\n";
		}
		if ($cite->eprint) {
			if ($cite->eprint) {
				$item .= "\t\t\t".' <span>|</span> <a href="' . stripslashes($cite->eprint) . '">' . Lang::txt('PLG_PUBLICATION_CITATIONS_ELECTRONIC_PAPER') . '</a>'."\n";
			}
		}
		$item .= "\t\t".'</p>'."\n";

		$item .= "\t".'</li>'."\n";

		// Decide which group to add it to
		if ($cite->affiliated) {
			$affiliated .= $item;
			$numaff++;
		} else {
			$nonaffiliated .= $item;
			$numnon++;
		}
	}
}
?>
<h3>
	<a name="citations"></a>
	<?php echo Lang::txt('PLG_PUBLICATION_CITATIONS'); ?>
	<span>
		<a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=citations&v=' . $this->publication->version_number . '#nonaffiliated'); ?>"><?php echo Lang::txt('PLG_PUBLICATION_CITATIONS_NONAFF'); ?> (<?php echo $numnon; ?>)</a> |
		<a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=citations&v=' . $this->publication->version_number . '#affiliated'); ?>"><?php echo Lang::txt('PLG_PUBLICATION_CITATIONS_AFF'); ?> (<?php echo $numaff; ?>)</a>
	</span>
</h3>
<?php
if ($this->citations) {
	if ($nonaffiliated) {
?>
	<a name="nonaffiliated"></a>
	<h4><?php echo Lang::txt('PLG_PUBLICATION_CITATIONS_NOT_AFFILIATED'); ?></h4>
	<ul class="citations results">
		<?php echo $nonaffiliated; ?>
	</ul>
<?php
	}
	if ($affiliated) {
?>
	<a name="affiliated"></a>
	<h4><?php echo Lang::txt('PLG_PUBLICATION_CITATIONS_AFFILIATED'); ?></h4>
	<ul class="citations results">
		<?php echo $affiliated; ?>
	</ul>
<?php
	}
} else {
?>
	<p><?php echo Lang::txt('PLG_PUBLICATION_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
<?php
}
?>
