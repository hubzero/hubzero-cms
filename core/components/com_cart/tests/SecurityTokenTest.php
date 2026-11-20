<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Tests;

use Components\Cart\Models\Cart;
use Hubzero\Config\Registry;
use Hubzero\Facades\Facade;
use PHPUnit\Framework\TestCase;

/**
 * A transaction's token is keyed with the hub's secret: it was the MD5 of
 * the transaction ID and a salt in the source, which anyone could compute
 */
class SecurityTokenTest extends TestCase
{
    /**
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->secret('first-hub-secret');
    }

    /**
     * @return  void
     */
    protected function tearDown(): void
    {
        $app = Facade::getApplication();
        $this->assertNotNull($app);
        $app->forget('config');

        parent::tearDown();
    }

    /**
     * Give the application a configuration with this secret
     *
     * @param   string  $secret
     * @return  void
     */
    private function secret(#[\SensitiveParameter] $secret)
    {
        $app = Facade::getApplication();
        $this->assertNotNull($app);
        $app->forget('config');
        $app['config'] = fn() => new Registry(['secret' => $secret]);
    }

    /**
     * A token verifies for its own transaction and no other
     *
     * @return  void
     */
    public function testTokenVerifiesForItsTransactionOnly()
    {
        $token = Cart::generateSecurityToken(42);

        $this->assertTrue(Cart::verifySecurityToken($token, 42));
        $this->assertFalse(Cart::verifySecurityToken($token, 43));
        $this->assertFalse(Cart::verifySecurityToken('', 42));
    }

    /**
     * The old token, computable from the source, is refused, and a hub with
     * another secret makes other tokens
     *
     * @return  void
     */
    public function testTokenDependsOnTheHubSecret()
    {
        $oldStyle = md5('ERDCVcvk$sad!ccsso====++!w' . 42);
        $this->assertFalse(Cart::verifySecurityToken($oldStyle, 42));

        $first = Cart::generateSecurityToken(42);
        $this->secret('second-hub-secret');

        $this->assertNotEquals($first, Cart::generateSecurityToken(42));
        $this->assertFalse(Cart::verifySecurityToken($first, 42));
    }

    /**
     * A hub with no secret cannot make tokens at all
     *
     * @return  void
     */
    public function testNoSecretMakesNoToken()
    {
        $this->secret('');

        $this->expectException(\Exception::class);
        Cart::generateSecurityToken(42);
    }
}
