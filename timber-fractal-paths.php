<?php

/*
Plugin Name: Timber Custom Loader
Description: This plugin adds a custom Twig loader to Timber
Version: 1.0.0
*/

class TimberCustomLoader {

  private $twig_version;
  private $fractal_path;
  private $fractal_handle;

  public function __construct() {
    $this->version        = '2.0.0';
    $this->fractal_path   = defined('FRACTAL_HANDLE') ? FRACTAL_HANDLE : 'fractal';
    $this->fractal_handle = defined('FRACTAL_PATH') ? FRACTAL_PATH : get_stylesheet_directory() . '/fractal/components';

    if (!is_admin()) {
      add_action('plugins_loaded', [$this, 'plugin_checks']);
    }
  }

  public function plugin_checks() {
    add_filter('timber/loader/loader', [$this, 'add_loader']);
  }

  public function add_loader($loader) {

    $this->twig_version = false;

    if ( class_exists('\Twig\Environment') ) {
      $this->twig_version =  Twig\Environment::VERSION;
    } elseif ( class_exists('Twig_Environment') ) {
      $this->twig_version = Twig_Environment::VERSION;
    }

    // Add namespace to Timber Loader
    $loader->addPath($this->fractal_path, $this->fractal_handle);
    $load2 = $loader;

    if ( version_compare($this->twig_version, '2.7.0', '>=') ) {
      require_once('lib/v2/Loader_v2.php');
      require_once('lib/v2/CustomChainLoader_v2.php');
    } else {
      require_once('lib/v1/Loader_v1.php');
      require_once('lib/v1/CustomChainLoader_v1.php');
    }

    // Create a custom loader to load Fractal style include:
    // `@patterns/component-name`, not `@patterns/component-name/component-name.twig`
    $fractalPathLoader = new \TimberFractalPathsLoader\FractalPathLoader;

    // Allow other loaders if needed
    $loaders = [$loader, $fractalPathLoader];
    $loaders = apply_filters('timber_fractal_paths/loaders', $loaders);

    // Add the fractal path loader to the end of the load chain (i.e. Timber does its lookup,
    // then runs ours), and return it as the new loader
    return new \TimberFractalPathsChainLoader\FractalPathChainLoader($loaders); //$loadChain;

  }
}

new TimberCustomLoader();
