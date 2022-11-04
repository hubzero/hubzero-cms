<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$numaff = 0;
$numnon = 0;

// Did we get any results back?
if ($this->citations)
{
	// Get a needed library
	include_once Component::path('com_citations') . DS . 'helpers' . DS . 'format.php';

	// Set some vars
	$affiliated = '';
	$nonaffiliated = '';

	$formatter = new \Components\Citations\Helpers\Format;
	//$formatter->setFormat($this->format);

	// Loop through the citations and build the HTML
	foreach ($this->citations as $cite)
	{
		$item  = "\t" . '<li>' . "\n";
		$item .= $cite->formatted(array('format' => $this->citationFormat));
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

<div class="customfields">
	<?php
		// Parse for <nb:field> tags
		$type = $this->resource->type;

		$data = array();
		preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->resource->fulltxt, $matches, PREG_SET_ORDER);
		if (count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				$data[$match[1]] = str_replace('="/site', '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $match[2]);
			}
		}
		include_once Component::path('com_resources') . DS . 'models' . DS . 'elements.php';
		$elements = new \Components\Resources\Models\Elements($data, $this->resource->type->customFields);
		$schema = $elements->getSchema();
		$tab = Request::getCmd('active', 'citations');  // The active tab (section)

		if (is_object($schema))
		{
			if (!isset($schema->fields) || !is_array($schema->fields))
			{
				$schema->fields = array();
			}
			foreach ($schema->fields as $field)
			{
				if (isset($data[$field->name]))
				{
					if ($elements->display($field->type, $data[$field->name]) && isset($field->display) && $field->display == $tab )
					{
						?>
						<h4><?php echo $field->label; ?></h4>
						<div class="resource-content">
						<?php echo $elements->display($field->type, $data[$field->name]); ?>
						</div>
						<?php
					}
				}
			}
		}
	?>
</div><!-- / .customfields -->
