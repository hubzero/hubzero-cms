<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for dropping the steps index
  *
**/
class Migration20150916164629ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

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

        if (empty($downloadFolder) || $downloadFolder == '/media/software') {
            $downloadFolder = DS . 'site' . DS . 'protected' . DS . 'storefront' . DS . 'software';
        }

        $params->set('downloadFolder', $downloadFolder);

        if (empty($imagesFolder)) {
            $imagesFolder = DS . 'site' . DS . 'storefront' . DS . 'products';
        }

        $params->set('imagesFolder', $imagesFolder);

        if (empty($collectionsImagesFolder)) {
            $collectionsImagesFolder = DS . 'site' . DS . 'storefront' . DS . 'collections';
        }

        $params->set('collectionsImagesFolder', $collectionsImagesFolder);

        if (!is_dir(PATH_APP . DS . trim($downloadFolder, DS))) {
            mkdir(PATH_APP . DS . trim($downloadFolder, DS), 0775, true);
        }

        if (!is_dir(PATH_APP . DS . trim($imagesFolder, DS))) {
            mkdir(PATH_APP . DS . trim($imagesFolder, DS), 0775, true);
        }

        if (!is_dir(PATH_APP . DS . trim($collectionsImagesFolder, DS))) {
            mkdir(PATH_APP . DS . trim($collectionsImagesFolder, DS), 0775, true);
        }

        $this->saveParams('com_storefront', $params);

        // Add a new index
        if ($schema->tableExists('#__storefront_product_meta')) {
            $schema->addUniqueIndex('#__storefront_product_meta', 'uniqueKey', ['pId', 'pmKey']);
        }

        if (
            $schema->tableExists('#__storefront_option_groups')
            && !$schema->hasColumn('#__storefront_option_groups', 'ogActive')
        ) {
            $schema->addColumn('#__storefront_option_groups', 'ogActive')->tinyInteger()->default(0)->execute();
        }

        if (
            $schema->tableExists('#__storefront_option_groups')
            && $schema->hasColumn('#__storefront_option_groups', 'ogName')
        ) {
            $schema->modifyColumn('#__storefront_option_groups', 'ogName')->string(100)->execute();
        }

        if (
            $schema->tableExists('#__storefront_options')
            && !$schema->hasColumn('#__storefront_options', 'oActive')
        ) {
            $schema->addColumn('#__storefront_options', 'oActive')->tinyInteger()->default(0)->execute();
        }

        if ($schema->tableExists('#__storefront_skus') && $schema->hasColumn('#__storefront_skus', 'sSku')) {
            $schema->modifyColumn('#__storefront_skus', 'sSku')->string(100)->execute();
        }

        if (!$schema->tableExists('#__storefront_images')) {
            $schema->createTable('#__storefront_images')
                ->unsignedInteger('imgId', ['autoIncrement' => true])
                ->char('imgName', 255)->nullable()
                ->char('imgObject', 25)->nullable()
                ->integer('imgObjectId')->nullable()
                ->tinyInteger('imgPrimary')->default(1)
                ->primaryKey('imgId')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        // Drop index
        if ($schema->tableExists('#__storefront_product_meta')) {
            $schema->dropIndex('#__storefront_product_meta', 'uniqueKey');
        }

        if (
            $schema->tableExists('#__storefront_option_groups')
            && $schema->hasColumn('#__storefront_option_groups', 'ogActive')
        ) {
            $schema->dropColumn('#__storefront_option_groups', 'ogActive');
        }

        if (
            $schema->tableExists('#__storefront_options')
            && $schema->hasColumn('#__storefront_options', 'oActive')
        ) {
            $schema->dropColumn('#__storefront_options', 'oActive');
        }

        $schema->dropTable('#__storefront_images');
    }
}
