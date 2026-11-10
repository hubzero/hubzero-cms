<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Support\Captcha\Tests;

use PHPUnit\Framework\TestCase;
use Plugins\Support\Captcha\Captcha;
use ReflectionClass;

require_once dirname(__DIR__) . '/captcha.php';

/**
 * The support captcha builds its form with a method it names from the
 * modCaptcha or comCaptcha setting (generate, the type, Captcha) and draws
 * its picture with one named from imageFunction (createImage, the value,
 * falling back to createImageAdv). Every value the settings form offers
 * must name a method, or that choice breaks the form it is on.
 */
class DispatchTest extends TestCase
{
    /**
     * The values the settings form offers for a field
     *
     * @param   string  $field
     * @return  array
     */
    private function options(string $field): array
    {
        $manifest = simplexml_load_file(dirname(__DIR__) . '/captcha.xml');
        $values   = [];
        foreach ($manifest->xpath('//field[@name="' . $field . '"]/option') as $option) {
            $values[] = (string) $option['value'];
        }
        $this->assertNotEmpty($values, "the form offers no $field");
        return $values;
    }

    /**
     * @return  void
     */
    public function testEveryCaptchaTypeTheFormOffersHasAMethod(): void
    {
        $class = new ReflectionClass(Captcha::class);
        foreach (['modCaptcha', 'comCaptcha'] as $field) {
            foreach ($this->options($field) as $type) {
                $method = 'generate' . ucfirst(strtolower($type)) . 'Captcha';
                $this->assertTrue($class->hasMethod($method), "$field $type has no $method()");
            }
        }
    }

    /**
     * @return  void
     */
    public function testEveryImageFunctionTheFormOffersHasAMethod(): void
    {
        $class = new ReflectionClass(Captcha::class);
        foreach ($this->options('imageFunction') as $value) {
            $this->assertTrue($class->hasMethod('createImage' . $value), "imageFunction $value has no createImage$value()");
        }
        $this->assertTrue($class->hasMethod('createImageAdv'), 'the fallback createImageAdv() is missing');
    }
}
