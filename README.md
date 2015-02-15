Darwin - work in progress
======

[![Travis](https://img.shields.io/travis/juniwalk/Darwin.svg?style=flat-square)](https://travis-ci.org/juniwalk/Darwin)
[![GitHub Releases](https://img.shields.io/github/release/juniwalk/Darwin.svg?style=flat-square)](https://github.com/juniwalk/Darwin/releases)
[![Total Donwloads](https://img.shields.io/packagist/dt/juniwalk/Darwin.svg?style=flat-square)](https://packagist.org/packages/juniwalk/Darwin)
[![Code Quality](https://img.shields.io/scrutinizer/g/juniwalk/Darwin.svg?style=flat-square)](https://scrutinizer-ci.com/g/juniwalk/Darwin/)
[![Tests Coverage](https://img.shields.io/scrutinizer/coverage/g/juniwalk/Darwin.svg?style=flat-square)](https://scrutinizer-ci.com/g/juniwalk/Darwin/)
[![License](https://img.shields.io/packagist/l/juniwalk/Darwin.svg?style=flat-square)](https://mit-license.org)

This is tiny CLI application which will help you manage your projects.

Installation
------------

Best way to install Darwin is using global composer.

```
$ composer global require juniwalk/darwin
```

If you do not have composer's /bin dir in $PATH then install Darwin using it's own command.

```
$ /root/.composer/vendor/bin/darwin self::install
```

This will create symlink to Darwin in `/usr/local/bin` directory.

Updating
--------

To update Darwin, just use composer's update command.

```
$ composer global update juniwalk/darwin
```

Fix permissions
---------------

Fix permissions of files and dirs on given path. If dir param is ommited, current working dir is used instead.

```
$ darwin fix /path/to/dir -o|--owner -f|--force
```

- `dir`: path to dir, if ommited, current dir is used.
- `--owner`: owner for found files and dirs, if ommited, www-data is used.
- `--force`: force the fix without checks.
