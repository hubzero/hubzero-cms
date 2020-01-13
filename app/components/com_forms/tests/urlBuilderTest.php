<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/urlBuilder.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\UrlBuilder;

class UrlBuilderTest extends Basic
{

	public function testGenerateUrlConnectsSegments()
	{
		$expectedUrl = '/foo/9/end';
		$builder = new UrlBuilder();
		$segments = ['foo', 9, 'end'];

		$generatedUrl = $builder->generateUrl($segments);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

	public function testGenerateUrlAddsParameters()
	{
		$expectedUrl = '/base?a=1&b=foo';
		$builder = new UrlBuilder();
		$segments = ['base'];
		$parameters = [
			'a' => 1,
			'b' => 'foo'
		];

		$generatedUrl = $builder->generateUrl($segments, $parameters);

		$this->assertEquals($expectedUrl, $generatedUrl);
	}

}
