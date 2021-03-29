Darwin
======

[![GitHub Releases](https://img.shields.io/github/release/juniwalk/darwin.svg?style=flat-square)](https://github.com/juniwalk/darwin/releases)
[![Total Donwloads](https://img.shields.io/packagist/dt/juniwalk/darwin.svg?style=flat-square)](https://packagist.org/packages/juniwalk/darwin)
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
$ darwin fix [folder] --config=CONFIG
```

- `[folder]`: path to directory to fix.
- `--config`: name of configuration file.

git:changelog | changelog
---------------------
Create changelog from git log output.

```
$ darwin changelog [range] --branch=BRANCH
```

- `[range]`: range of the logs to include *("rebuild" or sha..sha)*.
- `--branch`: name of working branch.

image:shrink | shrink
---------------------
Use this command to shrink all images that ale larger than given size option.

```
$ darwin shrink --size=SIZE --quality=QUALITY --backup
```

- `--size`: size to which the image will be fitted.
- `--quality`: quality of resulting image.
- `--backup`: backup image before resizing *(adds .backup suffix to filename)*.

image:restore | restore
-----------------------
Restore all modified images *(any image with suffix .backup)*.

```
$ darwin restore
```

backup:clean
------------
Clean out backups using defined parameters.

```
$ darwin backup:clean [folder] --force --keep-count=KEEP-COUNT --keep-time=KEEP-TIME
```

- `[folder]`: path to directory to clean
- `--force`: command runs in dry-run as default. Use -f to perform deletions.
- `--keep-count`: minimum number of backups to be kept per project.
- `--keep-time`: keep backups that are no older than keep-time.
