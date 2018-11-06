<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rarst\ReleaseBelt\Model\FileModel;
use Slim\Exception\NotFoundException;
use Slim\Http\Stream;

class FileController
{
    protected $model;

    public function __construct(FileModel $model)
    {
        $this->model = $model;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $sendFile = $this->model->getFile($args['vendor'], $args['file']);

        if (! $sendFile->isReadable()) {
            throw new NotFoundException($request, $response);
        }

        //        $this->container->logDownload($sendFile);

        return $this->sendFile($response, $sendFile->getRealPath());
    }

    protected function sendFile(ResponseInterface $response, string $path): ResponseInterface
    {
        $fileStream = fopen($path, 'rb');

        return $response->withHeader('Content-Type', 'application/force-download')
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Type', 'application/download')
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Disposition', 'attachment; filename="'.basename($path).'"')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'public')
            ->withBody(new Stream($fileStream));
    }
}
