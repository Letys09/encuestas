<?php
	namespace App\Model;

	use PDOException;
	use App\Lib\Response;

	class EncuestaModel {
		private $db;
		private $table = 'enc_encuesta';
		private $tableU = 'enc_universo';
		private $response;

		public function __CONSTRUCT($db) {
			$this->db = $db;
		}

		public function getByNombre($nombre) {
			$this->response = new Response();
			$this->response->result = $this->db
				->from($this->table)
				->where("nombre", $nombre)
				->where("status", 1)
				->fetchAll();

			if(count($this->response->result) > 0) { 
				foreach($this->response->result as &$encuesta) {
					//$encuesta->nombre = utf8_decode($encuesta->nombre);
					$encuesta->nombre = utf8_decode($encuesta->nombre);
				}

				$this->response->SetResponse(true); 
			} else { $this->response->SetResponse(false, 'NO se encontró ningun registro'); }

			return $this->response;
		}

		public function get($ID_encuesta) {
			$this->response = new Response();
			$this->response->result = $this->db
				->from($this->table)
				->where("ID_encuesta", $ID_encuesta)
				->fetch();

			//if($this->response->result) { $this->response->result->nombre = utf8_decode($this->response->result->nombre); $this->response->SetResponse(true); }
			if($this->response->result) { $this->response->result->nombre = $this->response->result->nombre; $this->response->SetResponse(true); }	
			else { $this->response->SetResponse(false, 'no existe el registro'); }

			return $this->response;
		}

		public function getAll($pagina=0, $limite=0, $busqueda='0', $status=0) {
			$this->response = new Response();
			$busqueda = $busqueda!='0'? $busqueda: '_';
			if($limite == 0) {
				$this->response->result = $this->db
					->from($this->table)
					->select("num_preguntas")
					->innerJoin("(SELECT ID_encuesta, COUNT(*) AS num_preguntas FROM $this->tableU GROUP BY ID_encuesta) $this->tableU ON $this->table.ID_encuesta = $this->tableU.ID_encuesta")
					->where("CONCAT(CASE WHEN LEN($this->table.ID_encuesta)<3 THEN CONCAT('P-', RIGHT(CONCAT('00', $this->table.ID_encuesta), 3)) ELSE CONCAT('P-', $this->table.ID_encuesta) END, ' ', FORMAT(fecha, 'dd/MM/yyyy'), ' ', nombre, ' ', num_preguntas, ' ', CASE WHEN status=1 THEN 'activo' ELSE 'inactivo' END) LIKE '%$busqueda%'")
					->where("status ".($status!=0? "=": ">")." $status")
					->fetchAll();
			} else {
				$inicial = $pagina * $limite;
				$this->response->result = $this->db
					->from($this->table)
					->select("num_preguntas")
					->innerJoin("(SELECT ID_encuesta, COUNT(*) AS num_preguntas FROM $this->tableU GROUP BY ID_encuesta) $this->tableU ON $this->table.ID_encuesta = $this->tableU.ID_encuesta")
					->where("CONCAT(CASE WHEN LEN($this->table.ID_encuesta)<3 THEN CONCAT('P-', RIGHT(CONCAT('00', $this->table.ID_encuesta), 3)) ELSE CONCAT('P-', $this->table.ID_encuesta) END, ' ', FORMAT(fecha, 'dd/MM/yyyy'), ' ', nombre, ' ', num_preguntas, ' ', CASE WHEN status=1 THEN 'activo' ELSE 'inactivo' END) LIKE '%$busqueda%'")
					->where("status ".($status!=0? "=": ">")." $status")
					->offset("$inicial ROWS FETCH NEXT $limite ROWS ONLY")
					->fetchAll();
			}
			foreach($this->response->result as &$encuesta) {
				//$encuesta->nombre = utf8_decode($encuesta->nombre);
				$encuesta->nombre = $encuesta->nombre;
			}

			$this->response->total = $this->db
				->from($this->table)
				->select(null)->select('COUNT(*) Total')
				->where("status ".($status!=0? "=": ">")." $status")
				->fetch()
				->Total;

			return $this->response->SetResponse(true);
		}

		public function getAllDataTables($inicial, $limite, $busqueda, $orden='nombre asc') {
			$this->response = new Response();
			$busqueda = $busqueda!='0'? $busqueda: '_';
			$this->response->result = $this->db
				->from($this->table)
				->select("num_preguntas")
				->innerJoin("(SELECT ID_encuesta, COUNT(*) AS num_preguntas FROM $this->tableU GROUP BY ID_encuesta) $this->tableU ON $this->table.ID_encuesta = $this->tableU.ID_encuesta")
				->where("CONCAT(CASE WHEN LEN($this->table.ID_encuesta)<3 THEN CONCAT('P-', RIGHT(CONCAT('00', $this->table.ID_encuesta), 3)) ELSE CONCAT('P-', $this->table.ID_encuesta) END, ' ', FORMAT(fecha, 'dd/MM/yyyy'), ' ', nombre, ' ', num_preguntas, ' ', CASE WHEN status=1 THEN 'activo' ELSE 'inactivo' END) LIKE '%$busqueda%'")
				->where("status > 0")
				->orderBy($orden)
				->offset("$inicial ROWS FETCH NEXT $limite ROWS ONLY")
				->fetchAll();
			foreach($this->response->result as &$encuesta) {
				//$encuesta->nombre = utf8_decode($encuesta->nombre);
				$encuesta->nombre = $encuesta->nombre;
			}

			$this->response->total = $this->db
				->from($this->table)
				->select(NULL)->select('COUNT(*) AS total')
				->where("status > 0")
				->fetch()
				->total;

			$this->response->filtered = $this->db
				->from($this->table)
				->select(NULL)->select('COUNT(*) AS total')
				->innerJoin("(SELECT ID_encuesta, COUNT(*) AS num_preguntas FROM $this->tableU GROUP BY ID_encuesta) $this->tableU ON $this->table.ID_encuesta = $this->tableU.ID_encuesta")
				->where("CONCAT(CASE WHEN LEN($this->table.ID_encuesta)<3 THEN CONCAT('P-', RIGHT(CONCAT('00', $this->table.ID_encuesta), 3)) ELSE CONCAT('P-', $this->table.ID_encuesta) END, ' ', FORMAT(fecha, 'dd/MM/yyyy'), ' ', nombre, ' ', num_preguntas, ' ', CASE WHEN status=1 THEN 'activo' ELSE 'inactivo' END) LIKE '%$busqueda%'")
				->where("status > 0")
				->fetch()
				->total;

			return $this->response->SetResponse(true);
		}

		public function add($data) {
			$this->response = new Response(); $fields = array(); $values = array(); 
			//foreach($data as $field => $value) { $fields[] = $field; $values[] = "'".utf8_encode($value)."'"; }
			foreach($data as $field => $value) { $fields[] = $field; $values[] = "'".$value."'"; }
			try{
				$this->response->result = $this->db->getPdo()->prepare("INSERT INTO $this->table(".implode(', ', $fields).") VALUES(".implode(', ', $values).")")->execute();

				if($this->response->result!=0) { 
					$this->response->result = $this->db->getPdo()->query("SELECT MAX(ID_encuesta) AS ID_encuesta FROM $this->table")->fetch()->ID_encuesta; 
					$this->response->SetResponse(true, 'id del registro: '.$this->response->result); 
				} else { $this->response->SetResponse(false, 'no se inserto el registro'); }
			} catch(\PDOException $ex) {
				$this->response->errors = $ex;
				$this->response->SetResponse(false, "catch: add model $this->table");
			}

			return $this->response;
		}

		public function edit($data, $ID_encuesta) {
			$this->response = new Response();
			$this->response->result = true;
			try{
				$orgInfo = $this->get($ID_encuesta)->result; $areTheSame = true;
				foreach($orgInfo as $field => $value) { if(isset($data[$field]) && $data[$field] != $value) { $areTheSame = false; break; } }
				if(!$areTheSame) {
					//foreach($data as $field => $value) { $data[$field] = utf8_encode($value); }
					foreach($data as $field => $value) { $data[$field] = $value; }
					$this->response->result = $this->db
						->update($this->table, $data)
						->where('ID_encuesta', $ID_encuesta)
						->execute();
				}

				if($this->response->result)	{ $this->response->SetResponse(true, 'id actualizado: '.$ID_encuesta); }
				else { $this->response->SetResponse(false, 'no se edito el registro'); }
			} catch(\PDOException $ex) {
				$this->response->errors = $ex;
				$this->response->SetResponse(false, "catch: edit model $this->table");
			}

			return $this->response;
		}

		public function del($ID_encuesta) {
			$this->response = new Response();
			$this->response->result = true;
			try{
				$data['status'] = 0;
				$orgStatus = $this->get($ID_encuesta)->result->status;
				if($orgStatus != $data['status']) {
					$this->response->result = $this->db
						->update($this->table, $data)
						->where('ID_encuesta', $ID_encuesta)
						->execute();
				}

				if($this->response->result)	{ $this->response->SetResponse(true, 'id baja: '.$ID_encuesta); }
				else { $this->response->SetResponse(false, 'no se dio de baja el registro'); }
			} catch(\PDOException $ex) {
				$this->response->errors = $ex;
				$this->response->SetResponse(false, "catch: del model $this->table");
			}

			return $this->response;
		}
	}
?>