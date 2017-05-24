[![Build Status](https://travis-ci.org/grasmash/xdebug-toggle.svg?branch=master)](https://travis-ci.org/grasmash/xdebug-toggle)

# Xdebug Toggle

A PHP-based CLI tool for quickly enabling and/or disabling xdebug.

This tool simply "comments out" the line in your php.ini file that loads the xDebug Zend extension. 

### Available commands:

| Command | Description                                     |
|---------|-------------------------------------------------|
| toggle  | Enables or disables xDebug, depending on state. |
| enable  | Enables xDebug.                                 |
| disable | Disables xDebug.                                |
| status  | Prints current status of xDebug.                |


### Example:

```
xdebug toggle
xdebug enable
xdebug disable
xdebug status
```

### Installation

You may use `Composer` to install this library. It is suggested that you install it globally and add Composer's global `bin` directory to your system's `$PATH`.

```
composer global require grasmash/xdebug-toggle --update-no-dev
```