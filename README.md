# Release Belt — Composer repo for ZIPs

Release Belt is a Composer repository, which serves to quickly integrate third party non–Composer releases into Composer workflow. Once Release Belt is installed and you upload your zip files with their respected version number, Release Belt does the rest.

Given the following folder tree:

```
releases
	wordpress-plugin
		rarst
			plugin.1.0.zip
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

Create the project:

```
composer create-project rarst/release-belt:dev-master
```

Place release ZIPs into `/releases/[type]/[vendor]/`.

Configure a web server to serve `index.php`, for example with the following `.htaccess`:

```
FallbackResource /index.php
```

When using the built in webserver of PHP >=5.4.0 you can use:

```
php -S localhost:8000 index.php
```

## Use

Once Release Belt is installed you can add the repository to the `composer.json` of your projects.

Release Belt home page will automatically generate some `composer.json` boilerplate for you to use.

### Configuration

You can configure Release Belt by creating `config.php` file, which returns array of options to override.

For example:

```php
<?php

// config.php

return [
    'debug' => true,  
];
```

### Authentication

Release Belt implements HTTP authentication to password protect your repository. You can configure it by adding `http.users` array to configuration, which holds `'login' => 'password hash'` pairs.

For example:

```php
<?php

// config.php

return [
    'http.users' => [
        'composer' => '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a',
    ],
    'http.whitelist' => [
        '127.0.0.1',
        '::1',
        '192.168.1.0/24',
        'fe80::/10'
    ]
];
```

There is an `encodePassword.php` command line helper included for hashing passwords:

```bash
>php encodePassword.php foo
$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a
```

If authentication is enabled, Release Belt home page will automatically generate `auth.json` boilerplate for you to use.

As seen in the example above, Release Belt optionally supports the ability to exempt a specific IP addresses or a range of IP addresses (specified with an IP/netmask) from having to authenticate. This is configured with a `http.whitelist` array.

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