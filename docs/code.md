code:changelog | changelog
--------------------------
Create changelog from git log output

```
$ darwin code:changelog [range] --branch=BRANCH --filter=FILTER --auto-commit
```

- `[range]`: range of the logs to include *("rebuild" or sha..sha)*
- `--branch`: name of working branch
- `--filter`: commit to be filtered using message
- `--auto-commit`: automaticaly commit generated changelog


code:deploy | deploy
--------------------
Deploy pending updates to the project

```
$ darwin code:deploy --unlock
```

- `--unlock`: unlock application afterwards


code:install
------------
Install application dependencies

```
$ darwin code:install
```


code:pull
---------
Pull repository changes

```
$ darwin code:pull
```

code:warmup | warmup
--------------------
Warmup the use of the application

```
$ darwin code:warmup
```
