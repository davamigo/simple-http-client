<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Use davamigo/simple-http-client library
use Davamigo\HttpClient\CurlHttpClient\CurlHttpClient;

// Create the HTTP client
$client = new CurlHttpClient();

// https/api.github.com/ requires a valid user agent
$client->setUserAgent('Mozilla/5.0');

// Create the request
$request = $client->get('https://api.github.com/repos/davamigo/simple-http-client');

// Send the request and get the response
$response = $client->send($request);

// If unsuccessful response...
if (!$response->isSuccessful()) {
    echo '<p>Status: ' . $response->getStatusCode() . '</p>';
    echo '<p>Phrase: ' . $response->getReasonPhrase() . '</p>';
    echo '<p>Body: ' . $response->getBody(true) . '</p>';
    exit(1);
}

$body = $response->getBody(true);
$data = json_decode($body, true);

echo '<p>Repository: <a href="' . $data['html_url'] . '">' . $data['full_name'] . '</a></p>';
echo '<p>Description: ' . $data['description'] . '</p>';
echo '<p>Language: ' . $data['language'] . '</p>';
echo '<p>Owner: <a href="' . $data['owner']['html_url'] . '">' . $data['owner']['login'] . '</a></p>';
