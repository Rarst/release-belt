<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Provider;

use Slim\App;
use Slim\Container;
use Symfony\Component\Finder\Finder;
use Tuupola\Middleware\HttpBasicAuthentication;

class AuthenticationProvider
{
    public function boot(App $app): void
    {
        /** @var Container $container */
        $container       = $app->getContainer();
        $userHashes = $this->getUserHashes($container);

        if (empty($userHashes)) {
            return;
        }

        $app->add(new HttpBasicAuthentication([
            'secure' => false,
            'users'  => $userHashes,
        ]));

        $container->extend('finder', function (Finder $finder, Container $container) {

            /** @var array[][] $users */
            $users = $container['users'];
            [$user] = explode(':', $container->request->getUri()->getUserInfo());

            return $this->applyPermissions($finder, $this->getPermissions($users, $user));
        });
    }

    protected function getUserHashes(Container $app): array
    {
        $users = [];

        if (! empty($app['http.users'])) {
            trigger_error('`http.users` option is deprecated in favor of `users`.', E_USER_DEPRECATED);

            foreach ($app['http.users'] as $login => $hash) {
                $users[$login] = $hash;
            }
        }

        foreach ($app['users'] as $login => $data) {
            if (! empty($data['hash'])) {
                $users[$login] = $data['hash'];
            }
        }

        return $users;
    }

    protected function getPermissions(array $users, $user): array
    {
        return [
            'allow'    => $users[$user]['allow'] ?? [],
            'disallow' => $users[$user]['disallow'] ?? [],
        ];
    }

    protected function applyPermissions(Finder $finder, array $permissions): Finder
    {
        foreach ($permissions['allow'] as $path) {
            $finder->path($path);
        }

        foreach ($permissions['disallow'] as $path) {
            $finder->notPath($path);
        }

        return $finder;
    }
}
