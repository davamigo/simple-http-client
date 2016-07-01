<?php

namespace Test\Unit\CurlHttpClient;

use Davamigo\HttpClient\CurlHttpClient\CurlHttpResponse;

/**
 * Class CurlHttpRequestTest
 * @package Test\Unit\CurlHttpClient
 * @group test_unit_http_curl_request
 * @test
 */
class CurlHttpResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testConstructResponseWithoutHeaderWorks()
    {
        $rawHeader = null;
        $rawBody = "This is the real document\n\n";
        $rawResponse = $rawHeader . $rawBody;

        $response = new CurlHttpResponse($rawResponse, null);

        $this->assertEquals($rawHeader, $response->getRawHeaders());
        $this->assertEquals($rawBody, $response->getBody());
        $this->assertEquals($rawResponse, $response->getResponseData());
        $this->assertEquals($rawResponse, $response->getMessage());
    }

    /**
     * @test
     */
    public function testConstructResponseWithHeaderWorks()
    {
        $rawHeader = "HTTP/1.1 200 OK\nContent-Type: text/html; charset=UTF-8\n\n";
        $rawBody = "This is the real document\n\n";
        $rawResponse = $rawHeader . $rawBody;

        $response = new CurlHttpResponse($rawResponse, array( 'header_size' => strlen($rawHeader) ));

        $this->assertEquals($rawHeader, $response->getRawHeaders());
        $this->assertEquals($rawBody, $response->getBody());
        $this->assertEquals($rawResponse, $response->getResponseData());
        $this->assertEquals($rawResponse, $response->getMessage());
    }

    /**
     * @test
     */
    public function testGetInfoWithoutKeyReturnsAnArray()
    {
        $info = array(
            'url'                       => 101,
            'http_code'                 => 102,
            'filetime'                  => 103,
            'total_time'                => 104,
            'namelookup_time'           => 105,
            'connect_time'              => 106,
            'pretransfer_time'          => 107,
            'starttransfer_time'        => 108,
            'redirect_count'            => 109,
            'redirect_time'             => 110,
            'size_upload'               => 111,
            'size_download'             => 112,
            'speed_download'            => 113,
            'speed_upload'              => 114,
            'header_size'               => 115,
            'certinfo'                  => 116,
            'request_size'              => 117,
            'ssl_verify_result'         => 118,
            'download_content_length'   => 119,
            'upload_content_length'     => 120,
            'content_type'              => 121,
            'redirect_url'              => 122
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertEquals($info, $response->getInfo());
    }

    /**
     * @test
     */
    public function testGetInfoWithStringKeyReturnsTheValue()
    {
        $info = array(
            'url'                       => 101,
            'http_code'                 => 102,
            'filetime'                  => 103,
            'total_time'                => 104,
            'namelookup_time'           => 105,
            'connect_time'              => 106,
            'pretransfer_time'          => 107,
            'starttransfer_time'        => 108,
            'redirect_count'            => 109,
            'redirect_time'             => 110,
            'size_upload'               => 111,
            'size_download'             => 112,
            'speed_download'            => 113,
            'speed_upload'              => 114,
            'header_size'               => 115,
            'certinfo'                  => 116,
            'request_size'              => 117,
            'ssl_verify_result'         => 118,
            'download_content_length'   => 119,
            'upload_content_length'     => 120,
            'content_type'              => 121,
            'redirect_url'              => 122
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertEquals($info['speed_upload'], $response->getInfo('speed_upload'));
    }

    /**
     * @test
     */
    public function testGetInfoWithIntegerKeyReturnsTheValue()
    {
        $info = array(
            'url'                       => 101,
            'http_code'                 => 102,
            'filetime'                  => 103,
            'total_time'                => 104,
            'namelookup_time'           => 105,
            'connect_time'              => 106,
            'pretransfer_time'          => 107,
            'starttransfer_time'        => 108,
            'redirect_count'            => 109,
            'redirect_time'             => 110,
            'size_upload'               => 111,
            'size_download'             => 112,
            'speed_download'            => 113,
            'speed_upload'              => 114,
            'header_size'               => 115,
            'certinfo'                  => 116,
            'request_size'              => 117,
            'ssl_verify_result'         => 118,
            'download_content_length'   => 119,
            'upload_content_length'     => 120,
            'content_type'              => 121,
            'redirect_url'              => 122
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertEquals($info['speed_upload'], $response->getInfo(CURLINFO_SPEED_UPLOAD));
    }

    /**
     * @test
     */
    public function testGetInfoWithInvalidKeyReturnsNull()
    {
        $info = array();

        $response = new CurlHttpResponse("", $info);

        $this->assertEquals(null, $response->getInfo(CURLINFO_SPEED_UPLOAD));
    }

    /**
     * @test
     */
    public function testGetProtocolWithValidHeaderReturnsTheProtocol()
    {
        $rawResponse = "HTTP/1.1 200 OK\nContent-Type: text/html; charset=UTF-8\n\n";
        $info = array(
            'header_size' => strlen($rawResponse)
        );

        $response = new CurlHttpResponse($rawResponse, $info);

        $this->assertEquals('HTTP', $response->getProtocol());
    }

    /**
     * @test
     */
    public function testGetProtocolWithoutHeaderThrownsAnException()
    {
        $rawResponse = "";
        $info = array();

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $response = new CurlHttpResponse($rawResponse, $info);
        $response->getProtocol();
    }

    /**
     * @test
     */
    public function testGetProtocolWithInvalidHeaderThrownsAnException()
    {
        $rawResponse = "HTTP/1.1 200 OK\nContent-Type: text/html; charset=UTF-8\n\n";
        $info = array(
            'header_size' => 4
        );

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $response = new CurlHttpResponse($rawResponse, $info);
        $response->getProtocol();
    }

    /**
     * @test
     */
    public function testGetProtocolWithInvalid2HeaderThrownsAnException()
    {
        $rawResponse = "HTTP 200 OK\nContent-Type: text/html; charset=UTF-8\n\n";
        $info = array(
            'header_size' => strlen($rawResponse)
        );

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $response = new CurlHttpResponse($rawResponse, $info);
        $response->getProtocol();
    }

    /**
     * @test
     */
    public function testGetProtocolVersionWithValidHeaderReturnsTheProtocol()
    {
        $rawResponse = "HTTP/1.1 200 OK\nContent-Type: text/html; charset=UTF-8\n\n";
        $info = array(
            'header_size' => strlen($rawResponse)
        );

        $response = new CurlHttpResponse($rawResponse, $info);

        $this->assertEquals('1.1', $response->getProtocolVersion());
    }

    /**
     * @test
     */
    public function testGetProtocolVersionWithoutHeaderThrownsAnException()
    {
        $rawResponse = "";
        $info = array();

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $response = new CurlHttpResponse($rawResponse, $info);
        $response->getProtocolVersion();
    }

    /**
     * @test
     */
    public function testGetProtocolVersionWithInvalidHeaderThrownsAnException()
    {
        $rawResponse = "HTTP/1.1 200 OK\nContent-Type: text/html; charset=UTF-8\n\n";
        $info = array(
            'header_size' => 4
        );

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $response = new CurlHttpResponse($rawResponse, $info);
        $response->getProtocolVersion();
    }

    /**
     * @test
     */
    public function testGetProtocolVersionWithInvalid2HeaderThrownsAnException()
    {
        $rawResponse = "HTTP 200 OK\nContent-Type: text/html; charset=UTF-8\n\n";
        $info = array(
            'header_size' => strlen($rawResponse)
        );

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $response = new CurlHttpResponse($rawResponse, $info);
        $response->getProtocolVersion();
    }
}
