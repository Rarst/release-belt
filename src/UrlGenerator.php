<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt;

use Slim\Interfaces\RouterInterface;

class UrlGenerator implements UrlGeneratorInterface
{
    /** @var RouterInterface $router */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getUrl(string $name, array $data = []): string
    {
        return $this->router->pathFor($name, $data);
    }

    public function getFileUrl(string $vendor, string $file): string
    {
        return $this->getUrl('file', ['vendor' => $vendor, 'file' => $file]);
    }
}
