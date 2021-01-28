<?php

namespace Drupal\one_api\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "one_settings_rest_resource",
 *   label = @Translation("One Settings"),
 *   uri_paths = {
 *     "canonical" = "/onedrupal/api/v1/settings"
 *   }
 * )
 */
class OneSettingsRestResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new OneSettingsRestResource object.
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
   * Responds to GET requests.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity object.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get() {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }
    $config = \Drupal::config('one.settings');
    $node_types = \Drupal\node\Entity\NodeType::loadMultiple();
    $responseObj = array();
    $responseObj["types"] = array();
    $user = $this->currentUser;
    foreach ($node_types as $node_type) {
      $node_type_machine_name = $node_type->id();
      $is_enabled = $config->get($node_type_machine_name.'_enable_content_type') ?: 0;
      if($is_enabled){
        $image_field_name = $config->get($node_type_machine_name.'_field_image') ?: '';
        $body_field_name = $config->get($node_type_machine_name.'_field_body') ?: '';
        $remote_image_field_name = $config->get($node_type_machine_name.'_field_remote_image') ?: '';
        $remote_page_field_name = $config->get($node_type_machine_name.'_field_remote_page') ?: '';
        $embedded_video_field_name = $config->get($node_type_machine_name.'_field_video_embed') ?: '';
        $preferred_view_mode = $config->get($node_type_machine_name.'_preferred_view_mode') ?: 'default';
        $sort_weight = $config->get($node_type_machine_name.'_weight_order') ?: 0;

        $taxonomy_fields = $config->get($node_type_machine_name.'_field_taxonomies') ?: '';
        $taxonomy_output_arr = array();
        if(!empty($taxonomy_fields)){
          $field_defs = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $node_type_machine_name);
          foreach($taxonomy_fields as $fldnm){
            foreach ($field_defs as $field_def) {
              $field_name = $field_def->getName();
              if($field_name == $fldnm){
                try{
                  if(!empty($field_def->getSettings()) && isset($field_def->getSettings()['handler'])){
                    $hand = $field_def->getSettings()['handler'];
                    if ($hand == "default:taxonomy_term") {
                      $taxonomy_config = $field_def->get('dependencies')['config'];
                      foreach($taxonomy_config as $cf){
                        if(strpos($cf,"taxonomy.vocabulary") !== FALSE){
                          //$field_settings = entity_get_form_display('node', $node_type_machine_name, 'default')->getComponent($fldnm)['settings'];
                          $taxonomy_output_arr[] = array(
                            "field" => $fldnm,
                            "vocabulary" => str_replace("taxonomy.vocabulary.","",$cf),
                            //"settings" => $field_def,
                            "auto_create" => $field_def->getSettings()['handler_settings']['auto_create'],
                          );
                        }
                      }
                    }
                  }
                }catch (\Exception $e) {
                  // Log the exception to watchdog.
                  watchdog_exception('OneSettingsRestResource', $e);
                }
                break;
              }
            }
          }
        }

        $create_node_access = false;
        if($user->hasPermission("create {$node_type_machine_name} content")){
          $create_node_access = true;
        }
        $responseObj["types"][] = array(
            "access_create" => $create_node_access,
            "node_type" => $node_type_machine_name,
            "preferred_view_mode" => $preferred_view_mode,
            "weight" => $sort_weight,
            "fields" => array(
                "image" => $image_field_name,
                "body" => $body_field_name,
                "remote_image" => $remote_image_field_name,
                "remote_page" => $remote_page_field_name,
                "embedded_video" => $embedded_video_field_name,
                "taxonomies" => $taxonomy_output_arr,
            ),
        );
      }
      usort($responseObj["types"], "one_api_weight_cmp");
      $responseObj["settings"]["taxonomy_menu_vocabulary"] = $config->get('taxonomy_menu_vocabulary') ?: '';
      $responseObj["settings"]["taxonomy_explorer_vocabularies"] = array_filter(array_values($config->get('taxonomy_explorer_vocabularies'))) ?: [];
      /*
       *  "create article content",
          "create page content",
          "edit any article content",
          "edit own article content",
       */
      //$user = \Drupal::currentUser();
      /*$user_roles = $user->getRoles();
      $roles_permissions = user_role_permissions($user_roles);
      $final_permissions_array = array();
      foreach ($roles_permissions as $role_key => $permissions) {
        foreach ($permissions as $permission) {
          $final_permissions_array[$permission] = $permission;
        }
      }
      $responseObj["permissions"] = array_keys($final_permissions_array);*/
    }
    $response = new ResourceResponse($responseObj, 200);
    $response->addCacheableDependency($this->currentUser);
    return $response;
  }
}
