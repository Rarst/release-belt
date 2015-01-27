# Release Belt — Composer repo for ZIPs

Release Belt is a prototype Composer repository, which serves to quickly integrate third party non–Composer releases into Composer workflow.

Given the following folder tree:

```
releases
	wordpress-plugin
		yoast
			wordpress-seo.1.6.zip
			wordpress-seo.1.7.zip
```

It will serve the following Composer repository at `/packages.json`:

```json
{
    "packages": {
		"yoast/wordpress-seo": {
            "1.6": {
                "name": "yoast/wordpress-seo",
                "version": "1.6",
                "dist": {
                    "url": "http://example.com/yoast/wordpress-seo.1.6.zip",
                    "type": "zip"
                },
                "type": "wordpress-plugin",
                "require": {
                    "composer/installers": "~1.0"
                }
            },
            "1.7": {
                "name": "yoast/wordpress-seo",
                "version": "1.7",
                "dist": {
                    "url": "http://example.com/yoast/wordpress-seo.1.7.zip",
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

## Installation (basic as–is usage for now)

Create the project:

```
composer create-project rarst/release-belt:dev-master
```

Place release ZIPs into `/releases/[type]/[vendor]/`.

Configure a web server to serve `index.php`, for example with the following `.htaccess`:

```
FallbackResource /index.php
```  

When using the built in webserver of PHP >=5.4.0 you can use

```
php -S localhost:8000 php/router.php
```

Release Belt DOES NOT have authentication implemented yet. Secure it via your web server if you dare to put it into the wild in its current state. 

## F.A.Q.

### Why not Packagist/Satis?

Composer infrastructure is awesome, but it expects vendors that are willing to play nice with it.

Release Belt is a solution for unwilling vendors and it was faster and easier to build a prototype from scratch. 

### Why not artifacts?

Composer artifacts require `composer.json` in them. This is for releases that don't even have that.

### But is it web scale?

No.
