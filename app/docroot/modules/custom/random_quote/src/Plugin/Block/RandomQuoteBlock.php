<?php

namespace Drupal\random_quote\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\random_quote\RandomQuoteService;
/**
 * Provides a 'random quote' block.
 *
 * @Block(
 *  id = "random_quote_block",
 *  admin_label = @Translation("Random Quote"),
 * )
 */
class RandomQuoteBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var AccountInterface $account
   */
  protected $account;

  protected $service;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Session\AccountInterface $account
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountInterface $account, RandomQuoteService $service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->account = $account;
    $this->service = $service;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('random_quote.random'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $random_quote = json_decode(\Drupal::service('random_quote.random')->getQuote());
    $build['#markup'] = '<p>' . $random_quote->content . '</p>';
    return $build;
  }
  /**
   * @return int
   */
  public function getCacheMaxAge() {
    return 0;
  }
}
