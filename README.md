Smart-Plugin-Upgrader
=====================

To use to programmatically install or upgrade plugins.

## Usage

First initialize the plugin. The class takes two parameters; download url and slug. Both are required.

For plugins:
```
$upgrader = new Smart_Upgrader( 'http://example.com/my-plugin.zip', 'my-plugin' ); 
```

For themes:
```
$upgrader = new Smart_Upgrader_Theme( 'http://example.com/my-theme.zip', 'my-theme' ); 
```

To install the plugin/theme:
```
$upgrader->install(); // returns a boolean
````

To upgrade the plugin/theme:
```
$upgrader->upgrade();
```

To activate the plugin/theme:
```
$upgrader->activate();
```
