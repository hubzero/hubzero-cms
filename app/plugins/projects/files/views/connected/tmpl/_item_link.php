<?php
use Hubzero\Utility\Arr;

$connectionId = $this->connectionId;
$item = $this->item;
$itemName = $this->itemName;
$match = [];
$model = $this->model;
try
{
	$itemMimeType = $item->getMimeType();
}
catch (Exception $e)
{
	$itemMimeType = null;
}

if ($itemMimeType && preg_match('/^application\/vnd\.google\-apps\.([^.]+)/', $itemMimeType, $match))
{
	$googleMimetypeMap = [
		'document'     => 'gdoc',      // Google Docs
		'form'         => 'gform',     // Google form
		'presentation' => 'gslides',   // Google Slides
		'spreadsheet'  => 'gsheet',    // Google Sheets
		'map'          => 'gmap',      // Google Maps
	];

	$format = Arr::getValue($match, 1, null);

	$itemExtension = Arr::getValue($googleMimetypeMap, $format, null);
}
else
{
	$itemExtension = $item->getExtension();
}

echo \Components\Projects\Models\File::drawIcon($itemExtension);
if ($this->itemIsFile)
{
	$this->view('_item_link_file')
		->set('connectionId', $connectionId)
		->set('handlerBase', $this->handlerBase)
		->set('itemFileName', $item->getFileName())
		->set('itemMimeType', $itemMimeType)
		->set('itemName', $itemName)
		->set('itemPath', $this->itemPath)
		->set('model', $model)
		->set('subdirPath', $this->subdirPath)
		->display();
}
else
{
	$this->view('_item_link_directory')
		->set('connectionId', $connectionId)
		->set('itemDisplayName', $item->getDisplayName())
		->set('itemName', $itemName)
		->set('model', $model)
		->set('urlEncodedItemPath', $this->urlEncodedItemPath)
		->display();
}
