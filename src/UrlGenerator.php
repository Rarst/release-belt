<?php

declare(strict_types=1);

namespace Rarst\ReleaseBelt;

use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouteParserInterface;

/**
 * Concrete implementation for the absolute URL generation.
 */
class UrlGenerator implements UrlGeneratorInterface
{
    private RouteParserInterface $routeParser;

    private UriInterface $uri;

    public function __construct(RouteParserInterface $routeParser, UriInterface $uri)
    {
        $this->routeParser = $routeParser;
        $this->uri         = $uri->withUserInfo('');
    }

    public function getUrl(string $name, array $data = []): string
    {
        return (string)$this->uri->withPath($this->routeParser->urlFor($name, $data));
    }

    public function getFileUrl(string $vendor, string $file): string
    {
        return $this->getUrl('file', ['vendor' => $vendor, 'file' => $file]);
    }
}
