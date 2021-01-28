<?php

namespace Drupal\one_api\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "one_taxonomy_rest_resource",
 *   label = @Translation("One taxonomy rest resource"),
 *   uri_paths = {
 *     "https://www.drupal.org/link-relations/create" = "/onedrupal/api/v1/taxonomy"
 *   }
 * )
 */
class OneTaxonomyRestResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new OneTaxonomyRestResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('one_api'),
      $container->get('current_user')
    );
  }

  /**
   * Responds to POST requests.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity object.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post(array $data) {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }
    $items_array = $data["items"];
    if(empty($items_array)){
      throw new NotFoundHttpException('items parameter missing. items array is required!');
    }
    $return_response = array();
    foreach ($items_array as $item_obj) {
      $vid = $item_obj["vid"];
      if(empty($vid)){
        throw new NotFoundHttpException('vid parameter missing. Vocabulary machine name is required!');
      }
      $tags_string = $item_obj["tags"];
      if(empty($tags_string)){
        throw new NotFoundHttpException('tags parameter missing. coma separated tags are required!');
      }
      $tags_array = explode(",", $tags_string);
      $tags_array = array_values(array_filter(array_map('trim', $tags_array)));

      $item_obj["terms"] = $this->terms_create_multiple($tags_array, $vid);
      $return_response[] = $item_obj;
    }
    // 201 Created responses return the newly created entity in the response
    // body. These responses are not cacheable, so we add no cacheability
    // metadata here.
    return new ModifiedResourceResponse(array("items" => $return_response), 201);
  }
  
  private function terms_create_multiple($tags_array, $vid){
    $return_tids = array();
    foreach ($tags_array as $tag_name) {
      $matched_terms = taxonomy_term_load_multiple_by_name($tag_name, $vid);
      if(!empty($matched_terms)){
        $matched_term = reset($matched_terms);
        $tid = $matched_term->get('tid')->value;
        $return_tids[$tid] = array(
          "name" => $tag_name,
          "tid" => $tid,
        );
      }else{
        // Create term
        if ($this->currentUser->hasPermission('create terms in '.$vid)) {
          $new_term = \Drupal\taxonomy\Entity\Term::create([
              'vid' => $vid,
              'name' => $tag_name,
          ]);
          
          $new_term->enforceIsNew();
          $new_term->save();
          $tid = $new_term->get('tid')->value;
          $return_tids[$tid] = array(
            "name" => $tag_name,
            "tid" => $tid,
          );
        }else if(empty($return_tids)){
          throw new AccessDeniedHttpException();
        }
      }
    }
    return array_values($return_tids);
  }

}
