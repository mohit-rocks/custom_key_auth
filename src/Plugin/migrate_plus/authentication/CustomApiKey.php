<?php

namespace Drupal\custom_key_auth\Plugin\migrate_plus\authentication;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate_plus\AuthenticationPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\key\KeyRepositoryInterface;

/**
 * Provides authentication for the HTTP resource using API KEY in header.
 *
 * @Authentication(
 *   id = "custom_api_key",
 *   title = @Translation("Authenticate using API key passed Header")
 * )
 */
class CustomApiKey extends AuthenticationPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The key repository.
   *
   * @var \Drupal\key\KeyRepositoryInterface;
   */
  protected $keyRepository;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\key\KeyRepositoryInterface $key_repository
   *   The KeyRepository.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, KeyRepositoryInterface $key_repository) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->keyRepository = $key_repository;
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
      $container->get('key.repository')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function getAuthenticationOptions() {
    $storage = $this->configuration['storage'];
    $header = $this->configuration['header'];
    $api_key = $this->configuration['api_key'];

    if ($storage == 'key') {
      $api_key = $this->keyRepository->getKey($api_key)->getKeyValue();
    }

    return [
      'headers' => [
        $header => $api_key,
      ],
    ];
  }

}
