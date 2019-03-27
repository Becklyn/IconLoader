Icon Loader
===========

Icon loader, that loads `.svg` icons from different directories and provides a registry to embed them.

Configuration:
--------------

```yaml
becklyn_icon_loader:    
    search_glob: "build/mayd/*/icon"    # this is the default value
```

The glob defines a path to all directories, that can contain SVG icons. These directories will be searched recursively and
all found icons will be added to the registry.

Usage
-----

In PHP:

```php
$registry = $container->get(IconRegistry::class);

$svgContent = $registry->get("add");    // $svgContent === "<svg xml..."
```

In Twig:

```twig
{{ icon("add") }}
```


Notable Behavior
----------------

*   Missing icons produce an exception if the app is in debug mode and will be an empty string in prod.
*   If multiple icons with the same name are found, an error is thrown only if these icons have different content.
*   The registry is cached in non-debug mode, so there shouldn't be any performance overhead.
