# Assets Tracker

Welcome to the Assets Tracker plugin.

It is still under development, but everything works (afaik).

## Description

This plugin gives you the option to input your assets and actively keep track of them.

## Installation

### Manual
* Upload the zip through WordPress plugin admin or
* Upload the files by ftp to `wp-content/plugins`.
* Activate the plugin `B3 : Assets Tracker` through the plugins page.

### Composer
Add this to your composer.json
```
  "repositories": [
    {
      "type":    "package",
      "package": {
        "name":    "beee4life/b3-assets-tracker",
        "type":    "wordpress-plugin",
        "dist":    {
          "type": "zip",
          "url":  "https://github.com/beee4life/b3-assets-tracker/archive/master.zip"
        }
      }
    }
  ],
```

Then run
```
composer require "Beee4life/b3-assets-tracker"
```

## CHANGELOG

1.2.0 - 12.10.24
* add line graph for all assets
* add curency symbol to graphs

1.1.0 - 09.10.24
* add closed status for type/position

1.0.0 - 08.10.24
First release
