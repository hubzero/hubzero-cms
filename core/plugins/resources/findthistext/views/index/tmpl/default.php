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

$this->css();

// parse the custom fields out of the resource
$resourceFields = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->model->resource->fulltxt, $matches, PREG_SET_ORDER);
if (count($matches) > 0)
{
	foreach ($matches as $match)
	{
		$resourceFields[$match[1]] = $match[2];
	}
}
?>

<h3><?php echo Lang::txt('PLG_RESOURCES_FINDTHISTEXT'); ?></h3>
<p><?php echo Lang::txt('PLG_RESOURCES_FINDTHISTEXT_DESC'); ?></p>

<table class="find">
	<tbody>
		<?php if (isset($resourceFields['doi']) && $resourceFields['doi'] != '') : ?>
			<tr>
				<th>
					<?php echo Lang::txt('PLG_RESOURCES_FINDTHISTEXT_FIELD_DOI_LABEL'); ?>
				</th>
				<td>
					<?php
						// make sure have a valid url
						$doiUrl = 'http://dx.doi.org/' . $resourceFields['doi'];
					?>
					<a rel="external" href="<?php echo $doiUrl; ?>">
						<?php echo $doiUrl; ?>
					</a>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ($this->openurl) : ?>
			<tr>
				<th>
					<?php echo Lang::txt('PLG_RESOURCES_FINDTHISTEXT_FIELD_LOCALLIBRARY_LABEL'); ?>
				</th>
				<td>
					<?php echo Lang::txt('PLG_RESOURCES_FINDTHISTEXT_FIELD_LOCALLIBRARY_DESC'); ?>
					<?php
						$text  = $this->openurl->text;
						$image = "<img src=\"{$this->openurl->icon}\" alt=\"\" />";

						// add field data to local library link
						$fields   = array('doi','isbn','issn');
						$linkData = array(
							'title' => $this->model->resource->title
						);
						foreach ($fields as $field)
						{
							if (isset($resourceFields[$field]) && $resourceFields[$field] != '')
							{
								$linkData[$field] = $resourceFields[$field];
							}
						}

						// build link
						$link  = rtrim($this->openurl->link, '?') . '?' . http_build_query($linkData);
					?>
					<a rel="external" href="<?php echo $link; ?>">
						<?php echo ($this->openurl->icon) ? $image : $text; ?>
					</a>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th>
				<?php echo Lang::txt('PLG_RESOURCES_FINDTHISTEXT_FIELD_GOOGLESCHOLAR_LABEL'); ?>
			</th>
			<td>
				<?php
					$query = '';
					if (isset($resourceFields['doi']) && $resourceFields['doi'] != '')
					{
						$query .= $resourceFields['doi'];
					}
					elseif ($this->model->resource->title)
					{
						$query .= $this->model->resource->title;
					}
					?>
				<a rel="external" title="Google Scholar Search Results" href="http://scholar.google.com/scholar?q=<?php echo $query; ?>">
					<img src="http://scholar.google.com/intl/en/scholar/images/scholar_logo_lg_2011.gif" alt="Google Scholar Search Results" width="100" />
				</a>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo Lang::txt('PLG_RESOURCES_FINDTHISTEXT_FIELD_OTHERSOURCES_LABEL'); ?>
			</th>
			<td>
				<ul>
					<li>
						<?php
							$url = 'http://www.deepdyve.com/search?query=' . str_replace(' ', '+',  $this->model->resource->title);
							echo Lang::txt('PLG_RESOURCES_FINDTHISTEXT_SOURCES_DEEPDYVE', $url);
						?>
					</li>
				</ul>
			</td>
		</tr>
	</tbody>
</table>