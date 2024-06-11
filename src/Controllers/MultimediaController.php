<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class MultimediaController {
    public function index(Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $params = ['title' => 'Multimedia'];

        $folder = 'images/multimedia';
        $mediaData = $this->getImagesFromFolder($folder);

        return $view->render($response, 'multimedia.html', [
            'title' => $params['title'],
            'media' => $mediaData
        ]);
    }

    private function getImagesFromFolder($folder) {
        $images = [];
        $imageFiles = glob($folder . '/*', GLOB_BRACE);
        foreach ($imageFiles as $image) {
            $images[] = [
                'url' => 'images/multimedia/' . basename($image),
                'alt' => basename($image)
            ];
        }
        return $images;
    }
}
