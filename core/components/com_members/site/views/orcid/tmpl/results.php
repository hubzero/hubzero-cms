<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
						<a class="title" rel="nofollow external" href="<?php echo $orcid_uri; ?>">
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