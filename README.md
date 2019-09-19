# timber-fractal-paths

Custom loader that allows Fractal paths to reroute to Fractal files.

## Fractal

Your [Fractal](https://fractal.build/) build should use a [twig
engine](https://github.com/frctl/twig). You can then call your fractal patterns as is
typical of Fractal:

```
{% include
  '@patterns/components/accordion-group/accordion-group-item' with {
    data: {
      title: 'Hi',
      content: 'Am content'
    }
  }
%}
```

`@patterns` is the default namespace used in this plugin, but `@components` is typical for
Fractal.

## Configuration

Namespace defaults to '@patterns'. Define `FRACTAL_HANDLE` in your theme to change the
handle (DO NOT use @ in the definition, i.e. 'patterns' _not_ '@patterns').

```
define('FRACTAL_HANDLE', 'patterns' );
```

Path defaults to 'THEME_PATH/fractal/patterns' (`get_stylesheet_directory() . '/fractal/patterns'`). Define `FRACTAL_PATH` in your theme to change the
handle (DO NOT use @ in the definition, i.e. 'patterns' _not_ '@patterns').

```
define('FRACTAL_PATH', get_stylesheet_directory() . '/fractal/patterns' );
```


## Want to add other Custom Loaders?

The `timber_fractal_paths/loaders` provides and array of loaders before they are pushed
into the custom chain loader. Create your loader, add it to the array, and return it.

```
add_filter('timber_fractal_paths/loaders', function($loaders) {
  $loaders[] = new SomeCustomLoaderClass($paths);
  return $loaders;
}));
```

---------------------

The scaffolding of this plugin came from [weareindi/timber-custom-loader](https://github.com/weareindi/timber-custom-loader)