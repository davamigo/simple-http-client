<?php

namespace Test\Unit\CurlHttpClient;

use Davamigo\HttpClient\CurlHttpClient\CurlHttpClient;
use Davamigo\HttpClient\CurlHttpClient\CurlProxyInterface;

/**
 * Unit test for class CurlHttpClient
 *
 * @package Test\Unit\CurlHttpClient
 * @author davamigo@gmail.com
 *
 * @group test_unit_http_curl_client
 * @group test_unit_http_curl
 * @group test_unit_http
 * @group test_unit
 * @group test
 * @test
 * @codeCoverageIgnore
 */
class CurlHttpClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var CurlProxyInterface */
    private $curl;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->curl = $this
            ->getMockBuilder('Davamigo\HttpClient\CurlHttpClient\CurlProxyInterface')
            ->setMethods(array('init', 'setopt', 'exec', 'getinfo', 'errno', 'error', 'close'))
            ->getMock();
    }

    /**
     * @test
     */
    public function testGetClientDataReturnsTheCurlProxy()
    {
        $client = new CurlHttpClient($this->curl);

        $this->assertEquals($this->curl, $client->getClientData());
    }

    /**
     * @test
     */
    public function testGetUserAgentAfterSetUserAgentResturnTheSameResult()
    {
        $userAgent = 'an_user_agent';

        $client = new CurlHttpClient($this->curl);
        $client->setUserAgent($userAgent);

        $this->assertEquals($userAgent, $client->getDefaultUserAgent());
    }

    /**
     * @test
     */
    public function testHttpGetReturnsRequestObject()
    {
        $uri = 'http://www.test.com/';

        /** @var \PHPUnit_Framework_MockObject_MockObject $curlMock */
        $curlMock = $this->curl;

        $curlMock
            ->expects($this->any())
            ->method('setopt')
            ->withConsecutive(
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_URL),
                    $this->equalTo($uri)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_RETURNTRANSFER),
                    $this->equalTo(true)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_POST),
                    $this->equalTo(false)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_HEADER),
                    $this->equalTo(true)
                )
            );

        $client = new CurlHttpClient($this->curl);
        $request = $client->get($uri);

        $this->assertInstanceOf('Davamigo\HttpClient\CurlHttpClient\CurlHttpRequest', $request);
        $this->assertEquals($uri, $request->getUri());
    }

    /**
     * @test
     */
    public function testHttpGetWithOptionsAndHeadersReturnsRequestObject()
    {
        $uri = 'http://www.test.com/';
        $options = array(
            'fail_on_error' => false,
            'max_connects'  => 5,
            'header'        => false
        );
        $header = array(
            'Content-Type'    => 'text/html; charset=UTF-8',
            'Accept-Language' => 'en-US'
        );

        /** @var \PHPUnit_Framework_MockObject_MockObject $curlMock */
        $curlMock = $this->curl;

        $curlMock
            ->expects($this->any())
            ->method('setopt')
            ->withConsecutive(
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_URL),
                    $this->equalTo($uri)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_RETURNTRANSFER),
                    $this->equalTo(true)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_POST),
                    $this->equalTo(false)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_HTTPHEADER),
                    $this->equalTo(array('Content-Type: text/html; charset=UTF-8'))
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_HTTPHEADER),
                    $this->equalTo(array('Accept-Language: en-US'))
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_FAILONERROR),
                    $this->equalTo(false)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_MAXCONNECTS),
                    $this->equalTo(5)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_HEADER),
                    $this->equalTo(true)
                )
            );

        $client = new CurlHttpClient($this->curl);
        $request = $client->get($uri, $header, $options);

        $this->assertInstanceOf('Davamigo\HttpClient\CurlHttpClient\CurlHttpRequest', $request);
        $this->assertEquals($uri, $request->getUri());
    }

    /**
     * @test
     */
    public function testHttpGetWithUserAgentReturnsRequestObject()
    {
        $uri = 'http://www.test.com/';
        $userAgent = 'Mozilla/5.0';

        /** @var \PHPUnit_Framework_MockObject_MockObject $curlMock */
        $curlMock = $this->curl;

        $curlMock
            ->expects($this->any())
            ->method('setopt')
            ->withConsecutive(
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_URL),
                    $this->equalTo($uri)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_RETURNTRANSFER),
                    $this->equalTo(true)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_POST),
                    $this->equalTo(false)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_HEADER),
                    $this->equalTo(true)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_USERAGENT),
                    $this->equalTo($userAgent)
                )
            );

        $client = new CurlHttpClient($this->curl);
        $client->setUserAgent($userAgent);
        $request = $client->get($uri);

        $this->assertInstanceOf('Davamigo\HttpClient\CurlHttpClient\CurlHttpRequest', $request);
        $this->assertEquals($uri, $request->getUri());
    }

    /**
     * @test
     */
    public function testHttpGetWithInvalidOptionsThrowAnException()
    {
        $options = array(
            'unknown_option' => false
        );

        /** @var \PHPUnit_Framework_MockObject_MockObject $curlMock */
        $curlMock = $this->curl;

        $curlMock
            ->expects($this->any())
            ->method('setopt')
            ->withConsecutive(
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_RETURNTRANSFER),
                    $this->equalTo(true)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_POST),
                    $this->equalTo(false)
                )
            );

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $client = new CurlHttpClient($this->curl);
        $client->get(null, null, $options);
    }

    /**
     * @test
     */
    public function testHttpPostReturnsRequestObject()
    {
        $uri = 'http://www.test.com/';
        $postBody = '{"success":true}';

        /** @var \PHPUnit_Framework_MockObject_MockObject $curlMock */
        $curlMock = $this->curl;

        $curlMock
            ->expects($this->any())
            ->method('setopt')
            ->withConsecutive(
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_URL),
                    $this->equalTo($uri)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_RETURNTRANSFER),
                    $this->equalTo(true)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_POST),
                    $this->equalTo(true)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_POSTFIELDS),
                    $this->equalTo($postBody)
                ),
                array(
                    $this->anything(),
                    $this->equalTo(CURLOPT_HEADER),
                    $this->equalTo(true)
                )
            );

        $client = new CurlHttpClient($this->curl);
        $request = $client->post($uri, null, $postBody);

        $this->assertInstanceOf('Davamigo\HttpClient\CurlHttpClient\CurlHttpRequest', $request);
        $this->assertEquals($uri, $request->getUri());
    }

    /**
     * @test
     */
    public function testHttpHeadAlwaysThrowsAnException()
    {
        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $client = new CurlHttpClient($this->curl);
        $client->head();
    }

    /**
     * @test
     */
    public function testHttpDeleteAlwaysThrowsAnException()
    {
        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $client = new CurlHttpClient($this->curl);
        $client->delete();
    }

    /**
     * @test
     */
    public function testHttpPutAlwaysThrowsAnException()
    {
        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $client = new CurlHttpClient($this->curl);
        $client->put();
    }

    /**
     * @test
     */
    public function testHttpPatchAlwaysThrowsAnException()
    {
        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $client = new CurlHttpClient($this->curl);
        $client->patch();
    }

    /**
     * @test
     */
    public function testHttpOptionsAlwaysThrowsAnException()
    {
        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $client = new CurlHttpClient($this->curl);
        $client->options();
    }

    /**
     * @test
     */
    public function testSendSingleRequestReturnResponseAobject()
    {
        $client = new CurlHttpClient($this->curl);
        $request = $client->get();
        $response = $client->send($request);

        $this->assertInstanceOf('Davamigo\HttpClient\CurlHttpClient\CurlHttpResponse', $response);
    }

    /**
     * @test
     */
    public function testSendMultipleRequestsReturnAnArrayOfResponseObjects()
    {
        $client = new CurlHttpClient($this->curl);
        $request1 = $client->get();
        $request2 = $client->post();
        $response = $client->send(array($request1, $request2));

        $this->assertInternalType('array', $response);
        $this->assertInstanceOf('Davamigo\HttpClient\CurlHttpClient\CurlHttpResponse', $response[0]);
        $this->assertInstanceOf('Davamigo\HttpClient\CurlHttpClient\CurlHttpResponse', $response[1]);
    }

    /**
     * @test
     */
    public function testSendInvalidSingleRequestThrowsAnException()
    {
        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $client = new CurlHttpClient($this->curl);
        $client->send('_invalid_type_');
    }

    /**
     * @test
     */
    public function testSendInvalidMultipleRequesstThrowsAnException()
    {
        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $client = new CurlHttpClient($this->curl);
        $client->send(array('_invalid_type_'));
    }
}
