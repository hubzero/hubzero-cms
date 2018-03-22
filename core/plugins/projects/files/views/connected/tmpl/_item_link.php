<?php
$connectionId = $this->connectionId;
$item = $this->item;
$itemName = $this->itemName;
$model = $this->model;

echo \Components\Projects\Models\File::drawIcon($item->getExtension());
if ($this->itemIsFile)
	{
		$this->view('_item_link_file')
			->set('connectionId', $connectionId)
			->set('handlerBase', $this->handlerBase)
			->set('itemFileName', $item->getFileName())
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
?>
