Darwin - work in progress
======

[![Travis](https://img.shields.io/travis/juniwalk/Darwin.svg?style=flat-square)](https://travis-ci.org/juniwalk/Darwin)
[![GitHub Releases](https://img.shields.io/github/release/juniwalk/Darwin.svg?style=flat-square)](https://github.com/juniwalk/Darwin/releases)
[![Total Donwloads](https://img.shields.io/packagist/dt/juniwalk/Darwin.svg?style=flat-square)](https://packagist.org/packages/juniwalk/Darwin)
[![Code Quality](https://img.shields.io/scrutinizer/g/juniwalk/Darwin.svg?style=flat-square)](https://scrutinizer-ci.com/g/juniwalk/Darwin/)
[![Tests Coverage](https://img.shields.io/scrutinizer/coverage/g/juniwalk/Darwin.svg?style=flat-square)](https://scrutinizer-ci.com/g/juniwalk/Darwin/)
[![License](https://img.shields.io/packagist/l/juniwalk/Darwin.svg?style=flat-square)](https://mit-license.org)

This is tiny CLI application which will help you manage your projects.

Fix permissions
---------------

Fix permissions of files and dirs in given dir. If dir is ommited, current working dir is used instead.

```
$ darwin fix dir -o|--owner -f|--force
```

- `dir`: path to dir, if ommited, current dir is used.
- `--owner`: owner for found files and dirs, if ommited, www-data is used.
- `--force`: force the fix without checks.
