<?php
	use App\Lib\Response;
	// use App\Lib\MiddlewareToken;
	require_once './core/defines.php';

	$app->group('/intento/', function() {
		$this->get('', function($request, $response, $arguments) {
			return $response->withHeader('Content-type', 'text/html')->write('Soy ruta de intento');
		})/* ->add( new MiddlewareToken() ) */;

		$this->get('get/{id}', function($request, $response, $arguments) {
			return $response->withJson($this->model->intento->get($arguments['id']));
		})/* ->add( new MiddlewareToken() ) */;

		$this->get('getAll/[{pagina}/{limite}]', function($request, $response, $arguments) {
			$arguments['pagina'] = isset($arguments['pagina'])? $arguments['pagina']: 0;
			$arguments['limite'] = isset($arguments['limite'])? $arguments['limite']: 0;

			return $response->withJson($this->model->intento->getAll($arguments['pagina'], $arguments['limite']));
		})/* ->add( new MiddlewareToken() ) */;

		$this->post('add/', function($request, $response, $arguments) {
			$this->model->transaction->iniciaTransaccion();
			$parsedBody = $request->getParsedBody();

			// $dataIntento = [ 'ID_url'=>$parsedBody['ID_url'], 'nombre'=>$parsedBody['nombre'], 'empresa'=>$parsedBody['empresa'], 'correo'=>$parsedBody['correo'], 'area'=>$parsedBody['area'], 'cargo'=>$parsedBody['cargo'], 'telefono'=>$parsedBody['telefono'], 'inicio'=>$parsedBody['inicio'], 'final'=>$parsedBody['final'] ];
			$dataIntento = [ 'ID_url'=>$parsedBody['ID_url'], 'nombre'=>'', 'empresa'=>'', 'correo'=>$parsedBody['correo'], 'area'=>'', 'cargo'=>'', 'telefono'=>'', 'inicio'=>$parsedBody['inicio'], 'final'=>$parsedBody['final'], 'comentarios'=>$parsedBody['comentarios'] ];
			$intento = $this->model->intento->add($dataIntento); if($intento->response) { $ID_intento = $intento->result;
				$dataRespuesta = [ 'ID_intento' => $ID_intento ];
				foreach($parsedBody['respuestas'] as $dataRespuesta) {
					$dataRespuesta['ID_intento'] = $ID_intento;
					$respuesta = $this->model->respuesta->add($dataRespuesta); if(!$respuesta->response) {
						$respuesta->state = $this->model->transaction->regresaTransaccion(); return $response->withJson($respuesta); 
					}
				}

				$body = 
					"<!DOCTYPE html>
					<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\">
						<head>
							<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
							<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
							<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
							<meta name=\"format-detection\" content=\"date=no\">
							<meta name=\"format-detection\" content=\"address=no\">
							<meta name=\"format-detection\" content=\"telephone=no\">
							<title>". SITE_NAME ."</title>
							<link href=\"https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap\" rel=\"stylesheet\">
							<style>
							*{ -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
							body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; background: #e2231a; font-family: 'Poppins', Helvetica Neue, Helvetica, Arial, sans-serif; -webkit-text-size-adjust: none; }
							a { color: #B3B2B1; text-decoration: none; }
							p { padding: 0 !important; margin: 0 !important; }
							strong { font-weight: 500 !important; }
							img { -ms-interpolation-mode: bicubic; }
							@media only screen and (max-width:525px) {
								.mobile-br-15 { height: 15px !important; }
								.m-td, .m-td, .m-td{ display: none !important; width: 0 !important; height: 0 !important; font-size: 0 !important; line-height: 0 !important; min-height: 0 !important; }
								.img-m-center{ text-align: center !important; }
								.text-r-m-center, .text-white-top, .text-white-top{ text-align: center !important; }
								.fluid-img img, .fluid-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }
								.mobile-shell { width: 100% !important; min-width: 100% !important; }
								.center { margin: 0 auto; }
								.td{ width: 100% !important; min-width: 100% !important; }
								.content-spacing { width: 15px !important; }
							}
							</style>
						</head>
						<body class=\"body\" style=\"padding:0 !important; margin:0 !important; display:block !important; min-width:100% !important; width:100% !important; background:#ededed; -webkit-text-size-adjust:none;\">
							<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ededed\">
								<tr>
									<td align=\"center\" valign=\"top\">
										<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#E2231A\">
											<tr>
												<td align=\"center\">
													<table width=\"650\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"mobile-shell\">
														<tr>
															<td class=\"td\" style=\"width:650px; min-width:650px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;\">
																<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
																	<tr>
																		<td>
																			<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">
																				<tr>
																					<td height=\"30\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">&nbsp;</td>
																				</tr>
																			</table>
																			<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">
																				<tr>
																					<td height=\"25\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">&nbsp;</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
										<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ededed\">
											<tr>
												<td valign=\"top\" class=\"m-td\" style=\"font-size:0pt; line-height:0pt; text-align:left\">
													<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#E2231A\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">
														<tr>
															<td bgcolor=\"#E2231A\" height=\"190\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">&nbsp;</td>
														</tr>
													</table>
												</td>
												<td width=\"650\" align=\"center\">
													<table width=\"650\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"mobile-shell\" bgcolor=\"#ffffff\">
														<tr>
															<td class=\"td\" style=\"width:650px; min-width:650px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;\">
																<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
																	<tr>
																		<td class=\"img\" style=\"font-size:0pt; line-height:0pt; text-align:left\" width=\"10\"></td>
																		<td>
																			<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">
																				<tr>
																					<td height=\"50\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">&nbsp;</td>
																				</tr>
																			</table>
																			<div style=\"font-size:0pt; line-height:0pt;\">
																				<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\">
																					<img src=\"". URL_ROOT ."/assets/images/Logo_ATM.png\" border=\"0\" width=\"300\" height=\"190\" alt=\"\" />
																				</div>
																			</div>
																			<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">
																				<tr>
																					<td height=\"40\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">&nbsp;</td>
																				</tr>
																			</table>
																		</td>
																		<td class=\"img\" style=\"font-size:0pt; line-height:0pt; text-align:left\" width=\"10\"></td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
													<table width=\"650\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"mobile-shell\">
														<tr>
															<td>
																<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">
																	<tr>
																		<td class=\"content-spacing\" style=\"font-size:0pt; line-height:0pt; text-align:left\" width=\"30\"></td>
																		<td>
																			<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">
																				<tr>
																					<td height=\"40\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">&nbsp;</td>
																				</tr>
																			</table>
																			<div style=\"color:#555555; font-family: 'Poppins', Helvetica Neue, Helvetica, Arial, sans-serif; font-size:26px; line-height:34px; text-align:center; font-weight: 500;\">Gracias por tu tiempo!</div>
																			<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">
																				<tr>
																					<td height=\"30\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">&nbsp;</td>
																				</tr>
																			</table>
																			<div style=\"color:#555555; font-family: 'Poppins', Helvetica Neue, Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:left\">
																				<span style=\"font-weight: 500; font-size:16px;\">RESUMEN DE LA ENCUESTA</span><br><br>";
																				foreach($_SESSION['encuestas'][$parsedBody['code']]->preguntas as $pregunta):
																				$body .= "<span style=\"font-weight: 500; font-size:14px;\">". $pregunta->pregunta ."</span><br>
																				<span>Respuesta: <b>". ($pregunta->temp_value==0? 'PESIMO (1)': ($pregunta->temp_value==1? 'MAL (2)': ($pregunta->temp_value==2? 'NORMAL (3)': ($pregunta->temp_value==3? 'BIEN (4)': 'EXCELENTE (5)')))) ."</b></span><br><br>";
																				endforeach;
																			$body .= "</div>
																			<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">
																				<tr>
																					<td height=\"20\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">&nbsp;</td>
																				</tr>
																			</table>
																			<div style=\"color:#555555; font-family: 'Poppins', Helvetica Neue, Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:left\">
																				<span style=\"font-weight: 500; font-size:16px;\">COMENTARIOS</span><br><br>
																				<span>".$parsedBody['comentarios']."</span>
																			</div>
																			<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">
																				<tr>
																					<td height=\"40\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">&nbsp;</td>
																				</tr>
																			</table>
																		</td>
																		<td class=\"content-spacing\" style=\"font-size:0pt; line-height:0pt; text-align:left\" width=\"30\"></td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td>
																<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">
																	<tr>
																		<td height=\"20\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">&nbsp;</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
												<td valign=\"top\" class=\"m-td\" style=\"font-size:0pt; line-height:0pt; text-align:left\">
													<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#E2231A\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">
														<tr>
															<td bgcolor=\"#E2231A\" height=\"190\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">&nbsp;</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
										<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
											<tr>
												<td align=\"center\">
													<table width=\"650\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"mobile-shell\">
														<tr>
															<td class=\"td\" style=\"width:650px; min-width:650px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;\">
																<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
																	<tr>
																		<td align=\"center\">
																			<div class=\"text-r-m-center\" style=\"color:#777777; font-family: 'Poppins', Helvetica Neue, Helvetica, Arial, sans-serif; font-size:14px; line-height:24px; text-align:center\">
																				<a href=\"https://trasladosuniversales.com.mx/\" target=\"_blank\" style=\"color:#666666; text-decoration:underline\"><span style=\"color:#666666; text-decoration:underline\">https://trasladosuniversales.com.mx/</span></a>
																				<br />
																				&copy; ". date('Y') ." ". SITE_NAME ." - Todos los derechos reservados
																			</div>
																		</td>
																	</tr>
																</table>
																<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">
																	<tr>
																		<td height=\"40\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">&nbsp;</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
										<div style=\"font-size:0pt; line-height:0pt;\" class=\"mobile-br-15\"></div>
									</td>
								</tr>
							</table>
						</body>
					</html>";
				$intento->email = $this->model->intento->sendEmail($parsedBody['correo'], "Gracias por tu tiempo", $body);
			} else { $intento->state = $this->model->transaction->regresaTransaccion(); return $response->withJson($intento); }

			unset($_SESSION['encuestas'][$parsedBody['code']]);
			$intento->state = $this->model->transaction->confirmaTransaccion();
			return $response->withJson($intento);
		});
	});
?>