<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Captcha\Image\Tests;

use PHPUnit\Framework\TestCase;
use Plugins\Captcha\Image\Image;
use ReflectionClass;

require_once dirname(__DIR__) . '/image.php';

/**
 * The image captcha draws its picture with a method it names from the
 * plugin's imageFunction setting: createImage plus the setting's value,
 * falling back to createImageAdv. Every value the settings form offers
 * must name a method, or that choice draws nothing.
 */
class DispatchTest extends TestCase
{
    /**
     * @return  void
     */
    public function testEveryImageFunctionTheFormOffersHasAMethod(): void
    {
        $manifest = simplexml_load_file(dirname(__DIR__) . '/image.xml');
        $values   = [];
        foreach ($manifest->xpath('//field[@name="imageFunction"]/option') as $option) {
            $values[] = (string) $option['value'];
        }
        $this->assertNotEmpty($values);

        $class = new ReflectionClass(Image::class);
        foreach ($values as $value) {
            $this->assertTrue($class->hasMethod('createImage' . $value), "imageFunction $value has no createImage$value()");
        }
        $this->assertTrue($class->hasMethod('createImageAdv'), 'the fallback createImageAdv() is missing');
    }
}
