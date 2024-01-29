<?php
	use App\Lib\Response;
	// use App\Lib\MiddlewareToken;
	require_once './core/defines.php';
 
	$app->group('/encuesta/', function() {
		$this->get('', function($request, $response, $arguments) {
			return $response->withHeader('Content-type', 'text/html')->write('Soy ruta de encuesta');
		});

		$this->get('getByNombre/{nombre}', function($request, $response, $arguments) {
			return $response->withJson($this->model->encuesta->getByNombre($arguments['nombre']));
		});

		$this->get('get/{id}', function($request, $response, $arguments) {
			$encuesta = $this->model->encuesta->get($arguments['id']);
			if($encuesta->response) {
				$encuesta->preguntas = $this->model->universo->getByEncuesta($arguments['id'])->result;
			}

			return $response->withJson($encuesta);
		});

		$this->get('getAll/[{pagina}/{limite}[/{busqueda}[/{status}]]]', function($request, $response, $arguments) {
			$arguments['pagina'] = isset($arguments['pagina'])? $arguments['pagina']: 0;
			$arguments['limite'] = isset($arguments['limite'])? $arguments['limite']: 0;
			$arguments['busqueda'] = isset($arguments['busqueda'])? $arguments['busqueda']: 0;
			$arguments['status'] = isset($arguments['status'])? $arguments['status']: 0;

			return $response->withJson($this->model->encuesta->getAll($arguments['pagina'], $arguments['limite'], $arguments['busqueda'], $arguments['status']));
		});

		$this->get('getAllDataTables/{inicial}/{limite}/{busqueda}', function($request, $response, $arguments) {
			$inicial = isset($_GET['start'])? $_GET['start']: $arguments['inicial'];
			$limite = isset($_GET['length'])? $_GET['length']: $arguments['limite']; $limite = $limite>0? $limite: 10;
			$busqueda = isset($_GET['search']['value'])? (strlen($_GET['search']['value'])>0? $_GET['search']['value']: '_'): $arguments['busqueda'];
			$orden = isset($_GET['order'])? $_GET['columns'][$_GET['order'][0]['column']]['data']: 'nombre';
			$orden .= isset($_GET['order'])? " ".$_GET['order'][0]['dir']: " asc";

			if(count($_GET['order'])>1){
				for ($i=1; $i < count($_GET['order']); $i++) { 
					$orden .= ', '.$_GET['columns'][$_GET['order'][$i]['column']]['data'].' '.$_GET['order'][$i]['dir'];
				}
			}

			$encuestas = $this->model->encuesta->getAllDataTables($inicial, $limite, $busqueda, $orden);

			$data = [];
			if(!isset($_SESSION)) { session_start(); }
			foreach($encuestas->result as $encuesta) {
				$acciones = '<a href="#" data-toogle="tooltip" data-placement="top" title="Editar" class="btnEditEncuesta"><i class="mdi mdi-pencil"></i></a> ';
				$acciones .= '<a href="#" data-toogle="tooltip" data-placement="top" title="Eliminar" class="btnDelEncuesta"><i class="mdi mdi-delete" style="color:red;"></i></a>';

				$status = $encuesta->status == 1;
				$folio = $encuesta->ID_encuesta;
				$folio = "E-".(strlen($folio)<3? str_pad($folio, 3, '0', STR_PAD_LEFT): $folio);
				$data[] = array(
					"ID_encuesta" => "<small class=\"folio\">$folio</small>",
					"fecha" => "<small class=\"creacion\">".date('d/m/Y', strtotime($encuesta->fecha))."</small>",
					"nombre" => "<small class=\"nombre\">".mb_strtoupper($encuesta->nombre)."</small>",
					"num_preguntas" => "<small class=\"preguntas\">$encuesta->num_preguntas</small>",
					"status" => "<small class=\"status\"><span class=\"label label-".($status? 'success': 'warning')."\">".($status? 'Activo': 'Inactivo')."</span><a href=\"#\" class=\"btn".($status? 'Baja': 'Alta')." text-".($status? 'danger': 'success')." pull-right\" data-toogle=\"tooltip\" data-placement=\"top\" title=\"".($status? 'Inactivar': 'Activar')."\"><i class=\"mdi mdi-".($status? 'close': 'check')."-circle\"></i></a></small>",
					"acciones" => "<div class=\"pull-right acciones\">$acciones</div>",
					"data_id" => $encuesta->ID_encuesta,
				);
			}

			echo json_encode(array(
				'draw' => $_GET['draw'],
				'data' => $data,
				'recordsTotal' => $encuestas->total,
				'recordsFiltered' => $encuestas->filtered,
			));
			exit(0);
		});

		$this->post('add/', function($request, $response, $arguments) {
			$this->model->transaction->iniciaTransaccion();
			$parsedBody = $request->getParsedBody();
			if(!isset($parsedBody['fecha'])) { $parsedBody['fecha'] = date('Y-m-d\TH:i:s'); }
			if(!isset($parsedBody['status'])) { $parsedBody['status'] = 1; }

			$data = ['nombre' => $parsedBody['nombre'], 'fecha'=>$parsedBody['fecha'], 'status'=>$parsedBody['status'] ];
			$encuesta = $this->model->encuesta->add($data); if($encuesta->response) { $ID_encuesta = $encuesta->result;
				$data = [ 'ID_encuesta' => $ID_encuesta ];
				foreach($parsedBody['detalles'] as $detalle) {
					$data['ID_pregunta'] = $detalle;
					$universo = $this->model->universo->add($data); if(!$universo->response) {
						$universo->state = $this->model->transaction->regresaTransaccion(); return $response->withJson($universo);
					}
				}
			} else { $encuesta->state = $this->model->transaction->regresaTransaccion(); return $response->withJson($encuesta); }

			$encuesta->state = $this->model->transaction->confirmaTransaccion();
			return $response->withJson($encuesta);
		});

		$this->post('edit/{id}', function($request, $response, $arguments) {
			return $response->withJson($this->model->encuesta->edit($request->getParsedBody(), $arguments['id']));
		});

		$this->post('del/{id}', function($request, $response, $arguments) {
			return $response->withJson($this->model->encuesta->del($arguments['id']));
		});

		$this->get('export/{format}/{busqueda}', function($request, $response, $arguments) {
			$encuestas = $this->model->encuesta->getAll(0, 0, $arguments['busqueda'])->result;
			if($arguments['format'] == 'xlsx') {
				return $this->rpt_renderer->render($response, 'xlsxEncuestas.phtml', ['encuestas'=>$encuestas]);
			} elseif($arguments['format'] == 'pdf') {
				return $this->rpt_renderer->render($response, 'pdfEncuestas.phtml', ['encuestas'=>$encuestas, 'vista'=>'rpt encuestas']);
			}
		});
	})/* ->add( new MiddlewareToken() ) */;
?>