<?php

namespace Drupal\one\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 */
class SettingsForm extends ConfigFormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'one.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('one.settings');

    $node_types = \Drupal\node\Entity\NodeType::loadMultiple();
    $view_mode_options = array(
      "default" => "Default View",
      "chart_bar_simple" => "Simple Bar Chart",
      "image_gallery_simple" => "Simple Image Gallery",
    );
    foreach ($node_types as $node_type) {
      $node_type_machine_name = $node_type->id();
      $field_defs = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $node_type_machine_name);
      $image_fields = array();
      $text_fields = array();
      $link_fields = array();
      $term_ref_fields = array();
      $video_embed_fields = array();
      foreach ($field_defs as $field_def) {
        $field_type = $field_def->getType();
        $field_name = $field_def->getName();
        if($field_type == "image"){
          $image_fields[$field_name] = $field_name;
        }elseif($field_type == "text_with_summary"){
          $text_fields[$field_name] = $field_name;
        }elseif($field_type == "link"){
          $link_fields[$field_name] = $field_name;
        }elseif($field_type == "video_embed_field"){
          $video_embed_fields[$field_name] = $field_name;
        }elseif($field_type == "entity_reference"){
          $hand = $field_def->getSettings()['handler'];
          if ($hand == "default:taxonomy_term") {
            $term_ref_fields[$field_name] = $field_name;
          }
        }else{
          // entity_reference
          //dsm("$field_type $field_name");
        }
      }
      $form['content_type_'.$node_type_machine_name] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Content Type '.$node_type->label()),
        '#description' => $this->t('Settings for Content Type'),
        '#weight' => '0',
      ];

      if(!empty($image_fields)){
        $form['content_type_'.$node_type_machine_name][$node_type_machine_name.'_field_image'] = [
          '#type' => 'select',
          '#empty_option' => 'None',
          '#title' => $this->t('Image'),
          '#description' => $this->t('Select primary Image field'),
          '#options' => $image_fields,
          '#default_value' => $config->get($node_type_machine_name.'_field_image') ?: '',
          '#weight' => '0',
        ];
      }
      if(!empty($text_fields)){
        $form['content_type_'.$node_type_machine_name][$node_type_machine_name.'_field_body'] = [
          '#type' => 'select',
          '#empty_option' => 'None',
          '#title' => $this->t('HTML body'),
          '#description' => $this->t('Select primary HTML long body field'),
          '#options' => $text_fields,
          '#default_value' => $config->get($node_type_machine_name.'_field_body') ?: '',
          '#weight' => '0',
        ];
      }
      if(!empty($term_ref_fields)){
        $form['content_type_'.$node_type_machine_name][$node_type_machine_name.'_field_taxonomies'] = [
          '#type' => 'checkboxes',
          '#title' => $this->t('Taxonomy Entity reference fields'),
          '#description' => $this->t('Select multiple term reference fields, for example can be used in search as facet filters'),
          '#options' => $term_ref_fields,
          '#default_value' => $config->get($node_type_machine_name.'_field_taxonomies') ?: '',
          '#weight' => '0',
        ];
      }
      if(!empty($video_embed_fields)){
        $form['content_type_'.$node_type_machine_name][$node_type_machine_name.'_field_video_embed'] = [
          '#type' => 'select',
          '#empty_option' => 'None',
          '#title' => $this->t('Embedded Video'),
          '#description' => $this->t('Select a link field to store primary Embedded Video URL like youtube URL'),
          '#options' => $video_embed_fields,
          '#default_value' => $config->get($node_type_machine_name.'_field_video_embed') ?: '',
          '#weight' => '0',
        ];
      }
      if(!empty($link_fields)){
        $form['content_type_'.$node_type_machine_name][$node_type_machine_name.'_field_remote_image'] = [
          '#type' => 'select',
          '#empty_option' => 'None',
          '#title' => $this->t('Remote Image URL'),
          '#description' => $this->t('Select a link field to store primary image URL hosted externally'),
          '#options' => $link_fields,
          '#default_value' => $config->get($node_type_machine_name.'_field_remote_image') ?: '',
          '#weight' => '0',
        ];
        $form['content_type_'.$node_type_machine_name][$node_type_machine_name.'_field_remote_page'] = [
          '#type' => 'select',
          '#empty_option' => 'None',
          '#title' => $this->t('Remote page URL'),
          '#description' => $this->t('Select a link field to store primary externally hosted webpage URL, For example link URL to read more news on an external site'),
          '#options' => $link_fields,
          '#default_value' => $config->get($node_type_machine_name.'_field_remote_page') ?: '',
          '#weight' => '0',
        ];
      }
      $form['content_type_'.$node_type_machine_name][$node_type_machine_name.'_preferred_view_mode'] = [
          '#type' => 'radios',
          '#title' => $this->t('Preferred View Mode'),
          '#description' => $this->t('How the list of this content type should be displayed?'),
          '#options' => $view_mode_options,
          '#default_value' => $config->get($node_type_machine_name.'_preferred_view_mode') ?: 'default',
          '#weight' => '0',
      ];
      $form['content_type_'.$node_type_machine_name][$node_type_machine_name.'_weight_order'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Weight'),
          '#description' => $this->t('Sort Order of content type'),
          '#size' => 2,
          '#maxlength' => 2,
          '#default_value' => $config->get($node_type_machine_name.'_weight_order') ?: 0,
          '#weight' => '0',
      ];
      $form['content_type_'.$node_type_machine_name][$node_type_machine_name.'_enable_content_type'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Enable Content Type'),
        '#description' => $this->t('Select to enable this Content Type'),
        '#default_value' => $config->get($node_type_machine_name.'_enable_content_type') ?: 0,
        '#weight' => '0',
      ];
    }
    $vocabularies = \Drupal\taxonomy\Entity\Vocabulary::loadMultiple();
    $vocabulary_options = array();
    foreach ($vocabularies as $vocabulary) {
	    $vocabulary_options[$vocabulary->id()] = $vocabulary->id();
    }
    if(!empty($vocabulary_options)){
	  $form['gbl']['taxonomy_menu_vocabulary'] = [
	    '#type' => 'select',
	    '#empty_option' => 'None',
	    '#title' => $this->t('Taxonomy Menu'),
	    '#description' => $this->t('Choose a Vocabulary that should serve as Site main navigation menu'),
	    '#options' => $vocabulary_options,
	    '#default_value' => $config->get('taxonomy_menu_vocabulary') ?: '',
	    '#weight' => '0',
      ];
	  $form['gbl']['taxonomy_explorer_vocabularies'] = [
          '#type' => 'checkboxes',
          '#title' => $this->t('Taxonomy explorer vocabularies'),
          '#description' => $this->t('To build sitemap'),
          '#options' => $vocabulary_options,
          '#default_value' => $config->get('taxonomy_explorer_vocabularies') ?: '',
          '#weight' => '0',
      ];
	}

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = $this->config('one.settings');
    $node_types = \Drupal\node\Entity\NodeType::loadMultiple();
    foreach ($node_types as $node_type) {
      $node_type_machine_name = $node_type->id();

      $config->set($node_type_machine_name.'_enable_content_type', $values[$node_type_machine_name.'_enable_content_type']);
      if(isset($values[$node_type_machine_name.'_field_body'])){
        $config->set($node_type_machine_name.'_field_body', $values[$node_type_machine_name.'_field_body']);
      }
      if(isset($values[$node_type_machine_name.'_field_image'])){
        $config->set($node_type_machine_name.'_field_image', $values[$node_type_machine_name.'_field_image']);
      }
      $field_remote_image = isset($values[$node_type_machine_name.'_field_remote_image']) ? $values[$node_type_machine_name.'_field_remote_image'] : '';
      $config->set($node_type_machine_name.'_field_remote_image', $field_remote_image);
      $field_remote_page = isset($values[$node_type_machine_name.'_field_remote_page']) ? $values[$node_type_machine_name.'_field_remote_page'] : '';
      $config->set($node_type_machine_name.'_field_remote_page', $field_remote_page);
      $field_remote_video = isset($values[$node_type_machine_name.'_field_video_embed']) ? $values[$node_type_machine_name.'_field_video_embed'] : '';
      $config->set($node_type_machine_name.'_field_video_embed', $field_remote_video);
      if(isset($values[$node_type_machine_name.'_field_taxonomies'])){
        $config->set($node_type_machine_name.'_field_taxonomies', $values[$node_type_machine_name.'_field_taxonomies']);
      }
      if(isset($values[$node_type_machine_name.'_preferred_view_mode'])){
        $config->set($node_type_machine_name.'_preferred_view_mode', $values[$node_type_machine_name.'_preferred_view_mode']);
      }
      if(isset($values[$node_type_machine_name.'_weight_order'])){
        $config->set($node_type_machine_name.'_weight_order', $values[$node_type_machine_name.'_weight_order']);
      }
    }
    if(isset($values['taxonomy_menu_vocabulary'])){
        $config->set('taxonomy_menu_vocabulary', $values['taxonomy_menu_vocabulary']);
    }
    if(isset($values['taxonomy_explorer_vocabularies'])){
        $config->set('taxonomy_explorer_vocabularies', $values['taxonomy_explorer_vocabularies']);
    }
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
