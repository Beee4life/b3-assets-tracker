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

1.14.0 - xx.11.24
* ?

1.13.0 - 03.11.24
* add bar chart
* style chart

1.12.0 - 03.11.24
* add graph arguments

1.11.0 - 01.11.24
* fix diff with non-existing values

1.10.0 - 29.10.24
* add group icons

1.9.0 - 29.10.24
* fix dashboard for 1 date
* disable continues for added

1.8.0 - 28.10.24
* improve graph shortcode
* improve stuff for front-end renders

1.7.0 - 27.10.24
* add graph shortcode
* sanitize/escape input/output

1.6.0 - 23.10.24
* change graph width to 100%
* partially sanitize input/escape output

1.5.1 - 18.10.24
* disable added condition

1.5.0 - 18.10.24
* improve asset type queries with added date

1.4.1 - 18.10.24
* include admin file for front-end use

1.4.0 - 18.10.24
* add added date for asset type
* hide assets if not added on date
* improve asset type view
* set width admin column

1.3.0 - 15.10.24
* Fix delete asset type(s)
* Change graph widths

1.2.1 - 13.10.24
* Fix missing css in shortcode output
* Split forms function into separate ones
* Add updated column to asset data

1.2.0 - 12.10.24
* add line graph for all assets
* add curency symbol to graphs

1.1.0 - 09.10.24
* add closed status for type/position

1.0.0 - 08.10.24
First release
