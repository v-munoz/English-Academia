<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class CheckoutController
{
    /*
    Función POST '/add-to-cart' para añadir items al carrito
    */
    function addItemToCart(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $view = Twig::fromRequest($request);

        // Inicializar el ID desde la sesión o como 0 si no hay datos
        $idItem = isset($_SESSION['lastId']) ? $_SESSION['lastId'] : 0;

        // Inicializar el carrito desde la sesión o como un array vacío si no hay datos
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        // Validar y almacenar datos a partir de los 3 valores presentes en todos los productos (quantity siempre se inicia en 1)
        if (!empty($data['product']) && !empty($data['price']) && !empty($data['quantity'])) {
            // Crear una clave única para evitar duplicados de productos en el carrito (nombre + {opcion + talla})
            $uniqueKey = $data['product'] .
                (isset($data['option']) ? '-' . $data['option'] : '') .
                (isset($data['size']) ? '-' . $data['size'] : '');

            // Buscar si el producto ya existe
            $itemInCart = array_key_exists($uniqueKey, $cart);
            // Si el producto existe, actualizar su cantidad
            if ($itemInCart) {
                $cart[$uniqueKey]['quantity'] += intval($data['quantity']);
            } else {
                // Crear array con la información de la compra
                // Datos obligatorios
                $item = [
                    'id' => $idItem,
                    'product' => $data['product'],
                    'price' => floatval($data['price']),
                    'quantity' => intval($data['quantity']),
                    'image' => $data['image'],
                ];
                // Datos opcionales
                // Añadir la talla, si existe
                if (!empty($data['size'])) {
                    $item['size'] = $data['size'];
                }
                // Añadir la opción seleccionada, si existe
                if (!empty($data['option'])) {
                    $item['option'] = $data['option'];
                }

                // Incrementar el ID para el siguiente producto
                $idItem++;
                // Agregar el producto al carrito
                $cart[$uniqueKey] = $item;
            }

            // Almacenar la última ID y los datos del carrito en la sesión
            $_SESSION['lastId'] = $idItem;
            $_SESSION['cart'] = $cart;

            // Consola: mostrar arrays carrito y data
            echo ("Sesion:");
            var_dump($_SESSION['cart']);
            echo ("Data:");
            var_dump($data);
            return $response;
        }

        // Devolver respuesta de error
        $response->getBody()->write(json_encode(['success' => false]));
        return $response->withHeader('Content-Type', 'application/json');
    }


    /*
    Función POST '/send-checkout' para crear la tabla con el pedido y enviarlo
    */
    function sendCheckout(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        // Añadir fecha de compra e ID único
        $data['date'] = date('d-m-Y H:i:s');
        $data['id'] = uniqid('', true);

        // Inicializar el carrito desde la sesión o como un array vacío si no hay datos
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        // Renderizar la plantilla Twig con los datos de compra
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../views/mails');
        $twig = new \Twig\Environment($loader);
        
        // Enviar un email al cliente con el resumen de la operación
        try{
            $mail = new PHPMailer();
            $mail->CharSet = "UTF-8";

            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
            $mail->isSMTP();
            $mail->Host = 'HOST_NAME';
            $mail->SMTPAuth = true;
            $mail->Username = 'SENDER_EMAIL';
            $mail->Password = 'PASSWORD';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            //Recipients
            $mail->setFrom('SENDER_EMAIL', 'NAME');
            $mail->addAddress($data['email']);
            
            // HTML con el contenido del email
            $mail->isHTML(true);
            $mail->Subject = "Resumen de su pedido";
            $mail->Body = $twig->render('Checkout_Client.html', [
                'order' => $data,
                'cart' => $cart
            ]);
        
            $mail->send();
            
        } catch (Exception $e) {
            echo "El mensaje no se pudo enviar: {$mail->ErrorInfo}";
        }

        // Enviar un email al server
        try{
            $mail = new PHPMailer();
            $mail->CharSet = "UTF-8";

            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
            $mail->isSMTP();
            $mail->Host = 'HOST_NAME';
            $mail->SMTPAuth = true;
            $mail->Username = 'SENDER_EMAIL';
            $mail->Password = 'PASSWORD';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            //Recipients
            $mail->setFrom('SENDER_EMAIL', 'NAME');
            $mail->addAddress($data['email']);
            
            // HTML con el contenido del email
            $mail->isHTML(true);
            $mail->Subject = "Nuevo pedido";
            $mail->Body = $twig->render('Checkout_Server.html', [
                'order' => $data,
                'cart' => $cart,
            ]);
        
            $mail->send();
            
        } catch (Exception $e) {
            echo "El mensaje no se pudo enviar: {$mail->ErrorInfo}";
        }

        // Resetear la última ID y los datos del carrito en la sesión
        $_SESSION['lastId'] = 0;
        $_SESSION['cart'] = [];

        return $twig->render('Checkout_Client.html', [
            'order' => $data,
            'cart' => $cart
        ]);
    }


    /*
    Función POST '/update-item-quantity' con petición AJAX FETCH para actualizar la cantidad en $_SESSION['cart']
    */
    function updateItemQuantity($request, $response)
    {
        $data = $request->getParsedBody();

        // Obtener el carrito actual de $_SESSION
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        // Verificar que los parámetros itemId y nuevaCantidad se han introducido correctamente
        if (isset($data['itemId']) && isset($data['updatedQuantity'])) {
            $itemId = $data['itemId'];
            $updatedQuantity = $data['updatedQuantity'];

            // Buscar si el producto ya existe en el carrito
            $uniqueKey = $data['product'] .
                (isset($data['option']) ? '-' . $data['option'] : '') .
                (isset($data['size']) ? '-' . $data['size'] : '');
            $itemInCart = isset($cart[$uniqueKey]);

            // Verificar si se encontró el producto antes de actualizarlo
            if ($itemInCart) {
                // Actualizar la cantidad del producto específico
                $updatedCart = $cart;
                $updatedCart[$uniqueKey]['quantity'] = $updatedQuantity;

                // Actualizar $_SESSION con el carrito actualizado
                $_SESSION['cart'] = $updatedCart;

                return $response;

            } else {
                return $response;
            }
        } else {
            return $response;
        }
    }



    /*
    Función POST '/delete-item' con petición AJAX FETCH para eliminar items en $_SESSION['cart']
    */
    function deleteItem($request, $response)
    {
        $data = $request->getParsedBody();

        if (isset($data['itemId'])) {
            $itemId = $data['itemId'];
            $uniqueKey = $data['product'] .
                (isset($data['option']) ? '-' . $data['option'] : '') .
                (isset($data['size']) ? '-' . $data['size'] : '');

            // Obtener el carrito actual de $_SESSION
            $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

            // Verificar si el ítem existe en el carrito
            $itemInCart = isset($cart[$uniqueKey]);

            // Verificar si se encontró el ítem
            if ($itemInCart) {
                // Eliminar el elemento del carrito por su clave
                unset($cart[$uniqueKey]);

                // Actualizar $_SESSION con el carrito modificado
                $_SESSION['cart'] = $cart;

                return $response;

            } else {
                return $response;
            }
        } else {
            return $response;
        }
    }
}
