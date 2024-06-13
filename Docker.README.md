# phpcfdi/captcha-local-resolver dockerfile helper

```shell script
# get the project repository on folder "captcha-local-resolver"
git clone https://github.com/phpcfdi/captcha-local-resolver.git captcha-local-resolver

# build the image "captcha-local-resolver" from folder "captcha-local-resolver/"
docker build --tag captcha-local-resolver captcha-local-resolver/

# remove image captcha-local-resolver
docker rmi captcha-local-resolver
```

## Run command

The project installed on `/opt/captcha-local-resolver/` and the entry point is the command
`/opt/captcha-local-resolver/bin/service.php`.

```shell
# show help
docker run -it --rm --user="$(id -u):$(id -g)" \
  captcha-local-resolver --help

# run service on ip 127.0.0.1 port 8086 in the foreground
docker run -it --rm --user="$(id -u):$(id -g)" --network host \
  captcha-local-resolver 127.0.0.1:8086

# run service on ip 127.0.0.1 port 8086 in the background
docker run --rm --user="$(id -u):$(id -g)" --detach --network host --name captcha-local-resolver \
  captcha-local-resolver 127.0.0.1:8086
```
