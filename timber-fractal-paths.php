<?php

/*
Plugin Name: Timber Custom Loader
Description: This plugin adds a custom Twig loader to Timber
Version: 1.0.0
*/

class TimberCustomLoader {

  public function __construct() {
    $this->version = '1.0.0';

    if (!is_admin()) {
      add_action('plugins_loaded', [$this, 'plugin_checks']);
    }
  }

  public function plugin_checks() {
    add_filter('timber/loader/loader', [$this, 'add_loader']);
  }

  public function add_loader($loader) {
    require_once('lib/Loader.php');
    require_once('lib/CustomChainLoader.php');

    // Add namespace to Timber Loader
    $loader->addPath(FRACTAL_PATH, FRACTAL_HANDLE);
    $load2 = $loader;

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
