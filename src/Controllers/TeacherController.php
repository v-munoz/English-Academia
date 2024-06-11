<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class TeacherController{
    function index (Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $params = ['title' => 'Equipo'];

        $jsonTeachers = file_get_contents('res/teachers.json');
        $teachersData = json_decode($jsonTeachers, true);

        return $view->render($response, 'teachers.html', [
            'title' => $params['title'],
            'teachers' => $teachersData
        ]);
    }
}
