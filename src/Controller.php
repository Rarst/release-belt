<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Container;
use Rarst\ReleaseBelt\Model\FileModel;
use Rarst\ReleaseBelt\Model\IndexModel;
use Slim\Exception\NotFoundException;
use Slim\Http\Response;
use Slim\Http\Stream;

class Controller
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getHtml(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        /** @var IndexModel $indexModel */
        $indexModel = $this->container['model.index'];

        return $this->container->view->render($response, 'index', $indexModel->getContext());
    }

    public function getJson(ServerRequestInterface $request, Response $response): ResponseInterface
    {
        return $response->withJson($this->container['data']);
    }

    public function getFile(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        /** @var FileModel $fileModel */
        $fileModel = $this->container['model.file'];
        $sendFile  = $fileModel->getFile($args['vendor'], $args['file']);

        if (! $sendFile->isReadable()) {
            throw new NotFoundException($request, $response);
        }

//        $this->container->logDownload($sendFile);

        $fileStream = fopen($sendFile->getRealPath(), 'rb');

        return $response->withHeader('Content-Type', 'application/force-download')
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Type', 'application/download')
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Disposition', 'attachment; filename="'.$sendFile->getBasename().'"')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'public')
            ->withBody(new Stream($fileStream));
    }
}
