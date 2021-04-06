clean:backup
------------
Clear out backups using defined parameters

```
$ darwin clean:backup [folder] --force --keep-count=KEEP-COUNT --keep-time=KEEP-TIME
```

- `[folder]`: path to directory to clean
- `--force`: command runs in dry-run as default. Use -f to perform deletions
- `--keep-count`: minimum number of backups to be kept per project
- `--keep-time`: keep backups that are no older than keep-time


clean:cache | clean
-------------------
Clear application cache and fix permissions

```
$ darwin clean:cache --skip-fix
```

- `--skip-fix`: skip fixing permissions


clean:logs
----------
Remove all error logs

```
$ darwin clean:logs
```


clean:sessions
--------------
Remove all user sessions

```
$ darwin clean:sessions
```
