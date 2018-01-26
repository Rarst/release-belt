<?php

namespace Rarst\ReleaseBelt\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $app)
    {
        $app['security.firewalls'] = [];

        $app->extend('finder', function (Finder $finder, Application $app) {

            if (empty($app['security.firewalls']) || empty($app['users'])) {
                return $finder;
            }

            /** @var array[][] $users */
            $users = $app['users'];
            /** @var Request $request */
            $request = $app['request_stack']->getCurrentRequest();
            $user    = $request->getUser();

            if (! empty($users[$user]['allow'])) {
                foreach ($users[$user]['allow'] as $path) {
                    $finder->path($path);
                }
            }

            if (! empty($users[$user]['disallow'])) {
                foreach ($users[$user]['disallow'] as $path) {
                    $finder->notPath($path);
                }
            }

            return $finder;
        });
    }

    public function boot(Application $app)
    {
        if (empty($app['http.users']) && empty($app['users'])) {
            return;
        }

        $users = [];

        if (! empty($app['http.users'])) {
            trigger_error('`http.users` option is deprecated in favor of `users`.', E_USER_DEPRECATED);

            foreach ($app['http.users'] as $login => $hash) {
                $users[$login] = ['ROLE_COMPOSER', $hash];
            }
        }

        foreach ($app['users'] as $login => $data) {
            $users[$login] = ['ROLE_COMPOSER', $data['hash']];
        }

        $app['security.firewalls'] = [
            'composer' => [
                'pattern' => '^.*$',
                'http'    => true,
                'users'   => $users,
            ],
        ];
    }
}
