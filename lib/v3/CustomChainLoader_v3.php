<?php

namespace TimberFractalPathsChainLoader;

use \Twig\Loader;
use \Twig\Error\LoaderError;
use \Twig\Source;


/**
 * Twigs Chain Loader, but modified for Timber, based of Drupal Twig_Loader_Chain class
 *
 * @link https://api.drupal.org/api/drupal/vendor%21twig%21twig%21lib%21Twig%21Loader%21Chain.php/class/Twig_Loader_Chain/8.2.x
 *
 */
class FractalPathChainLoader implements \Twig\Loader\LoaderInterface {
  private $hasSourceCache = array();
  protected $loaders = array();

  /**
   * Constructor.
   *
   * @param Twig_LoaderInterface[] $loaders An array of loader instances
   */
  public function __construct(array $loaders = array()) {
    foreach ($loaders as $loader) {
      $this->addLoader($loader);
    }
  }

  /**
   * Adds a loader instance.
   *
   * @param Twig_LoaderInterface $loader A Loader instance
   */
  public function addLoader(\Twig\Loader\LoaderInterface $loader): void {
    $this->loaders[] = $loader;
    $this->hasSourceCache = array();
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceContext(string $name): \Twig\Source {
    $exceptions = array();
    foreach ($this->loaders as $loader) {
      if (!$loader->exists($name)) {
        continue;
      }

      try {
        return $loader->getSourceContext($name);
      } catch (\Twig\Error\LoaderError $e) {
        $exceptions[] = $e->getMessage();
      }
    }
    throw new \Twig\Error\LoaderError(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' (' . implode(', ', $exceptions) . ')' : ''));
  }

  /**
   * {@inheritdoc}
   */
  public function exists(string $name): bool {
    $name = (string) $name;
    if (isset($this->hasSourceCache[$name])) {
      return $this->hasSourceCache[$name];
    }

    foreach ($this->loaders as $loader) {
      if ($loader->exists($name)) {
          return $this->hasSourceCache[$name] = true;
      }
    }

    return $this->hasSourceCache[$name] = false;

    // foreach ($this->loaders as $loader) {
    //   if ($loader instanceof Twig_ExistsLoaderInterface) {
    //     if ($loader->exists($name)) {
    //       return $this->hasSourceCache[$name] = true;
    //     }
    //     continue;
    //   }
    //   if ($loader->exists($name)) {
    //     return $this->hasSourceCache[$name] = true;
    //   }
    //   // try {
    //   //   $loader->getSourceContext($name);
    //   //   return $this->hasSourceCache[$name] = true;
    //   // } catch (Twig_Error_Loader $e) {
    //   // }
    // }
    // return $this->hasSourceCache[$name] = false;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheKey(string $name): string {
    $exceptions = [];
    foreach ($this->loaders as $loader) {
      if (!$loader->exists($name)) {
        continue;
      }

      try {
        return $loader->getCacheKey($name);
      } catch (\Twig\Error\LoaderError $e) {
        $exceptions[] = \get_class($loader).': '.$e->getMessage();
      }
    }
    throw new \Twig\Error\LoaderError(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' (' . implode(', ', $exceptions) . ')' : ''));
  }

  /**
   * {@inheritdoc}
   */
  public function isFresh(string $name, int $time): bool {
    $exceptions = [];
    foreach ($this->loaders as $loader) {
      if (!$loader->exists($name)) {
        continue;
      }

      try {
        return $loader->isFresh($name, $time);
      } catch (Twig_Error_Loader $e) {
        $exceptions[] = get_class($loader) . ': ' . $e->getMessage();
      }
    }
    throw new \Twig\Error\LoaderError(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' (' . implode(', ', $exceptions) . ')' : ''));
  }

}
