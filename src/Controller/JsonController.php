<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

/**
 * Handles packages.json route.
 */
class JsonController
{
    /** @var array|array[] */
    protected $data;

    /** @var bool */
    private $debug;

    /**
     * JsonController constructor.
     *
     * @param array[] $data
     */
    public function __construct(array $data, bool $debug = false)
    {
        $this->data  = $data;
        $this->debug = $debug;
    }

    /**
     * Returns JSON response with packages data.
     *
     * @suppress PhanTypeMismatchArgument
     * @suppress PhanUnusedPublicMethodParameter
     */
    public function __invoke(ServerRequestInterface $request, Response $response): ResponseInterface
    {
        return $response->withJson($this->data, null, $this->debug ? JSON_PRETTY_PRINT : 0);
    }
}
