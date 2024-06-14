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

## Version 0.1.2 2024-06-13

Fixes and improvements:

- Fix continuous integration build by adding `ringcentral/psr7` ad development dependency.
- Fix `server:port` argument on command line script.
- Provide Docker instructions.

Maintenance:

- Update license year to 2024.
- Remove release badge.
- Update build badge.
- Update code style standards configuration to recent versions.
- Disallow language detection on `public/` directory.
- Update GitHub workflow:
  - Allow running jobs manually.
  - Add PHP 8.2 and PHP 8.3 to test matrix.
  - Run jobs using PHP 8.3.
  - Change `::set-output` directive to `$GITHUB_OUTPUT`.
  - Update GitHub actions to version 4.
- Update developent tools.

## Version 0.1.1 2022-05-25

Maintenance review:

- Update license year.
- Fix PHPStan found issues.
- Fix using deprecated React objects.
- Update `.gitattibutes` excluded files.
- Split CI *worflow steps* to *worflow jobs*.
- Add PHP 8.1 to CI.
- Migrate development tools from `develop/install-development-tools` to `phive`.
- Update code style to PSR-12.
- Scrutinizer create its own code coverage.
- Move `development/docs/Testing.md` to `docs/Testing.md`
- Remove `console.log` calls from `index.html` implementation.

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
