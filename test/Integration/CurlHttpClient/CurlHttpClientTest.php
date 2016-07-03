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
    public function testSimplePostSucceeded()
    {
        $uri = 'http://www.example.com/';

        $client = new CurlHttpClient();
        $request = $client->post($uri);

        $result = $client->send($request);
        $this->assertTrue($result->isSuccessful());
    }

    /**
     * @test
     */
    public function testMuntiRequestSucceeded()
    {
        $uri = 'http://www.example.com/';

        $client = new CurlHttpClient();
        $requests = array(
            $client->get($uri),
            $client->post($uri),
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
}
