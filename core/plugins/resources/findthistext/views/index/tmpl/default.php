<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

// parse the custom fields out of the resource
$resourceFields = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->model->fulltxt, $matches, PREG_SET_ORDER);
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
						$doiUrl = 'https://doi.org/' . $resourceFields['doi'];
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
							'title' => $this->model->title
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
					elseif ($this->model->resource && $this->model->resource->title)
					{
						$query .= $this->model->title;
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
							$url = 'http://www.deepdyve.com/search?query=' . str_replace(' ', '+', $this->model->title);
							echo Lang::txt('PLG_RESOURCES_FINDTHISTEXT_SOURCES_DEEPDYVE', $url);
						?>
					</li>
				</ul>
			</td>
		</tr>
	</tbody>
</table>