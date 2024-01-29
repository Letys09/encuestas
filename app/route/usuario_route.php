<?php
	use App\Lib\Response;
	// use App\Lib\MiddlewareToken;
	require_once './core/defines.php';

	$app->group('/usuario/', function() use ($app) {
		$this->get('', function($request, $response, $arguments) {
			return $response->withHeader('Content-type', 'text/html')->write('Soy ruta de usuario');
		});

		$this->post('login/', function($request, $response, $arguments) {
			$parsedBody= $request->getParsedBody();
			$email= $parsedBody['email'];
			$contrasena = $parsedBody['contrasena'];

			$usuario = $this->model->usuario->login($email, $contrasena);
			if($usuario->response) {
				//$_SESSION['token'] = $this->model->usuario->crearToken($usuario->result);

				$this->logger->info("Slim-Skeleton 'usuario/login/' ".$usuario->result->id_login);
			}

			return $response->withJson($usuario);
		});

		$this->get('logout', function($request, $response, $arguments) use ($app) {
			$userId = $_SESSION['usuario']->id_login;
			$this->logger->info("Slim-Skeleton 'usuario/logout/' $userId");
			$this->model->usuario->logout();

			return $this->response->withRedirect('../login');
		});
	});
?>