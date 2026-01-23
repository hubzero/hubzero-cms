<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v2\AwsS3Adapter;
use League\Flysystem\Filesystem;

/**
 * Plugin class for AWS S3 filesystem connectivity
 */
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
class plgFilesystemAWSS3 extends \Hubzero\Plugin\Plugin
{
    /**
     * Initializes the AWS S3 connection
     *
     * @param   array   $params  Any connection params needed
     * @return  object
     **/
    public static function init($params = [])
    {
        // Get the params
        $pparams = Plugin::params('filesystem', 'awss3');

        $app_id = $params['app_id'];
        $app_secret = $params['app_secret'];
        $region = $params['region'];
        $bucket = $params['bucket'];
        $path = isset($params['path']) ? $params['path'] : '';

        $client = S3Client::factory([
            'key'           => $app_id,
            'secret'    => $app_secret,
            'region'    => $region,
            'base_url' => 'http://s3.amazonaws.com',
            'signature' => 'v4'
        ]);
        $adapter = new AwsS3Adapter($client, $bucket, $path);
        return $adapter;
    }
}
