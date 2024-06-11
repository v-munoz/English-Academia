<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class EventsController{
    function index(Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $params = ['title' => 'Eventos'];
    
        // Cargar el contenido de archivos JSON
        $jsonEvents = file_get_contents('res/events.json');
        $eventsData = json_decode($jsonEvents, true);
    
        // Ordenar los eventos por fecha en orden descendente
        usort($eventsData, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Definir el número de events por página
        $eventsPerPage = 1;


        // Calcular la paginación para obtener los eventos concretos por página
        $totalEvents = count($eventsData);
        $page = $_GET['page'] ?? 1; 
        $totalPages = ceil($totalEvents / $eventsPerPage);
        $offset = ($page - 1) * $eventsPerPage;
        $events = array_slice($eventsData, $offset, $eventsPerPage);

        // Calcular el rango de páginas a mostrar alrededor de la página actual
        $visiblePageRange = 1; 
        $startPage = max(1, $page - $visiblePageRange);
        $endPage = min($totalPages, $page + $visiblePageRange);

        return $view->render($response, 'events.html', [
            'title' => $params['title'],
            'events' => $events,
            'page' => $page,
            'totalPages' => $totalPages,
            'startPage' => $startPage,
            'endPage' => $endPage
        ]);
    }
    
}
