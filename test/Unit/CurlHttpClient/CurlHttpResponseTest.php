<?php

namespace Test\Unit\CurlHttpClient;

use Davamigo\HttpClient\CurlHttpClient\CurlHttpResponse;

/**
 * Class CurlHttpResponseTest
 *
 * @package Test\Unit\CurlHttpClient
 * @author davamigo@gmail.com
 * @group test_unit_http_curl_response
 * @group test_unit_http_curl
 * @group test_unit_http
 * @group test_unit
 * @group test
 * @test
 * @codeCoverageIgnore
 */
class CurlHttpResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testConstructResponseWithoutHeaderWorks()
    {
        $rawHeader = null;
        $rawBody = "This is the real document";
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
        $rawHeader = "HTTP/1.1 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
        $rawBody = "This is the real document";
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
        $rawResponse = "HTTP/1.1 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
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
        $rawResponse = "HTTP/1.1 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
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
        $rawResponse = "HTTP 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
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
        $rawResponse = "HTTP/1.1 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
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
        $rawResponse = "HTTP/1.1 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
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
        $rawResponse = "HTTP 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
        $info = array(
            'header_size' => strlen($rawResponse)
        );

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $response = new CurlHttpResponse($rawResponse, $info);
        $response->getProtocolVersion();
    }

    /**
     * @test
     */
    public function testGetStatusCodeFromInfoArrayWorks()
    {
        $rawResponse = "";
        $info = array(
            'http_code' => 200
        );

        $response = new CurlHttpResponse($rawResponse, $info);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testGetStatusCodeFromHeaderArrayWorks()
    {
        $rawResponse = "HTTP 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
        $info = array(
            'header_size' => strlen($rawResponse)
        );

        $response = new CurlHttpResponse($rawResponse, $info);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testGetStatusCodeWhenNoHeaderThrowsAnException()
    {
        $rawResponse = "";
        $info = array();

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $response = new CurlHttpResponse($rawResponse, $info);
        $response->getStatusCode();
    }

    /**
     * @test
     */
    public function testGetStatusCodeWhenInvalidStatusLineThrowsAnException()
    {
        $rawResponse = "HTTP 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
        $info = array(
            'header_size' => 4
        );

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $response = new CurlHttpResponse($rawResponse, $info);
        $response->getStatusCode();
    }

    /**
     * @test
     */
    public function testGetStatusCodeWhenInvalidCodeThrowsAnException()
    {
        $rawResponse = "HTTP 666 OK\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
        $info = array(
            'header_size' => strlen($rawResponse)
        );

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $response = new CurlHttpResponse($rawResponse, $info);
        $response->getStatusCode();
    }

    /**
     * @test
     */
    public function testGetReasonPhraseWithValidCodeReturnAnString()
    {
        $rawResponse = "";
        $info = array(
            'http_code' => 200
        );

        $response = new CurlHttpResponse($rawResponse, $info);
        $reasonPhrase = $response->getReasonPhrase();

        $this->assertInternalType('string', $reasonPhrase);
        $this->assertNotEmpty($reasonPhrase);
    }

    /**
     * @test
     */
    public function testGetReasonPhraseWithUnknownCodeReturnNull()
    {
        $rawResponse = "";
        $info = array(
            'http_code' => 199
        );

        $response = new CurlHttpResponse($rawResponse, $info);

        $this->assertNull($response->getReasonPhrase());
    }

    /**
     * @test
     */
    public function testGetReasonPhraseWithInvalidCodeThrowsAnException()
    {
        $rawResponse = "";
        $info = array(
            'http_code' => 1
        );

        $this->setExpectedException('Davamigo\HttpClient\Domain\HttpException');

        $response = new CurlHttpResponse($rawResponse, $info);
        $response->getReasonPhrase();
    }

    /**
     * @test
     */
    public function testGetHeaderLinesWithDataReturnAnArray()
    {
        $rawResponse = "HTTP 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\nAccept-Language: en-US\r\n\r\n";
        $info = array(
            'header_size' => strlen($rawResponse)
        );

        $response = new CurlHttpResponse($rawResponse, $info);
        $headerLines = $response->getHeaderLines();

        $expected = array(
            'HTTP 200 OK',
            'Content-Type: text/html; charset=UTF-8',
            'Accept-Language: en-US'
        );

        $this->assertEquals($expected, $headerLines);
    }

    /**
     * @test
     */
    public function testGetHeaderLinesWithoutHeaderReturnAnEmptyArray()
    {
        $rawResponse = "";
        $info = array();

        $response = new CurlHttpResponse($rawResponse, $info);
        $headerLines = $response->getHeaderLines();

        $this->assertInternalType('array', $headerLines);
        $this->assertEmpty($headerLines);
    }

    /**
     * @test
     */
    public function testGetAllHeaderReturnsAnArray()
    {
        $rawResponse = "HTTP 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\nAccept-Language: en-US\r\n\r\n";
        $info = array(
            'header_size' => strlen($rawResponse)
        );

        $response = new CurlHttpResponse($rawResponse, $info);
        $header = $response->getHeader();

        $expected = array(
            'Status-Line' => 'HTTP 200 OK',
            'Content-Type' => 'text/html; charset=UTF-8',
            'Accept-Language' => 'en-US'
        );

        $this->assertEquals($expected, $header);
    }

    /**
     * @test
     */
    public function testGetAllHeaderWithDuplicatedKeysReturnsAnArray()
    {
        $rawResponse = "HTTP 200 OK\r\n";
        $rawResponse .= "Content-Type: text/html; charset=UTF-8\r\n";
        $rawResponse .= "Accept-Language: en-US\r\n";
        $rawResponse .= "Accept-Language: en-UK\r\n";
        $rawResponse .= "Accept-Language: es-ES\r\n";
        $rawResponse .= "\r\n";
        $info = array(
            'header_size' => strlen($rawResponse)
        );

        $response = new CurlHttpResponse($rawResponse, $info);
        $header = $response->getHeader();

        $expected = array(
            'Status-Line' => 'HTTP 200 OK',
            'Content-Type' => 'text/html; charset=UTF-8',
            'Accept-Language' => array('en-US', 'en-UK', 'es-ES')
        );

        $this->assertEquals($expected, $header);
    }

    /**
     * @test
     */
    public function testGetHeaderReturnsTheValue()
    {
        $rawResponse = "HTTP 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\nAccept-Language: en-US\r\n\r\n";
        $info = array(
            'header_size' => strlen($rawResponse)
        );

        $response = new CurlHttpResponse($rawResponse, $info);
        $header = $response->getHeader('Accept-Language');

        $expected = 'en-US';

        $this->assertEquals($expected, $header);
    }

    /**
     * @test
     */
    public function testGetHeaderWithDuplicatedKeysReturnsAnArray()
    {
        $rawResponse = "HTTP 200 OK\r\n";
        $rawResponse .= "Content-Type: text/html; charset=UTF-8\r\n";
        $rawResponse .= "Accept-Language: en-US\r\n";
        $rawResponse .= "Accept-Language: en-UK\r\n";
        $rawResponse .= "Accept-Language: es-ES\r\n";
        $rawResponse .= "\r\n";
        $info = array(
            'header_size' => strlen($rawResponse)
        );

        $response = new CurlHttpResponse($rawResponse, $info);
        $header = $response->getHeader('Accept-Language');

        $expected = array('en-US', 'en-UK', 'es-ES');

        $this->assertEquals($expected, $header);
    }

    /**
     * @test
     */
    public function testGetHeaderWithNonExistingKeyReturnsNull()
    {
        $rawResponse = "HTTP 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
        $info = array(
            'header_size' => strlen($rawResponse)
        );

        $response = new CurlHttpResponse($rawResponse, $info);
        $header = $response->getHeader('Accept-Language');

        $this->assertNull($header);
    }

    /**
     * @test
     */
    public function testIsInformationalReturnTrue()
    {
        $info = array(
            'http_code' => 100
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertTrue($response->isInformational());
    }

    /**
     * @test
     */
    public function testIsInformationalReturnFalse()
    {
        $info = array(
            'http_code' => 200
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertFalse($response->isInformational());
    }

    /**
     * @test
     */
    public function testIsInformationalCatchesTheException()
    {
        $info = array();

        $response = new CurlHttpResponse("", $info);

        $this->assertFalse($response->isInformational());
    }

    /**
     * @test
     */
    public function testIsSuccessfulReturnTrue()
    {
        $info = array(
            'http_code' => 200
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertTrue($response->isSuccessful());
    }

    /**
     * @test
     */
    public function testIsSuccessfulReturnFalse()
    {
        $info = array(
            'http_code' => 300
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertFalse($response->isSuccessful());
    }

    /**
     * @test
     */
    public function testIsSuccessfulCatchesTheException()
    {
        $info = array();

        $response = new CurlHttpResponse("", $info);

        $this->assertFalse($response->isSuccessful());
    }

    /**
     * @test
     */
    public function testIsRedirectReturnTrue()
    {
        $info = array(
            'http_code' => 300
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertTrue($response->isRedirect());
    }

    /**
     * @test
     */
    public function testIsRedirectReturnFalse()
    {
        $info = array(
            'http_code' => 400
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertFalse($response->isRedirect());
    }

    /**
     * @test
     */
    public function testIsRedirectCatchesTheException()
    {
        $info = array();

        $response = new CurlHttpResponse("", $info);

        $this->assertFalse($response->isRedirect());
    }

    /**
     * @test
     */
    public function testIsClientErrorReturnTrue()
    {
        $info = array(
            'http_code' => 400
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertTrue($response->isClientError());
    }

    /**
     * @test
     */
    public function testIsClientErrorReturnFalse()
    {
        $info = array(
            'http_code' => 500
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertFalse($response->isClientError());
    }

    /**
     * @test
     */
    public function testIsClientErrorCatchesTheException()
    {
        $info = array();

        $response = new CurlHttpResponse("", $info);

        $this->assertFalse($response->isClientError());
    }

    /**
     * @test
     */
    public function testIsServerErrorReturnTrue()
    {
        $info = array(
            'http_code' => 500
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertTrue($response->isServerError());
    }

    /**
     * @test
     */
    public function testIsServerErrorReturnFalse()
    {
        $info = array(
            'http_code' => 100
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertFalse($response->isServerError());
    }

    /**
     * @test
     */
    public function testIsServerErrorCatchesTheException()
    {
        $info = array();

        $response = new CurlHttpResponse("", $info);

        $this->assertFalse($response->isServerError());
    }

    /**
     * @test
     */
    public function testIsErrorReturnTrue()
    {
        $info = array(
            'http_code' => 500
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertTrue($response->isError());
    }

    /**
     * @test
     */
    public function testIsErrorReturnFalse()
    {
        $info = array(
            'http_code' => 100
        );

        $response = new CurlHttpResponse("", $info);

        $this->assertFalse($response->isError());
    }
}
