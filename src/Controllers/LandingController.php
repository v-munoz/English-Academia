<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class LandingController{
    function landing (Request $request, Response $response, array $args){
        $view = Twig::fromRequest($request);
        $params = ['title' => 'Landing'];
        return $view->render($response, 'landing.html', $params);
    }
}
