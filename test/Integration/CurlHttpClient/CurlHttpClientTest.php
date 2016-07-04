<?php

namespace Test\Integration\CurlHttpClient;

use Davamigo\HttpClient\CurlHttpClient\CurlHttpClient;

/**
 * Integration test for class CurlHttpClient
 *
 * @package Test\Integration\CurlHttpClient
 * @author davamigo@gmail.com
 *
 * @group test_integration_http_curl_client
 * @group test_integration_http_curl
 * @group test_integration_http
 * @group test_integration
 * @group test
 * @test
 * @codeCoverageIgnore
 */
class CurlHttpClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testSimpleGetSucceeded()
    {
        $uri = 'http://www.example.com/';

        $client = new CurlHttpClient();
        $request = $client->get($uri);

        $result = $client->send($request);
        $this->assertTrue($result->isSuccessful());
    }

    /**
     * @test
     */
    public function testSimpleHeadSucceeded()
    {
        $uri = 'http://www.example.com/';

        $client = new CurlHttpClient();
        $request = $client->head($uri);

        $result = $client->send($request);
        $this->assertTrue($result->isSuccessful());
    }

    /**
     * @test
     */
    public function testSimplePostSucceeded()
    {
        $uri = 'http://www.example.com/';

        $client = new CurlHttpClient();
        $request = $client->post($uri, null, 'some_data');

        $result = $client->send($request);
        $this->assertTrue($result->isSuccessful());
    }

    /**
     * @test
     */
    public function testSimplePutSucceededWithClientError()
    {
        $uri = 'http://www.example.com/';

        $client = new CurlHttpClient();
        $request = $client->put($uri, null, 'some_data');

        $result = $client->send($request);
        $this->assertTrue($result->isClientError());
        $this->assertEquals(405, $result->getStatusCode());
    }

    /**
     * @test
     */
    public function testSimplePatchSucceededWithClientError()
    {
        $uri = 'http://www.example.com/';

        $client = new CurlHttpClient();
        $request = $client->patch($uri, null, 'some_data');

        $result = $client->send($request);
        $this->assertTrue($result->isClientError());
        $this->assertEquals(405, $result->getStatusCode());
    }

    /**
     * @test
     */
    public function testSimpleDeleteSucceededWithClientError()
    {
        $uri = 'http://www.example.com/';

        $client = new CurlHttpClient();
        $request = $client->delete($uri);

        $result = $client->send($request);
        $this->assertTrue($result->isClientError());
        $this->assertEquals(405, $result->getStatusCode());
    }

    /**
     * @test
     */
    public function testSimpleOptionsSucceeded()
    {
        $uri = 'http://www.example.com/';

        $client = new CurlHttpClient();
        $request = $client->options($uri);

        $result = $client->send($request);
        $this->assertTrue($result->isSuccessful());
    }

    /**
     * @test
     */
    public function testMultiRequestSucceeded()
    {
        $uri = 'http://www.example.com/';

        $client = new CurlHttpClient();
        $requests = array(
            $client->head($uri),
            $client->get($uri),
            $client->post($uri)
        );

        $results = $client->send($requests);
        foreach ($results as $result) {
            $this->assertTrue($result->isSuccessful());

        }
    }

    /**
     * @test
     */
    public function testInvalidUrlFails()
    {
        $uri = 'http://www.example.com/fail';

        $client = new CurlHttpClient();
        $request = $client->get($uri);

        $result = $client->send($request);
        $this->assertTrue($result->isClientError());
    }

    /**
     * @test
     */
    public function testInvalidUrlThrowsAnException()
    {
        $uri = 'http://_thisisanivalidurl_.com';

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $client = new CurlHttpClient();
        $request = $client->get($uri);
        $client->send($request);
    }
}
