
make:close | fix
----------------
Set file permissions as strictly closed

```
$ darwin make:close --force
```

- `--force`: force permission fix even on excluded folders.


make:open
---------
Set file permissions as open

```
$ darwin make:open --force
```

- `--force`: force permission fix even on excluded folders


make:config
-----------
Create darwin configuration file

```
$ darwin make:config --type=TYPE
```

- `--type`: type of the config to be created


make:locked | lock
------------------
LOCK access into website

```
$ darwin make:locked
```


make:unlocked | unlock
----------------------
UNLOCK access into website

```
$ darwin make:unlocked
```