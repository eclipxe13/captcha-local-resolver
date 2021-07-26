# CHANGELOG

## About Semantic Versioning

The Semantic Versioning rules apply *only* to:

- The HTTP API.
- The service startup (parameters to start the server).

In summary, [SemVer](https://semver.org/) can be viewed as `[ Breaking ].[ Feature ].[ Fix ]`, where:

- `Breaking version` includes incompatible changes to the API.
- `Feature version` adds a new feature(s) in a backwards-compatible manner.
- `Fix version` includes backwards-compatible bug fixes.

**Version `0.x.x` doesn't have to apply any of the SemVer rules**

## Version 0.1.0 2021-07-25

Usage changes:

- `bin/service.php` now receives 1 argument as `<ip-addess>:<port-number>`.
- API now allows `application/json` content types in requests.

Internal changes:

- Update development environment.

## Version 0.0.2 2021-03-04

- Add new route `/discard-code` and use it in default web application.
- Fix web application double entry when the same image is inserted two times.
- Upgrade to: `php: ^7.4`, `react/react: ^1.1`, `react/http: ^1.2` and `phpunit/phpunit: ^9.5`.

## Version 0.0.1 2020-02-06

- Initial release
