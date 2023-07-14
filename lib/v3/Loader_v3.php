<?php

namespace TimberFractalPathsLoader;

use \Twig\Loader;

use \Twig\Error\LoaderError;
use \Twig\Source;

class FractalPathLoader implements \Twig\Loader\LoaderInterface {

  private $namespace;

  private $fractalPath;

  public function __construct() {
    $this->namespace = defined('FRACTAL_HANDLE') ? '@' . FRACTAL_HANDLE : '@patterns';
    $this->fractalPath = defined('FRACTAL_PATH') ? FRACTAL_PATH : get_stylesheet_directory() . '/fractal/patterns';
  }

  /**
   * Gets the source code of a template, given its name.
   *
   * @param  string $name string The name of the template to load
   * @return string The template source code
   *
   */
  public function getSourceContext(string $handle): \Twig\Source {
    return $this->getFractalPath($handle, true);
  }



  /**
   * Gets the cache key to use for the cache for a given template name.
   *
   * @param  string $name string The name of the template to load
   * @return string The cache key
   *
   */
  public function getCacheKey(string $name): string {
    return md5($name);
  }

  public function exists(string $handle) {
    return is_file($this->getFractalPathString($handle));

  }

  /**
   * Returns true if the template is still fresh.
   *
   * @param string    $name The template name
   * @param timestamp $time The last modification time of the cached template
   *
   */
  public function isFresh(string $name, int $time): bool {
    return true;
    // return filemtime($this->findTemplate($name)) <= $time;
  }

  public function getFractalPathString($handle) {
    // Namespace from definition
    $this->namespace = $this->namespace;
    $twig_ext  = '.twig';

    // Return the handle if it does not contain the Fractal namespace or if the extension
    // ends in twig (the path is _not_ a Fractal path)
    if (substr($handle, 0, strlen($this->namespace)) !== $this->namespace || substr_compare( $handle, $twig_ext, -strlen( $twig_ext ) ) === 0) {
      return false;
    }

    $filename = $componentPath = substr($handle, strlen($this->namespace) + 1);
    $subpaths = explode('/', $componentPath);

    if (count($subpaths) > 1) {
      $filename = array_pop($subpaths);
    }

    $path   = [ $this->fractalPath, $componentPath ];
    $path[] = $filename . $twig_ext;
    $path   = array_filter($path);
    $path   = implode(DIRECTORY_SEPARATOR, $path);

    return $path;
  }

  public function getFractalPath($handle, $get_contents = false) {
    $path = $this->getFractalPathString($handle);

    return new \Twig\Source(file_get_contents($path), $handle, $path);
  }

}
