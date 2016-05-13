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
?>
<table class="results" id="orcid-results-list">
	<tbody>
		<?php if (count($this->records)) { ?>
			<?php
			foreach ($this->records as $record)
			{
				if (!$record || empty($record))
				{
					continue;
				}
				$fname     = array_key_exists('given-names', $record) ? $record['given-names'] : '';
				$lname     = array_key_exists('family-name', $record) ? $record['family-name'] : '';
				$orcid_uri = array_key_exists('uri', $record)         ? $record['uri'] : '';
				$orcid     = array_key_exists('path', $record)        ? $record['path'] : '';

				/*$scheme = Request::scheme();
				if ($orcid_uri && substr($orcid_uri, 0, strlen($scheme)) != $scheme)
				{
					$orcid_uri = preg_replace('/^([^:]+)/i', $scheme, $orcid_uri);
				}*/
				?>
				<tr>
					<td>
						<a class="title" target="_blank" rel="external" href="<?php echo $orcid_uri; ?>">
							<?php echo $fname . ' ' . $lname; ?>
						</a>
					</td>
					<td>
						<span class="orcid">
							<?php echo $orcid; ?>
						</span>
					</td>
					<td>
						<a class="btn" onclick="<?php echo 'HUB.Orcid.associateOrcid(\'orcid\', \'' . $orcid . '\');'; ?>">
							<?php echo Lang::txt('Associate this ORCID'); ?>
						</a>
					</td>
				</tr>
				<?php
			}
			?>
		<?php } else { ?>
			<tr>
				<td class="no-results">
					<?php echo Lang::txt('No results found.'); ?>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>