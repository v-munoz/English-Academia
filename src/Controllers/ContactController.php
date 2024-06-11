<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ContactController{
    /*
    Función '/contacto' para cargar la vista del Formulario de Contacto
    */
    function contact (Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $params = ['title' => 'Contacto'];
        return $view->render($response, 'contact.html', $params);
    }

    /*
    Función '/contacto/press' para cargar la vista del Formulario de Contacto
    */
    function press (Request $request, Response $response, $args) {
        $view = Twig::fromRequest($request);
        $params = ['title' => 'Prensa'];
        
        return $view->render($response, 'press.html', $params);
    }
    


    /*
    Función POST '/send-newsletter' para procesar la solicitud de suscripción a la newsletter
    */
    function sendNewsletter(Request $request, Response $response, $args): Response {
        $data = $request->getParsedBody();
        $data['date'] = date('H:i:s d-m-Y');
        
        // Renderizar la plantilla Twig con los datos del usuario
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../views/mails');
        $twig = new \Twig\Environment($loader);
        
        // Enviar un email al cliente con el resumen de la operación
        try{
            $mail = new PHPMailer();
            $mail->CharSet = "UTF-8";

            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
            $mail->isSMTP();
            $mail->Host = 'nachorosa.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@nachorosa.com';
            $mail->Password = 'y@sWr1577';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            

            //Recipients
            $mail->setFrom('noreply@nachorosa.com', 'Nacho Rosa');
            $mail->addAddress($data['email']);
            
            // HTML con el contenido del email
            $mail->isHTML(true);
            $mail->Subject = "Bienvenido a la newsletter de Nacho Rosa";
            $mail->Body = $twig->render('Newsletter_Client.html', ['newsletter' => $data]);
            
        
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
            $mail->Host = 'nachorosa.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@nachorosa.com';
            $mail->Password = 'y@sWr1577';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            //Recipients
            $mail->setFrom('noreply@nachorosa.com', 'Nacho Rosa');
            $mail->addAddress($data['email']);
            
            // HTML con el contenido del email
            $mail->isHTML(true);
            $mail->Subject = "Nueva suscripción a tu newsletter";
            $mail->Body = $twig->render('Newsletter_Server.html', ['newsletter' => $data]);
        
            $mail->send();
            
        } catch (Exception $e) {
            echo "El mensaje no se pudo enviar: {$mail->ErrorInfo}";
        }

        return $response;
    }

    /*
    Función POST '/contacto' para procesar el form de contacto
    */
    function sendContactInfo(Request $request, Response $response, $args): Response {
        $data = $request->getParsedBody();

        $data['date'] = date('d-m-Y H:i:s');
    
        // Renderizar la plantilla Twig con los datos del usuario
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../views/mails');
        $twig = new \Twig\Environment($loader);
        
        // Enviar un email al cliente con el resumen de la operación
        try{
            $mail = new PHPMailer();
            $mail->CharSet = "UTF-8";

            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
            $mail->isSMTP();
            $mail->Host = 'nachorosa.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@nachorosa.com';
            $mail->Password = 'y@sWr1577';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            //Recipients
            $mail->setFrom('noreply@nachorosa.com', 'Nacho Rosa');
            $mail->addAddress($data['email']);
            
            // HTML con el contenido del email
            $mail->isHTML(true);
            $mail->Subject = "Gracias por contactar con Nacho Rosa";
            $mail->Body = $twig->render('Contact_Client.html', ['contactInfo' => $data]);
        
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
            $mail->Host = 'nachorosa.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@nachorosa.com';
            $mail->Password = 'y@sWr1577';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            //Recipients
            $mail->setFrom('noreply@nachorosa.com', 'Nacho Rosa');
            $mail->addAddress($data['email']);
            
            // HTML con el contenido del email
            $mail->isHTML(true);
            $mail->Subject = "Tienes un nuevo mensaje del formulario de contacto";
            $mail->Body = $twig->render('Contact_Server.html', ['contactInfo' => $data]);
        
            $mail->send();
            
        } catch (Exception $e) {
            echo "El mensaje no se pudo enviar: {$mail->ErrorInfo}";
        }
    
        return $response;
    }

}
