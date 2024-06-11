<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class StoreController {
    /*
    Función de inicio para cargar la vista de Tienda
    */
    function index(Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $params = ['title' => 'Tienda'];

        // Cargar el contenido del archivo JSON
        $jsonItems = file_get_contents('res/products.json');
        $itemsData = json_decode($jsonItems, true);

        return $view->render($response, 'store.html', [
            'title' => $params['title'],
            'products' => $itemsData
        ]);
    }

    // Función GET '/detalles/{producto}'para cargar los detalles de cada producto
    function productDetails(Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $product = $args['product'];
        $params = [
            'title' => 'Tienda',
            'product' => $product
        ];

        // Cargar el contenido del archivo JSON
        $jsonItems = file_get_contents('res/products.json');
        $itemsData = json_decode($jsonItems, true);

        // Verificar si el producto existe en el JSON
        if (isset($itemsData[$product])) {
            // Obtener los datos del producto desde el JSON
            $product = $itemsData[$product];
        } else {
            // Manejar el caso en que el producto no se encuentre
            return $response->withStatus(404)->getBody()->write('Producto no encontrado');
        }

        return $view->render($response, 'product_details.html', [
            'title' => $params['title'],
            'product' => $product
        ]);
    }

}
