<?php

namespace Test\Unit\CurlHttpClient;

use Davamigo\HttpClient\CurlHttpClient\CurlHttpRequest;
use Davamigo\HttpClient\CurlHttpClient\CurlProxyInterface;

/**
 * Class CurlHttpRequestTest
 *
 * @package Test\Unit\CurlHttpClient
 * @author davamigo@gmail.com
 * @group test_unit_http_curl_request
 * @group test_unit_http_curl
 * @group test_unit_http
 * @group test_unit
 * @group test
 * @test
 * @codeCoverageIgnore
 */
class CurlHttpRequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var resource */
    private $handler;

    /** @var CurlProxyInterface */
    private $curl;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->handler = tmpfile();

        $this->curl = $this
            ->getMockBuilder('Davamigo\HttpClient\CurlHttpClient\CurlProxyInterface')
            ->setMethods(array('init', 'setopt', 'exec', 'getinfo', 'errno', 'error', 'close'))
            ->getMock();
    }

    /**
     * @test
     */
    public function testGetRequestDataSucceded()
    {
        $request = new CurlHttpRequest($this->handler, "http://www.google.com", $this->curl);

        $this->assertEquals($this->handler, $request->getRequestData());
    }

    /**
     * @test
     */
    public function testSetUriSucceded()
    {
        $testUri = 'http://www.google.com';

        /** @var \PHPUnit_Framework_MockObject_MockObject $curlMock */
        $curlMock = $this->curl;

        $curlMock
            ->expects($this->once())
            ->method('setopt')
            ->with(
                $this->anything(),
                $this->equalTo(CURLOPT_URL),
                $this->equalTo($testUri)
            )
            ->will($this->returnValue(true));

        $request = new CurlHttpRequest($this->handler, null, $this->curl);

        $this->assertNull($request->getUri());
        $this->assertTrue($request->setUri($testUri));
        $this->assertEquals($testUri, $request->getUri());
    }

    /**
     * @test
     */
    public function testSetOptSucceded()
    {
        $testOption = '_an_option_';
        $testValue = '_some_value_';

        /** @var \PHPUnit_Framework_MockObject_MockObject $curlMock */
        $curlMock = $this->curl;

        $curlMock
            ->expects($this->once())
            ->method('setopt')
            ->with(
                $this->anything(),
                $this->equalTo($testOption),
                $this->equalTo($testValue)
            )
            ->will($this->returnValue(true));

        $request = new CurlHttpRequest($this->handler, null, $this->curl);

        $this->assertTrue($request->setOpt($testOption, $testValue));
    }

    /**
     * @test
     */
    public function testSendReturnsValidResponseObjec()
    {
        $rawHeader = "HTTP 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
        $rawContent = "This is the content.";
        $rawResponse = $rawHeader . $rawContent;
        $info = array(
            'http_code'   => 200,
            'header_size' => strlen($rawHeader)
        );

        /** @var \PHPUnit_Framework_MockObject_MockObject $curlMock */
        $curlMock = $this->curl;

        $curlMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue($rawResponse));

        $curlMock
            ->expects($this->once())
            ->method('getinfo')
            ->will($this->returnValue($info));

        $request = new CurlHttpRequest($this->handler, "http://www.google.com", $this->curl);
        $response = $request->send();

        $this->assertInstanceOf('Davamigo\HttpClient\CurlHttpClient\CurlHttpResponse', $response);
        $this->assertEquals($rawHeader, $response->getRawHeaders());
        $this->assertEquals($rawContent, $response->getBody());
    }

    /**
     * @test
     */
    public function testSendWhenCurlExecFailsThrowsAnException()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $curlMock */
        $curlMock = $this->curl;

        $curlMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(false));

        $curlMock
            ->expects($this->once())
            ->method('errno')
            ->will($this->returnValue(101));

        $curlMock
            ->expects($this->once())
            ->method('error')
            ->will($this->returnValue('an error message'));

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $request = new CurlHttpRequest($this->handler, "http://www.google.com", $this->curl);
        $request->send();
    }

    /**
     * @test
     */
    public function testSendCurlGetInfoFailsThrowsAnException()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $curlMock */
        $curlMock = $this->curl;

        $curlMock
            ->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(null));

        $curlMock
            ->expects($this->once())
            ->method('getinfo')
            ->will($this->returnValue(false));

        $curlMock
            ->expects($this->once())
            ->method('errno')
            ->will($this->returnValue(101));

        $curlMock
            ->expects($this->once())
            ->method('error')
            ->will($this->returnValue('an error message'));

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $request = new CurlHttpRequest($this->handler, "http://www.google.com", $this->curl);
        $request->send();
    }
}
