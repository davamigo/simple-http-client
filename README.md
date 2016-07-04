davamigo/simple-http-client
===========================

This is a simple PHP HTTP client library implemented with CURL that helps making HTTP requests.

Install
-------

The recommended way to add this library to your PHP projects is through [composer](https://getcomposer.org/).

Add the package and the repository to your **composer.json** file:
```javascript
{
    "require": {
        "davamigo/simple-http-client": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:davamigo/simple-http-client.git"
        }
    ]
}
```

Then update the dependencies:
```bash
$ php composer.phar update
```

Example
-------

See: [web/index.php](web/index.php):

```php
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

// Get the body of the response as string
$body = $response->getBody(true);

// Show the JSON data read from the web service
$data = json_decode($body, true);
echo '<p>Repository: <a href="' . $data['html_url'] . '">' . $data['full_name'] . '</a></p>';
echo '<p>Description: ' . $data['description'] . '</p>';
echo '<p>Language: ' . $data['language'] . '</p>';
echo '<p>Owner: <a href="' . $data['owner']['html_url'] . '">' . $data['owner']['login'] . '</a></p>';

```
