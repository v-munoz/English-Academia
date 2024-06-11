<?php
// Importar app factory & twig
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Selective\BasePath\BasePathMiddleware;

// Importar controllers
use App\Controllers\HomeController;
use App\Controllers\TeacherController;
use App\Controllers\CoursesController;
use App\Controllers\VideosController;
use App\Controllers\EventsController;
use App\Controllers\MultimediaController;
use App\Controllers\StoreController;
use App\Controllers\ContactController;
use App\Controllers\LegalController;
use App\Controllers\CheckoutController;
use App\Controllers\LandingController;



/* BASE CONFIGS */
// Cargar el autoload de Slim
require __DIR__ . '/../vendor/autoload.php';

// Cargar dotenv para el entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Crear la app y definir base_path()
$app = AppFactory::create();
// Establece la ruta base para ejecutar la aplicación en un subdirectorio (de selective/basepath)
$app->setBasePath("/English-Academia");
$app->add(new BasePathMiddleware($app));

/* MIDDLEWARE */
// Añadir el middleware de Slim
$app->addRoutingMiddleware();

// Middleware para parsear JSON
$app->addBodyParsingMiddleware();

//Crear Twig y su Middleware. Pasar cache a true en PROD
$twig = Twig::create(__DIR__ . '/../views', ['cache' => false]); // https://www.slimframework.com/docs/v4/objects/routing.html#route-expressions-caching
$app->add(TwigMiddleware::create($app, $twig));

// Middleware para pasar las variables de $_SESSION a Twig
$app->add(function ($request, $handler) use ($twig) {
    // Obtener las variables de sesión del carrito y el id/contador
    $cart = $_SESSION['cart'] ?? [];
    $idItem = $_SESSION['lastId'] ?? 0;

    // Pasar las variables de sesión a Twig
    $twig->getEnvironment()->addGlobal('cart', $cart);
    $twig->getEnvironment()->addGlobal('idItem', $idItem);

    return $handler->handle($request);
});

// Middleware para el manejo de errores
$app->addErrorMiddleware(true, true, true); // En PROD hay que deshabilitarlo (false, false, false)
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Además, también hay que ajustar el php.ini para que no muestre errores


/* ROUTES */
// Página de Inicio
$app->get('/', HomeController::class . ':index');

// Página de Equipo de profesores
$app->get('/nosotros', TeacherController::class . ':index');

// Página de Cursos
$app->get('/cursos', CoursesController::class . ':index');

// Página de Videos
$app->get('/videos', VideosController::class . ':index');

// Página de eventos
$app->get('/eventos', EventsController::class . ':index');

// Página de Multimedia
$app->get('/multimedia', MultimediaController::class . ':index');

// Página de Tienda
$app->get('/tienda', StoreController::class . ':index')->setName('tienda');

// Detalles del Producto
$app->get("/tienda/{product}", StoreController::class . ':productDetails');

// Controladores del Pedido
$app->post('/add-to-cart', CheckoutController::class . ':addItemToCart');                  // Agregar producto al carrito
$app->post('/update-item-quantity', CheckoutController::class . ':updateItemQuantity');    // Modificar cantidad en carrito
$app->post('/delete-item', CheckoutController::class . ':deleteItem');                     // Eliminar producto del carrito
$app->post('/send-checkout', CheckoutController::class . ':sendCheckout');                 // Enviar tabla con pedido

// Páginas de Contacto y Prensa
$app->get('/contacto', ContactController::class . ':contact');                      // Página de contacto

// Controladores de Contacto
$app->post('/send-newsletter', ContactController::class . ':sendNewsletter');       // Envío formulario de Newsletter
$app->post('/send-contact', ContactController::class . ':sendContactInfo');         // Envío formulario de Contacto

// Páginas de Textos Legales
$app->get('/aviso-legal', LegalController::class . ':legal');
$app->get('/politica-de-cookies', LegalController::class . ':cookies');
$app->get('/politica-de-privacidad', LegalController::class . ':privacy');

// Página de RRSS (Link Tree)
$app->get('/landing', LandingController::class . ':landing')->setName('landing');



/* INICIO */
// Establecer la duración máxima de la sesión en 1 hora (3600 segundos)
ini_set('session.gc_maxlifetime', 3600);

// Iniciar sesión para almacenar variables $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Iniciar la app
$app->run();
