<?php
	use App\Lib\Response;
	// use App\Lib\MiddlewareToken;
	require_once './core/defines.php';

	$app->group('/pregunta/', function() {
		$this->get('', function($request, $response, $arguments) {
			return $response->withHeader('Content-type', 'text/html')->write('Soy ruta de pregunta');
		});

		$this->get('getByPregunta/{pregunta}', function($request, $response, $arguments) {
			return $response->withJson($this->model->pregunta->getByPregunta($arguments['pregunta']));
		});

		$this->get('get/{id}', function($request, $response, $arguments) {
			return $response->withJson($this->model->pregunta->get($arguments['id']));
		});

		$this->get('getAll/[{pagina}/{limite}]', function($request, $response, $arguments) {
			$arguments['pagina'] = isset($arguments['pagina'])? $arguments['pagina']: 0;
			$arguments['limite'] = isset($arguments['limite'])? $arguments['limite']: 0;

			return $response->withJson($this->model->pregunta->getAll($arguments['pagina'], $arguments['limite']));
		});

		$this->get('getAllDataTables/{inicial}/{limite}/{busqueda}[/{status}]', function($request, $response, $arguments) {
			$inicial = isset($_GET['start'])? $_GET['start']: $arguments['inicial'];
			$limite = isset($_GET['length'])? $_GET['length']: $arguments['limite']; $limite = $limite>0? $limite: 10;
			$busqueda = isset($_GET['search']['value'])? (strlen($_GET['search']['value'])>0? $_GET['search']['value']: '_'): $arguments['busqueda'];
			$orden = isset($_GET['order'])? $_GET['columns'][$_GET['order'][0]['column']]['data']: 'nombre';
			$orden .= isset($_GET['order'])? " ".$_GET['order'][0]['dir']: " asc";
			$status = isset($arguments['status'])? $arguments['status']: 0;
			
			if(count($_GET['order'])>1){
				for ($i=1; $i < count($_GET['order']); $i++) { 
					$orden .= ', '.$_GET['columns'][$_GET['order'][$i]['column']]['data'].' '.$_GET['order'][$i]['dir'];
				}
			}

			$preguntas = $this->model->pregunta->getAllDataTables($inicial, $limite, $busqueda, $orden, $status);

			$data = [];
			if(!isset($_SESSION)) { session_start(); }
			foreach($preguntas->result as $pregunta) {
				$acciones = $pregunta->status==1? '<a href="#" data-toogle="tooltip" data-placement="top" title="Ver encuestas" class="btnVerEncuestas"><i class="mdi mdi-eye" style="color: green;"></i></a> ': '';
				$acciones .= '<a href="#" data-toogle="tooltip" data-placement="top" title="Editar" class="btnEditPregunta"><i class="mdi mdi-pencil"></i></a> ';
				$acciones .= '<a href="#" data-toogle="tooltip" data-placement="top" title="Eliminar" class="btnDelPregunta"><i class="mdi mdi-delete" style="color:red;"></i></a>';

				$status = $pregunta->status == 1;
				$folio = $pregunta->ID_pregunta;
				$folio = "P-".(strlen($folio)<3? str_pad($folio, 3, '0', STR_PAD_LEFT): $folio);
				$data[] = array(
					"ID_pregunta" => "<small class=\"folio\">$folio</small>",
					"fecha" => "<small class=\"agregada\">".date('d/m/Y', strtotime($pregunta->fecha))."</small>",
					"pregunta" => "<small class=\"pregunta\">$pregunta->pregunta</small>",
					"status" => "<small class=\"status\"><span class=\"label label-".($status? 'success': 'warning')."\">".($status? 'Activo': 'Inactivo')."</span><a href=\"#\" class=\"btn".($status? 'Baja': 'Alta')." text-".($status? 'danger': 'success')." pull-right\" data-toogle=\"tooltip\" data-placement=\"top\" title=\"".($status? 'Inactivar': 'Activar')."\"><i class=\"mdi mdi-".($status? 'close': 'check')."-circle\"></i></a></small>",
					"acciones" => "<div class=\"pull-right acciones\">$acciones</div>",
					"acciones_encuesta" => "<div class=\"acciones pull-right\"><a href=\"#\" data-popup=\"tooltip\" title=\"Agregar\" class=\"btn btn-xs btn-success btnAddPregunta\"><i class=\"mdi fa-lg mdi-plus\"></i></a></div>",
					"data_id" => $pregunta->ID_pregunta,
				);
			}

			echo json_encode(array(
				'draw' => $_GET['draw'],
				'data' => $data,
				'recordsTotal' => $preguntas->total,
				'recordsFiltered' => $preguntas->filtered,
			));
			exit(0);
		});

		$this->post('add/', function($request, $response, $arguments) {
			$parsedBody = $request->getParsedBody();
			if(!isset($parsedBody['fecha'])) { $parsedBody['fecha'] = date('Y-m-d\TH:i:s'); }
			if(!isset($parsedBody['status'])) { $parsedBody['status'] = 1; }

			// return $response->withJson($this->model->pregunta->add($parsedBody));
			return $response->withHeader('Content-type', 'application/json')->write(json_encode($this->model->pregunta->add($parsedBody)));
		});

		$this->post('edit/{id}', function($request, $response, $arguments) {
			return $response->withJson($this->model->pregunta->edit($request->getParsedBody(), $arguments['id']));
		});

		$this->post('del/{id}', function($request, $response, $arguments) {
			return $response->withJson($this->model->pregunta->del($arguments['id']));
		});

		$this->get('export/{format}/{busqueda}', function($request, $response, $arguments) {
			$preguntas = $this->model->pregunta->getAll(0, 0, $arguments['busqueda'])->result;
			if($arguments['format'] == 'xlsx') {
				return $this->rpt_renderer->render($response, 'xlsxPreguntas.phtml', ['preguntas'=>$preguntas]);
			} elseif($arguments['format'] == 'pdf') {
				return $this->rpt_renderer->render($response, 'pdfPreguntas.phtml', ['preguntas'=>$preguntas, 'vista'=>'rpt preguntas']);
			}
		});
	})/* ->add( new MiddlewareToken() ) */;
?>