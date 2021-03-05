# Testing

## Unit test

This tests will check individually the components, tests must check only one class at the time.

## Functional tests

These tests are more behavioral, they are expected to test from input to output but not using a
running server. This can be made executing the `Application` object.

## Manual testing commands

```shell
# run server
php bin/service.php 127.0.0.1 9794

# get homepage
curl -v http://127.0.0.1:9794/

# get list of current captchas
curl -s http://127.0.0.1:9794/captchas | jq .

# send images
# # image qwerty
curl -s --data-urlencode "image=$(base64 -w0 tests/_files/captchas/qwerty.png)" http://127.0.0.1:9794/send-image | jq .
{
  "code": "460ea98992564122f444fd6290108eda"
}
# # image 123456
curl -s --data-urlencode "image=$(base64 -w0 tests/_files/captchas/123456.png)" http://127.0.0.1:9794/send-image | jq .
{
  "code": "ff5f74f4d238eefafcc9dcbc52ee7a09"
}

# obtain decoded image qwerty (with no answer)
curl -s -d code=460ea98992564122f444fd6290108eda http://127.0.0.1:9794/obtain-decoded | jq .
{
  "code": "460ea98992564122f444fd6290108eda"
}

# set answer for image qwerty
curl -vs -d code=460ea98992564122f444fd6290108eda -d answer=qwerty http://127.0.0.1:9794/set-code-answer | jq .

# obtain decoded image qwerty (with answer)
curl -s -d code=460ea98992564122f444fd6290108eda http://127.0.0.1:9794/obtain-decoded | jq .
{
  "code": "460ea98992564122f444fd6290108eda"
  "answer": "qwerty"
}

# discard image 123456
curl -sv -d "code=ff5f74f4d238eefafcc9dcbc52ee7a09" http://127.0.0.1:9794/discard-code
```
