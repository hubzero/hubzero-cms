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

$numaff = 0;
$numnon = 0;

// Did we get any results back?
if ($this->citations)
{
	// Get a needed library
	include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php');

	// Set some vars
	$affiliated = '';
	$nonaffiliated = '';

	$formatter = new \Components\Citations\Helpers\Format;
	//$formatter->setFormat($this->format);

	// Loop through the citations and build the HTML
	foreach ($this->citations as $cite)
	{
		$item  = "\t" . '<li>' . "\n";
		$item .= \Components\Citations\Helpers\Format::formatReference($cite, '');
		$item .= "\t\t" . '<p class="details">' . "\n";
		$item .= "\t\t\t" . '<a href="' . Route::url('index.php?option=com_citations&task=download&id=' . $cite->id . '&citationFormat=bibtex&no_html=1') . '" title="' . Lang::txt('PLG_RESOURCES_CITATIONS_DOWNLOAD_BIBTEX') . '">BibTex</a> <span>|</span> ' . "\n";
		$item .= "\t\t\t" . '<a href="' . Route::url('index.php?option=com_citations&task=download&id=' . $cite->id . '&citationFormat=endnote&no_html=1') . '" title="' . Lang::txt('PLG_RESOURCES_CITATIONS_DOWNLOAD_ENDNOTE') . '">EndNote</a>' . "\n";
		if ($cite->eprint)
		{
			if ($cite->eprint)
			{
				$item .= "\t\t\t" . ' <span>|</span> <a href="' . stripslashes($cite->eprint) . '">' . Lang::txt('PLG_RESOURCES_CITATIONS_ELECTRONIC_PAPER') . '</a>' . "\n";
			}
		}
		$item .= "\t\t" . '</p>' . "\n";
		$item .= "\t" . '</li>' . "\n";

		// Decide which group to add it to
		if ($cite->affiliated)
		{
			$affiliated .= $item;
			$numaff++;
		}
		else
		{
			$nonaffiliated .= $item;
			$numnon++;
		}
	}
}
?>
<h3>
	<?php echo Lang::txt('PLG_RESOURCES_CITATIONS'); ?>
	<span>
		<a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=citations#nonaffiliated'); ?>"><?php echo Lang::txt('PLG_RESOURCES_CITATIONS_NONAFF'); ?> (<?php echo $numnon; ?>)</a> |
		<a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=citations#affiliated'); ?>"><?php echo Lang::txt('PLG_RESOURCES_CITATIONS_AFF'); ?> (<?php echo $numaff; ?>)</a>
	</span>
</h3>
<?php if ($this->citations) { ?>
	<?php if ($nonaffiliated) { ?>
		<h4><?php echo Lang::txt('PLG_RESOURCES_CITATIONS_NOT_AFFILIATED'); ?></h4>
		<ul class="citations results">
			<?php echo $nonaffiliated; ?>
		</ul>
	<?php } ?>
	<?php if ($affiliated) { ?>
		<h4><?php echo Lang::txt('PLG_RESOURCES_CITATIONS_AFFILIATED'); ?></h4>
		<ul class="citations results">
			<?php echo $affiliated; ?>
		</ul>
	<?php } ?>
<?php } else { ?>
	<p><?php echo Lang::txt('PLG_RESOURCES_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
<?php } ?>
