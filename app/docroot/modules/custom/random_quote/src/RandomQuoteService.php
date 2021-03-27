<?php

namespace Drupal\random_quote;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\ClientInterface;

/**
 * Class RandomQuoteService.
 */
class RandomQuoteService implements RandomQuoteInterface {

  public static $baseUri = 'https://quotes15.p.rapidapi.com/quotes/random/';

  protected $httpClient;

  /**
   * Constructs a database object.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The Guzzle HTTP client.
   */
  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  public function getQuote() {
    try {
      $response = $this->httpClient->request(
        'GET',
        self::$baseUri,
        [
          'headers' => [
            'x-rapidapi-host' => 'quotes15.p.rapidapi.com',
            'x-rapidapi-key' => 'b0ada4171cmshad461ecab547c44p11d9cbjsn3b4c38b04b69'
          ],
        ]
      );
      return $response->getBody()->getContents();
    }
    catch (GuzzleException $e) {
      $msg = 'Error: ' . $e->getMessage();
      return $msg;
    }
  }
}
