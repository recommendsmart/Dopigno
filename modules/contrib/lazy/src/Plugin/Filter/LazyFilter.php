<?php

namespace Drupal\lazy\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

/**
 * Provides a filter to lazy-load images.
 *
 * @Filter(
 *   id = "lazy_filter",
 *   title = @Translation("Lazy-load images and IFRAMEs via bLazy"),
 *   description = @Translation("<a href=':url'>Configure options</a>", arguments = {":url" = "/admin/config/content/lazy"}),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE,
 *   weight = 20
 * )
 */
class LazyFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $config = \Drupal::config('lazy.settings')->get();
    $opt_skipClass = $config['skipClass'];
    $opt_selector = ltrim($config['selector'], '.');
    $opt_tags = $config['alter_tag'];
    $opt_src = ($config['src'] !== 'src') ? $config['src'] : 'data-filterlazy-src';
    $opt_placeholderSrc = $config['placeholderSrc'];

    $result = new FilterProcessResult($text);
    $html_dom = Html::load($text);

    $pages = $config['disabled_paths'];
    $path_matches = lazy_disabled_by_path($pages);

    if (!$path_matches) {
      foreach ($opt_tags as $tag => $status) {
        $matches = $html_dom->getElementsByTagName($tag);
        foreach ($matches as $element) {
          $classes = $element->getAttribute('class');
          $classes = ($classes !== '') ? explode(' ', $classes) : [];
          $parent_classes = $element->parentNode->getAttribute('class');
          $parent_classes = ($parent_classes !== '') ? explode(' ', $parent_classes) : [];
          if (empty($opt_tags[$tag])) {
            // If the `tag` is not enabled remove the bLazy selector class.
            if (($key = array_search($opt_selector, $classes, FALSE)) !== FALSE) {
              unset($classes[$key]);
              $element->setAttribute('class', implode(' ', $classes));
              if (empty($classes)) {
                $element->removeAttribute('class');
              }
            }
          }
          else {
            // `tag` is enabled. Make sure skipClass is not set before
            // proceeding.
            if (!in_array($opt_skipClass, $classes, FALSE) && !in_array($opt_skipClass, $parent_classes, FALSE)) {
              $classes[] = $opt_selector;
              $classes = array_unique($classes);
              $element->setAttribute('class', implode(' ', $classes));

              $src = $element->getAttribute('src');
              $element->removeAttribute('src');

              $element->setAttribute($opt_src, $src);
              $element->setAttribute('src', $opt_placeholderSrc);
            }
          }
        }
      }
    }
    $result->setProcessedText(Html::serialize($html_dom));

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    $settings = \Drupal::config('lazy.settings');
    $tags = $settings->get('alter_tag');
    $skip = $settings->get('skipClass');
    $options = ['%img' => '<img>', '%iframe' => '<iframe>'];

    $skip_help = t('If you want certain elements skip lazy-loading, add <code>%class</code> class name.', ['%class' => $skip]);

    if (!empty($tags)) {
      if ($tags['img'] && $tags['iframe']) {
        return t('Lazy-loading is enabled for both %img and %iframe tags.', $options) . ' ' . $skip_help;
      }
      elseif ($tags['img']) {
        return t('Lazy-loading is enabled for %img tags.', $options) . ' ' . $skip_help;
      }
      elseif ($tags['iframe']) {
        return t('Lazy-loading is enabled for %iframe tags.', $options) . ' ' . $skip_help;
      }
    }

    return t('Lazy-loading is not enabled.');
  }

}
