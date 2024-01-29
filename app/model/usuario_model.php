<?php
	namespace App\Model;

	use PDOException;
	use App\Lib\Response;

	class UsuarioModel {
		private $db;
		private $table = 'usuario_tusamovil';
		private $response;
		
		public function __CONSTRUCT($db) {
			$this->db = $db;
		}

		public function login($username, $password) {

			$this->response = new Response();
			$password = strrev(md5(sha1($password)));

			$usuario = $this->db
				->from($this->table)
				->where(is_numeric($username)? "fk_matricula = $username": "(usuario='$username' OR email='$username')")
				->where("password = '$password'")
				->where('tipo_cliente IN (4, 5, 6)')
				->where('activo = 1')
				->fetch();

			if(is_object($usuario)) {
				unset($usuario->password);
				$usuario->usuario = utf8_encode($usuario->usuario);
				$this->addSessionLogin($usuario);

				$this->response->SetResponse(true, 'acceso correcto'); 
			} else {
				$this->response->SetResponse(false, 'verifica tus datos'); 
			}

			$this->response->result = $usuario;
			return $this->response;
		}

		public function addSessionLogin($usuario){
			require_once './core/defines.php';
			$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
			$_SESSION['navegador'] = $_SERVER['HTTP_USER_AGENT'];
			$_SESSION['usuario'] = $usuario;
			$_SESSION['iniciada'] = date('Y-m-d H:i:s');
		}

		/* public function crearToken($usuario) {
			$JWT = new \App\Lib\JWT();
			$datos = [
				'nbf' => time(),
				'aud' => SITE_NAME,
				'id_usuario' => $usuario->id_login,
			];

			return $JWT->crearToken(json_encode($datos));
		} */

		public function logout() {
			$this->response = new Response();
			session_unset();
			session_regenerate_id(true);
			session_destroy();

			return $this->response;
		}
	}
?>