<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Mail\Tests;

use Hubzero\Config\Registry;
use Hubzero\Facades\Facade;
use Hubzero\Mail\Message;
use PHPUnit\Framework\TestCase;

/**
 * Mail goes out through the transport the site configuration names.
 */
class TransportDsnTest extends TestCase
{
    /**
     * @var  mixed
     */
    private $previous = null;

    /**
     * @return  void
     */
    protected function tearDown(): void
    {
        $app = Facade::getApplication();
        $app->forget('config');
        if ($this->previous !== null) {
            $app['config'] = $this->previous;
        }
    }

    /**
     * @param   array  $settings
     * @return  void
     */
    private function configure(array $settings): void
    {
        $app = Facade::getApplication();
        if (isset($app['config'])) {
            $this->previous = $app['config'];
        }
        // An earlier test may have resolved the service, which freezes it
        $app->forget('config');
        $app['config'] = new Registry($settings);
    }

    /**
     * @return  void
     */
    public function testAnExplicitDsnIsUsedAsItIs(): void
    {
        $this->configure(['mailer_dsn' => 'null://null', 'mailer' => 'smtp']);

        $this->assertSame('null://null', Message::transportDsn());
    }

    /**
     * @return  void
     */
    public function testSendmailIsTheDefault(): void
    {
        $this->configure([]);

        $this->assertSame('sendmail://default', Message::transportDsn());
    }

    /**
     * @return  void
     */
    public function testSmtpCarriesHostPortAndEncodedCredentials(): void
    {
        $this->configure([
            'mailer'   => 'smtp',
            'smtphost' => 'Mail.Example.org',
            'smtpport' => '587',
            'smtpuser' => 'Hub',
            'smtppass' => 'p@ss:w/rd',
        ]);

        $this->assertSame('smtp://Hub:p%40ss%3Aw%2Frd@mail.example.org:587', Message::transportDsn());
    }

    /**
     * @return  void
     */
    public function testMandrillOverSmtp(): void
    {
        $this->configure(['mailer' => 'mandrill+smtp', 'smtpuser' => 'key', 'smtppass' => 'secret']);

        $this->assertSame('mandrill+smtp://key:secret@default', Message::transportDsn());
    }
}
