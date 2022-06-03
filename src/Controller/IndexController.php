<?php

declare(strict_types=1);

namespace Rarst\ReleaseBelt\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rarst\ReleaseBelt\Model\IndexModel;
use Slim\Views\Mustache;

/**
 * Handles index route.
 */
class IndexController
{
    /** @var Mustache */
    protected $view;

    /** @var IndexModel */
    protected $model;

    /**
     * IndexController constructor.
     */
    public function __construct(Mustache $view, IndexModel $model)
    {
        $this->view  = $view;
        $this->model = $model;
    }

    /**
     * Renders model context with mustache template.
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->view->render($response, 'index', $this->model->getContext());
    }
}
