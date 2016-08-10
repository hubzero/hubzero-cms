<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for dropping the steps index
 **/
class Migration20150916164629ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Create software/images folders if needed
		$params = $this->getParams('com_storefront');

		$downloadFolder = $params->get('downloadFolder');
		$imagesFolder = $params->get('imagesFolder');
		$collectionsImagesFolder = $params->get('collectionsImagesFolder');

		if (substr($downloadFolder, 0, 4) == '/app') {
			$downloadFolder = substr($downloadFolder, 4);
		}

		if (substr($imagesFolder, 0, 4) == '/app') {
			$imagesFolder = substr($imagesFolder, 4);
		}

		if (substr($collectionsImagesFolder, 0, 4) == '/app') {
			$collectionsImages = substr($collectionsImagesFolder, 4);
		}

		if (empty($downloadFolder) || $downloadFolder == '/media/software')
		{
			$downloadFolder = DS . 'site' . DS . 'protected' . DS . 'storefront' . DS . 'software';
		}

		$params->set('downloadFolder', $downloadFolder);

		if (empty($imagesFolder))
		{
			$imagesFolder = DS . 'site' . DS . 'storefront' . DS . 'products';
		}

		$params->set('imagesFolder', $imagesFolder);

		if (empty($collectionsImagesFolder))
		{
			$collectionsImagesFolder = DS . 'site' . DS . 'storefront' . DS . 'collections';
		}

		$params->set('collectionsImagesFolder', $collectionsImagesFolder);

		if (!is_dir(PATH_APP . DS . trim($downloadFolder, DS)))
		{
			mkdir(PATH_APP . DS . trim($downloadFolder, DS), 0775, true);
		}

		if (!is_dir(PATH_APP . DS . trim($imagesFolder, DS)))
		{
			mkdir(PATH_APP . DS . trim($imagesFolder, DS), 0775, true);
		}

		if (!is_dir(PATH_APP . DS . trim($collectionsImagesFolder, DS)))
		{
			mkdir(PATH_APP . DS . trim($collectionsImagesFolder, DS), 0775, true);
		}

		$this->saveParams('com_storefront', $params);

		// Add a new index
		if ($this->db->tableExists('#__storefront_product_meta'))
		{
			$query = "SHOW INDEX FROM `#__storefront_product_meta` WHERE Key_name = 'uniqueKey'";
			$this->db->setQuery($query);
			$this->db->execute();
			if ($this->db->getNumRows() <= 0)
			{
				$query = "ALTER TABLE `#__storefront_product_meta` ADD UNIQUE INDEX `uniqueKey` (`pId`,`pmKey`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__storefront_option_groups') && !$this->db->tableHasField('#__storefront_option_groups', 'ogActive')) {
			$query = "ALTER TABLE `#__storefront_option_groups` ADD `ogActive` TINYINT(1)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_option_groups') && $this->db->tableHasField('#__storefront_option_groups', 'ogName')) {
			$query = "ALTER TABLE `#__storefront_option_groups` MODIFY `ogName` CHAR(100)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_options') && !$this->db->tableHasField('#__storefront_options', 'oActive')) {
			$query = "ALTER TABLE `#__storefront_options` ADD `oActive` TINYINT(1)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_skus') && $this->db->tableHasField('#__storefront_skus', 'sSku')) {
			$query = "ALTER TABLE `#__storefront_skus` MODIFY `sSku` CHAR(100)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__storefront_images'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__storefront_images` (
				  `imgId` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				  `imgName` CHAR(255) DEFAULT NULL,
				  `imgObject` CHAR(25) DEFAULT NULL,
				  `imgObjectId` INT(11) DEFAULT NULL,
				  `imgPrimary` TINYINT(1) DEFAULT '1',
				  PRIMARY KEY (`imgId`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		// Drop index
		if ($this->db->tableExists('#__storefront_product_meta'))
		{
			$query = "SHOW INDEX FROM `#__storefront_product_meta` WHERE Key_name = 'uniqueKey'";
			$this->db->setQuery($query);
			$this->db->execute();
			if ($this->db->getNumRows() > 0)
			{
				$query = "DROP INDEX `uniqueKey` ON `#__storefront_product_meta`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__storefront_option_groups') && $this->db->tableHasField('#__storefront_option_groups', 'ogActive')) {
			$query = "ALTER TABLE `#__storefront_option_groups` DROP COLUMN `ogActive`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_options') && $this->db->tableHasField('#__storefront_options', 'oActive')) {
			$query = "ALTER TABLE `#__storefront_options` DROP COLUMN `oActive`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_images'))
		{
			$query = "DROP TABLE IF EXISTS `#__storefront_images`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

}
