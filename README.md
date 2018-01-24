# Release Belt — Composer repo for ZIPs
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Rarst/release-belt/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Rarst/release-belt/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/Rarst/release-belt/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Rarst/release-belt/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/Rarst/release-belt/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Rarst/release-belt/?branch=master)
[![PDS Skeleton](https://img.shields.io/badge/pds-skeleton-blue.svg?style=flat-square)](https://github.com/php-pds/skeleton)

Release Belt is a Composer repository, which serves to quickly integrate third party non–Composer releases into Composer workflow. Once Release Belt is installed and you upload your zip files with their respected version number, Release Belt does the rest.

Given the following folder tree:

```
releases/wordpress-plugin/rarst/plugin.1.0.zip
```

It will serve the following Composer repository at `/packages.json` automagically:

```json
{
    "packages": {
        "rarst/plugin": {
            "1.0": {
                "name": "rarst/plugin",
                "version": "1.0",
                "dist": {
                    "url": "http://example.com/rarst/plugin.1.0.zip",
                    "type": "zip"
                },
                "type": "wordpress-plugin",
                "require": {
                    "composer/installers": "~1.0"
                }
            }
        }
    }
}
```

## Installation

### 1. Create the project

To create a standalone copy of the project:

```bash
composer create-project rarst/release-belt
```

To keep up with updates more conveniently you can use a Git checkout instead:

```bash
git clone https://github.com/Rarst/release-belt
cd release-belt
composer install --no-dev
```

Fetch latest changes and update dependencies with:

```bash
git pull
composer install --no-dev
```

### 2. Place release ZIPs into `releases/` directory

The directory structure should be: `releases/[type]/[vendor name]/[release zip file]`.

`[type]` could be e.g. `library`, `wordpress-plugin`, and `wordpress-theme`.

### 3. Configure a web server

`public/` directory should be used as web root and `index.php` in it as the file to handle requests.

Visit home page and `/packages.json` in a web browser to check if it is working.

#### Apache

On a typical Apache server this can be done with the following `.htaccess`:

```apacheconfig
FallbackResource /index.php
```

Or with mod_rewrite version:

```apacheconfig
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
</IfModule>
```


#### PHP

When using the built–in PHP web server you can use:

```bash
php -S localhost:8000 index.php
```

## Use

Once Release Belt is installed you can add the repository to the `composer.json` of your projects.

Release Belt home page will automatically generate some `composer.json` boilerplate for you to use.

### Configuration

You can configure Release Belt by creating `config/config.php` file, which returns array of options to override.

See [`config/configExample.php`](config/configExample.php) for the annotated example and default settings.

#### Authentication

Release Belt implements HTTP authentication to password protect your repository. You can configure it by adding `http.users` array to configuration, which holds `'login' => 'password hash'` pairs.

There is an `bin/encodePassword.php` command line helper included for hashing passwords:

```bash
>php bin/encodePassword.php foo
$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a
```

If authentication is enabled, Release Belt home page will automatically generate `auth.json` boilerplate for you to use.

## F.A.Q.

### Why not Packagist/Satis?

Composer infrastructure is awesome, but it expects vendors that are willing to play nice with it.

Release Belt is a solution for unwilling vendors and it was faster and easier to build a dedicated solution from scratch. 

### Why not artifacts?

Composer artifacts require `composer.json` in them. This is for releases that don't even have that.

### But is it web scale?

No.


# License

MIT
