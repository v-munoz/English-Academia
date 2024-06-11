<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController{
    function index (Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $params = ['title' => 'Inicio'];

        // Obtener la fecha actual para obtener el próximo concierto
        $currentDate = time();

        // Cargar el contenido de conciertos desde el archivo JSON
        $jsonEvents = file_get_contents('res/events.json');
        $eventsData = json_decode($jsonEvents, true);

        // Inicializar el próximo concierto con un valor muy grande
        $closestEventDate = PHP_INT_MAX;
        $nextEvent = null;

        // Iterar sobre cada concierto para encontrar el más próximo a la fecha actual
        foreach ($eventsData as $event) {
            // Convertir la fecha del concierto a formato UNIX timestamp
            $eventDate = strtotime($event['date']);

            // Verificar si la fecha del concierto es más cercana que la fecha más próxima actual
            if ($eventDate < $closestEventDate && $eventDate > $currentDate) {
                $closestEventDate = $eventDate;
                $nextEvent = $event;
            }
        }

        // Cargar el contenido de canciones desde el archivo JSON
        $jsonCourses = file_get_contents('res/courses.json');
        $coursesData = json_decode($jsonCourses, true);
        $lastCourse = $coursesData[0];

        // Cargar el contenido de videos desde el archivo JSON
        $jsonVideos = file_get_contents('res/videos.json');
        $videosData = json_decode($jsonVideos, true);
        $lastVideo = $videosData[0];

        return $view->render($response, 'home.html', [
            'title' => $params['title'],
            'last_course' => $lastCourse,
            'last_video' => $lastVideo,
            'next_event' => $nextEvent
        ]);
    } 
}
