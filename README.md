# Assets Tracker

Welcome to the Assets Tracker plugin.

It is still under development, but everything works (afaik).

## Description 

This plugin gives you the option to input your assets and actively keep track of them.

## Installation

### Manual
* Upload the zip through WordPress plugin admin or
* Upload the files by ftp to `wp-content/plugins`.
* Activate the plugin `CSV to WP` through the plugins page. 

### Composer
Add this to your composer.json
```
  "repositories": [
    {
      "type":    "package",
      "package": {
        "name":    "beee4life/b3-assets-tracker",
        "type":    "wordpress-plugin",
        "version": "1.0",
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

08.10.24 - First release 1.0.0
