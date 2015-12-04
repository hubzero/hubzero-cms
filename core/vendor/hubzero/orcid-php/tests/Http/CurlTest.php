<?php
/**
 * @package   orcid-php
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Mock' . DIRECTORY_SEPARATOR . 'CurlInterceptor.php';

use Orcid\Http\Curl;
use \Mockery as m;

/**
 * Base curl functionality tests tests
 */
class CurlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Intercept a native function call so we can inspect its arguments
     *
     * @param   closure  $closure  The call we will be intercepting a response from
     * @return  string
     **/
    private function intercept($closure)
    {
        // Start output buffer
        ob_start();

        // Make the calls
        call_user_func($closure);

        // Grag the contents
        $contents = ob_get_contents();

        // Close the buffer
        ob_end_clean();

        // Return the contents
        return $contents;
    }

    /**
     * Gets a mock curl object with the setOpt overloaded
     *
     * @return  object
     **/
    private function curl()
    {
        return m::mock('Orcid\Http\Curl')->makePartial();
    }

    /**
     * Test to make sure we can init a curl instance
     *
     * @return  void
     **/
    public function testNewCurlCallsInitialize()
    {
        $call = function () {
            $curl = new Curl;
        };

        // We'll expect an "INIT" from the init call and a "1" from setting the CURLOPT_RETURNTRANSFER option
        $this->assertRegExp('/INIT/', $this->intercept($call), 'New curl object did not initialize the native curl call');
    }

    /**
     * Test to make sure we close the original curl call when resetting
     *
     * @return  void
     **/
    public function testCurlResetCallsClose()
    {
        $call = function () {
            $curl = new Curl;
            $curl->reset();
        };

        // We'll expect an "INIT" from the init call and a "1" from setting the CURLOPT_RETURNTRANSFER option
        $this->assertRegExp('/CLOSE/', $this->intercept($call), 'Resetting the curl object did not call curl_close');
    }

    /**
     * Test to make sure curl executing makes native curl call
     *
     * @return  void
     **/
    public function testCurlExecutes()
    {
        $call = function () {
            $curl = new Curl;
            $curl->execute();
        };

        // We'll expect an "INIT" from the init call and a "1" from setting the CURLOPT_RETURNTRANSFER option
        $this->assertRegExp('/EXEC/', $this->intercept($call), 'Executing the curl object did not call curl_exec');
    }

    /**
     * Tests that setting an option calls the underlying curl_setopt with the given param
     *
     * @return  void
     **/
    public function testSetOptCallsSetOptWithParameter()
    {
        $call = function () {
            $curl = new Curl;
            $curl->setOpt(CURLOPT_URL, 'http://hubzero.org');
        };

        $this->assertRegExp('/http:\/\/hubzero\.org/', $this->intercept($call), 'The curl_setopt function was not called as expected');
    }

    /**
     * Test to make sure setting a url calls set opt with the given url
     *
     * @return  void
     **/
    public function testSetUrlCallsSetOptWithUrl()
    {
        $this->curl()->shouldReceive('setOpt')->once()->getMock()->setUrl('http://hubzero.org');
    }

    /**
     * Test to make sure setting post fields properly encodes
     *
     * @return  void
     **/
    public function testSetPostFields()
    {
        $this->curl()
             ->shouldReceive('setOpt')
             ->once()
             ->with(CURLOPT_POST, 2)
             ->getMock()
             ->shouldReceive('setOpt')
             ->once()
             ->with(CURLOPT_POSTFIELDS, 'foo=bar&me=you')
             ->getMock()
             ->setPostFields([
                 'foo' => 'bar',
                 'me'  => 'you'
             ]);
    }

    /**
     * Test to make sure setting headers properly formats them
     *
     * @return  void
     **/
    public function testSetHeaders()
    {
        $this->curl()
             ->shouldReceive('setOpt')
             ->once()
             ->with(CURLOPT_HTTPHEADER, ['foo: bar'])
             ->getMock()
             ->shouldReceive('setOpt')
             ->once()
             ->with(CURLOPT_HTTPHEADER, ['foo: bar', 'me: you'])
             ->getMock()
             ->setHeader('foo: bar')
             ->setHeader([
                 'foo' => 'bar',
                 'me'  => 'you'
             ]);
    }
}
