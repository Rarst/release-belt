<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Provider;

use Psr\Http\Message\ServerRequestInterface;
use Rarst\ReleaseBelt\Application;
use Slim\Container;
use Slim\Http\Environment;
use Symfony\Component\Finder\Finder;
use Tuupola\Middleware\HttpBasicAuthentication;

/**
 * Implements HTTP authentication.
 */
class AuthenticationProvider
{
    /** @var Container */
    private $container;

    /**
     * Does necessary registrations on the app instance.
     */
    public function boot(Application $app): void
    {
        /** @var Container $container */
        $container       = $app->getContainer();
        $this->container = $container;

        $container['username'] = function () use ($container) {
            /** @var Environment $environment */
            $environment = $container['environment'];

            return $environment->get('PHP_AUTH_USER', '');
        };

        $userHashes = $this->getUserHashes();

        if (empty($userHashes)) {
            return;
        }

        $app->add(new HttpBasicAuthentication([
            'secure' => false,
            'users'  => $userHashes,
            'before' => $this->before(),
        ]));
    }

    /**
     * Returns a closure to use in authentication middleware.
     *
     * The closure add username attribute to request and applies permissions.
     */
    private function before(): \Closure
    {
        $auth = $this; // We need this because middleware binds the closure to its own object.

        return function (ServerRequestInterface $request, array $arguments) use ($auth) : ServerRequestInterface {
            $username = $arguments['user'] ?? '';

            $auth->applyPermissions(
                $auth->container['finder'],
                $auth->getPermissions($auth->container['users'], $username)
            );

            return $request->withAttribute('username', $username);
        };
    }

    /**
     * Retrieves a set of user names with password hashes from a container instance.
     */
    protected function getUserHashes(): array
    {
        $users = [];

        if (! empty($this->container['http.users'])) {
            trigger_error('`http.users` option is deprecated in favor of `users`.', E_USER_DEPRECATED);

            $users = $this->container['http.users'];
        }

        foreach ($this->container['users'] as $login => $data) {
            if (! empty($data['hash'])) {
                $users[$login] = $data['hash'];
            }
        }

        return $users;
    }

    /**
     * Retrieves package access permissions for a specific user.
     *
     * @param array[] $users
     */
    protected function getPermissions(array $users, string $user): array
    {
        return [
            'allow'    => $users[$user]['allow'] ?? [],
            'disallow' => $users[$user]['disallow'] ?? [],
        ];
    }

    /**
     * Applies access permissions on a Finder instance for package lookup..
     */
    protected function applyPermissions(Finder $finder, array $permissions): Finder
    {
        /** @var string $path */
        foreach ($permissions['allow'] as $path) {
            $finder->path((string)$path);
        }

        /** @var string $path */
        foreach ($permissions['disallow'] as $path) {
            $finder->notPath((string)$path);
        }

        return $finder;
    }
}
