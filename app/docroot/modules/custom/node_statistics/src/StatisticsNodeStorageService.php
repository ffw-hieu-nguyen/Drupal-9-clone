<?php

namespace Drupal\cms_statistics;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;
use Drupal\redis\ClientFactory;
use Drupal\redis\RedisPrefixTrait;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class StatisticsRedisStorageService.
 */
class StatisticsNodeStorageService implements StatisticsNodeStorageInterface {

  use RedisPrefixTrait;

  /**
   * Drupal\redis\ClientFactory definition.
   *
   * @var \Drupal\redis\ClientFactory
   */
  protected $clientFactory;

  /**
   * Redis client.
   *
   * @var \Drupal\redis\ClientFactory
   */
  protected $client;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The redirect.destination service.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   *   The current request.
   */
  protected $currentRequest;

  /**
   * The TimeInterface.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * Key prefix storage.
   *
   * @var string
   */
  protected $sessionID;

  /**
   * Constructs a new StatisticsRedisStorageService object.
   *
   * @param \Drupal\redis\ClientFactory $client_factory
   *   The redis client.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The TimeInterface.
   */
  public function __construct(ClientFactory $client_factory, Connection $connection, RequestStack $requestStack, TimeInterface $time) {
    $this->clientFactory = $client_factory;
    $this->connection = $connection;
    $this->currentRequest = $requestStack->getCurrentRequest();
    $this->time = $time;
    $this->client = $this->clientFactory->getClient();
    $this->sessionID = $this->setSessionId();
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentKeys() {
    return $this->client->keys('statistics:node:*');
  }

  /**
   * {@inheritdoc}
   */
  public function recordView($id) {
    $key = "statistics:node:{$id}:{$this->sessionID}:{$this->time->getRequestTime()}";
    $this->client->incr($key);
  }

  /**
   * {@inheritdoc}
   */
  public function fetchViews(array $keys) {
    $counter = [];
    foreach ($keys as $key) {
      $list = explode(':', $key);
      $nid = $list[2];
      $current = isset($counter[$nid]) ? $counter[$nid] : 0;
      $counter[$nid] = $current + $this->client->get($key);
    }
    return $counter;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteRecords(array $keys) {
      $this->client->unlink($keys);
  }

  /**
   * {@inheritdoc}
   */
  public function syncDatabase($counter) {
    foreach ($counter as $nid => $number) {
      $status = (bool) $this->connection->merge('node_counter')
        ->key('nid', $nid)
        ->fields([
          'daycount' => 1,
          'totalcount' => 1,
          'timestamp' => $this->time->getRequestTime(),
        ])
        ->expression('daycount', "daycount + {$number}")
        ->expression('totalcount', "totalcount + {$number}")
        ->execute();

      if (!$status) {
        \Drupal::logger('Sync Redis')->error("{$nid}:{$counter}");
      }
    }
  }

  /**
   * Set key session from user ip and use agent.
   *
   * @return string
   *   String hash use ip and use agent.
   */
  protected function setSessionId() {
    $ip = $this->currentRequest->getClientIp();
    $user_agent = $this->currentRequest->headers->get('user-agent');
    return md5($ip . $user_agent);
  }

}
