<?php

declare(strict_types=1);

namespace Rarst\ReleaseBelt\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

/**
 * Handles packages.json route.
 */
class JsonController
{
    /** @var array[] */
    protected array $data;

    private bool $debug;

    /**
     * @param array[] $data
     */
    public function __construct(array $data, bool $debug = false)
    {
        $this->data  = $data;
        $this->debug = $debug;
    }

    /**
     * Returns JSON response with packages data.
     */
    public function __invoke(ServerRequestInterface $request, Response $response): Response
    {
        $prettyPrint = $this->debug ? JSON_PRETTY_PRINT : 0;

        return $response->withJson($this->data, null, JSON_THROW_ON_ERROR | $prettyPrint);
    }
}
