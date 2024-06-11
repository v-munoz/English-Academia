<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;


class LegalController{
    // Función para cargar la vista del Aviso Legal
    function legal (Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $params = [
            'title' => 'Aviso Legal',
        ];
        return $view->render($response, 'legal_warning.html', $params);
    }

    // Función para cargar la vista de la Política de Cookies
    function cookies (Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $params = [
            'title' => 'Política de Cookies',
        ];
        return $view->render($response, 'cookies_policy.html', $params);
    }

    // Función para cargar la vista de la Política de Privacidad
    function privacy (Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $params = ['title' => 'Política de Privacidad'];
        return $view->render($response, 'privacy_policy.html', $params);
    }
    
} 
