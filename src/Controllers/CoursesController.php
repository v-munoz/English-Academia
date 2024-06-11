<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CoursesController{
    function index (Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $params = ['title' => 'Cursos'];

        // Cargar el contenido del archivo JSON
        $jsonContent = file_get_contents('res/courses.json');
        $coursesData = json_decode($jsonContent, true);

        return $view->render($response, 'courses.html', [
            'title' => $params['title'],
            'courses' => $coursesData
        ]);
    }
}
