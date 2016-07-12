Darwin
======

[![Travis](https://img.shields.io/travis/juniwalk/darwin.svg?style=flat-square)](https://travis-ci.org/juniwalk/darwin)
[![GitHub Releases](https://img.shields.io/github/release/juniwalk/darwin.svg?style=flat-square)](https://github.com/juniwalk/darwin/releases)
[![Total Donwloads](https://img.shields.io/packagist/dt/juniwalk/darwin.svg?style=flat-square)](https://packagist.org/packages/juniwalk/darwin)
[![Code Quality](https://img.shields.io/scrutinizer/g/juniwalk/darwin.svg?style=flat-square)](https://scrutinizer-ci.com/g/juniwalk/darwin/)
[![Tests Coverage](https://img.shields.io/scrutinizer/coverage/g/juniwalk/darwin.svg?style=flat-square)](https://scrutinizer-ci.com/g/juniwalk/darwin/)
[![License](https://img.shields.io/packagist/l/juniwalk/darwin.svg?style=flat-square)](https://mit-license.org)

This is tiny CLI application which will help you manage your projects.

Installation
------------

Darwin requires composer for its functionality, install it using this command:

```
$ composer global require juniwalk/darwin
```

file:permission | fix
---------------------
Fix permissions of files and dirs on current working directory.

```
$ darwin fix
```

image:shring | shring
---------------------
Use this command to shring all images that ale larger than given size option.

```
$ darwin shring --size=SIZE --quality=QUALITY --backup
```

- `---size`: size to which the image will be fitted.
- `---quality`: quality of resulting image.
- `---backup`: backup image before resizing *(adds .backup suffix to filename)*.

image:restore | restore
-----------------------
Restore all modified images *(any image with suffix .backup)*.

```
$ darwin restore
```
