# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Unreleased

## 0.4 - 2018-11-09

### Changed

- framework dependencies to Slim from Silex.
- minimum required PHP version to 7.1.3. 

### Removed

- unnecessary FileInfo extension dependency.

## 0.3 - 2018-02-01

### Added
- command line boilerplate to home page.
- site favicon, image from [Bytesize](https://danklammer.com/bytesize-icons/).
- support for all `composer/installer` types (^1.5).
- `users` configuration option with permission control by package path.
- Composer script for updates.

### Changed
- home page markup to Bootstrap 4.
- downloads log configuration for simpler.

### Fixed
- path to the new config location in password helper.

### Deprecated
- `http.users` configuration option in favor of `users`.

## 0.2 - 2018-01-17

### Added
- `public` directory for use as web root.
- log of downloads.

### Changed
- directory structure for PDS skeleton.
- moved config file location to the new `config` directory and provided example config file.

### Deprecated
- use of package root as web root.

## 0.1 - 2018-01-02

### Added
- tagged an initial stable release.