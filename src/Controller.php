<?php
namespace Rarst\ReleaseBelt;

use Slim\Container;
use Rarst\ReleaseBelt\Model\FileModel;
use Rarst\ReleaseBelt\Model\IndexModel;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getHtml()
    {
        /** @var IndexModel $indexModel */
        $indexModel = $this->container['model.index'];

        return $this->container->view->fetch('index', $indexModel->getContext());
    }

    public function getJson(Application $app)
    {
        return $app->json($app['data']);
    }

    public function getFile(Application $app, $vendor, $file)
    {
        /** @var FileModel $fileModel */
        $fileModel = $app['model.file'];

        try {
            $sendFile = $fileModel->getFile($vendor, $file);
        } catch (FileNotFoundException $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $app->logDownload($sendFile);

        return $app->sendFile($sendFile->getRealPath());
    }
}
