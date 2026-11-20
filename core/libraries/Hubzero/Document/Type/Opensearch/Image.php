<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type\Opensearch;

use Hubzero\Base\Obj;

/**
 * Image for the OpenSearch Description
 *
 * Inspired by Joomla's JOpenSearchImage class
 */
class Image extends Obj
{
    /**
     * The images MIME type
     *
     * @var  string
     */
    public $type = '';

    /**
     * URL of the image or the image as base64 encoded value
     *
     * @var  string
     */
    public $data = '';

    /**
     * The image's width
     *
     * @var  string
     */
    public $width;

    /**
     * The image's height
     *
     * @var  string
     */
    public $height;
}
