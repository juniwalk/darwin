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
