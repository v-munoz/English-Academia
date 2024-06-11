<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class VideosController{
    function index (Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $params = ['title' => 'Vídeos'];
        
        // Cargar el contenido del archivo JSON
        $jsonVideos = file_get_contents('res/videos.json');
        $videosData = json_decode($jsonVideos, true);

        return $view->render($response, 'videos.html', [
            'title' => $params['title'],
            'videos' => $videosData,
        ]);
    }
}
