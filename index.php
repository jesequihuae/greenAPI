<?php 

	require 'vendor/autoload.php';
	require 'conexion.php';
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$c = new \Slim\Container();

	$c['errorHandler'] = function ($c) {
		return function ($request, $response, $exception) use ($c) {
			$error = array('error' => $exception->getMessage());
		return $c['response']->withStatus(500)
					->withHeader('Content-Type','application/json')
					->write(json_encode($error));
		};
	};

	$app = new \Slim\App($c);

	/**************************************************************
               INICIAN LAS RUTAS DE NUESTRO SERVICIO WEB
	***************************************************************/

	/* Rutas que pertenezcan a la empresa */
	$app->group('/empresa', function(){

		/* Regresa información de la empresa que se está solicitando mediante el ID */
		$this->get('/obtener/{id}',function(Request $request, Response $response, $args){
			$Empresa = Empresa::where('idEmpresa','=',$args['id'])->get();
			if(sizeof($Empresa) == 0){
				$answer = array('Mensaje' => 'No existe el registro');
				return sendBadResponse(json_encode($answer), $response);
			} else {
			return sendOkResponse($Empresa->toJson(), $response);
			}
		});

		/* Realiza el registro de una nueva empresa */
		$this->post('/nueva', function(Request $request, Response $response, $args){
			$datos = $request->getParsedBody();
			$Empresa = new Empresa();
			$Empresa->nombre = $datos['nombre'];
			$Empresa->ubicacion = $datos['ubicacion'];
			if($Empresa->save()){ 
				$answer = array('OK' => 'Se ha registrado Exitosamente');
				return sendOkResponse(json_encode($answer), $response);
			} else {
				$answer = array('Error' => 'Ha ocurrido un error');
				return sendBadResponse(json_encode($answer),$response);
			}
		});
	});

	/* Rutas que pertenecen a Usuarios */
	$app->group('/usuario', function(){

		/* Registro de nuevo usuario */
		$this->post('/nuevo', function(Request $request, Response $response, $args){
			$datosUsuario = $request->getParsedBody();
			$Usuario = new Usuario();
			$Usuario->correo_electronico = $datosUsuario['correo_electronico'];
			$Usuario->contrasena = $datosUsuario['contrasena'];
			$Usuario->empresa_idempresa = $datosUsuario['empresa_idempresa'];
			if($Usuario->save()){
				$answer = array('OK' => 'Se ha registrado Exitosamente');
				return sendOkResponse(json_encode($answer), $response);
			} else {
				$answer = array('Error' => 'Ha ocurrido un error');
				return sendBadResponse(json_encode($answer), $response);
			}
		});

		/* Obtener datos de un usuario en especifico */
		$this->get('/obtener/{id}', function(Request $request, Response $response, $args){
			$Usuario = Usuario::where('idusuario','=',$args['id'])->get();
			if(sizeof($Usuario) == 0){
				$answer = array('Mensaje' => 'No existe el registro');
				return sendBadResponse(json_encode($answer), $response);
			} else {
				return sendOkResponse($Usuario->toJson(),$response);
			}
		});

		/* Inicio de Sesión */
		$this->get('/inicioSesion/{correo}/{contrasena}', function(Request $request, Response $response, $args){
			$Sesion = Usuario::where('correo_electronico','=',$args['correo'])->where('contrasena','=',$args['contrasena'])->get();
			if(sizeof($Sesion) > 0){
				$id = json_decode($Sesion);
				return sendOkResponse(json_encode( array( 'OK' => 'Usuario encontrado', 'id' => $id[0]->idusuario)), $response);
			}
			else
				return sendBadResponse(json_encode(array('Error' => 'No existe el usuario')),$response);
		});

	});

	/* Rutas para los cultivos */
	$app->group('/cultivo', function(){
		
		/* Registra un nuevo cultivo */
		$this->post('/nuevo', function(Request $request, Response $response, $args){
			$datosCultivos = $request->getParsedBody();
			$Cultivo = new C_tipo_cultivo();
			$Cultivo->cultivo = $datosCultivos['cultivo'];
			if($Cultivo->save()){
				$answer = array('OK' => 'Se ha registrado Exitosamente');
				return sendOkResponse(json_encode($answer), $response);
			} else {
				$answer = array('Error' => 'Ha ocurrido un error');
				return sendBadResponse(json_encode($answer), $response);
			}
		});
	});

	/* Rutas para las variables */
	$app->group('/variable', function(){
		
		/* Registra un nuevo cultivo */
		$this->post('/nuevo', function(Request $request, Response $response, $args){
			$datosVariable = $request->getParsedBody();
			$Variable = new C_variable();
			$Variable->variable = $datosVariable['variable'];
			if($Variable->save()){
				$answer = array('OK' => 'Se ha registrado Exitosamente');
				return sendOkResponse(json_encode($answer), $response);
			} else {
				$answer = array('Error' => 'Ha ocurrido un error');
				return sendBadResponse(json_encode($answer), $response);
			}
		});
	});

	/* Rutas para las plantas */
	$app->group('/planta', function(){

		/* Registra nueva planta */
		$this->post('/nuevo', function(Request $request, Response $response, $args){
			$datosPlanta = $request->getParsedBody();
			$Planta = new Planta();
			$Planta->ubicacion = $datosPlanta['ubicacion'];
			$Planta->empresa_idempresa = $datosPlanta['empresa_idempresa'];
			if($Planta->save()){
				$answer = array('OK' => 'Se ha registrado Exitosamente');
				return sendOkResponse(json_encode($answer), $response);
			} else {
				$answer = array('Error' => 'Ha ocurrido un error');
				return sendBadResponse(json_encode($answer), $response);
			}
		});
	});

	/* Rutas para los invernaderos */
	$app->group('/invernadero', function(){

		/* Registra nuevo invernadero */
		$this->post('/nuevo', function(Request $request, Response $response, $args){
			$datosInvernadero = $request->getParsedBody();
			$Invernadero = new Invernadero();
			$Invernadero->planta_idplanta = $datosInvernadero['planta_idplanta'];
			$Invernadero->informacion_rangos_idinformacion_rangos = $datosInvernadero['informacion_rangos_idinformacion_rangos'];
			if($Invernadero->save()){
				$answer = array('OK' => 'Se ha registrado Exitosamente');
				return sendOkResponse(json_encode($answer), $response);
			} else {
				$answer = array('Error' => 'Ha ocurrido un error');
				return sendBadResponse(json_encode($answer), $response);
			}
		});
	});

	/* Rutas para informacion de rangos */
	$app->group('/informacion_rangos', function(){

		/* Registra informacion de rangos */
		$this->post('/nuevo', function(Request $request, Response $response, $args){
			$datosInformacion = $request->getParsedBody();
			$informacion = new Informacion_rangos();
			$informacion->minimo = $datosInformacion['minimo'];
			$informacion->maximo = $datosInformacion['maximo'];
			$informacion->c_tipo_cultivo_idc_tipo_cultivo = $datosInformacion['c_tipo_cultivo_idc_tipo_cultivo'];
			$informacion->c_variable_idc_variable = $datosInformacion['c_variable_idc_variable'];
			if($informacion->save()){
				$answer = array('OK' => 'Se ha registrado Exitosamente');
				return sendOkResponse(json_encode($answer), $response);
			} else {
				$answer = array('Error' => 'Ha ocurrido un error');
				return sendBadResponse(json_encode($answer), $response);
			}
		});
	});	


	$app->post('/agregarRegistro', function(Request $request, Response $response, $args){
		$data = $request->getParsedBody();
		// print_r($data);
		$JSON_Modelo = array(
			'id' => $data['id'],
			'type' => $data['type'],
			'typeOfCrop' => $data['typeOfCrop'],
			'organization' => array(
				'name' => $data['organization_name']
			),
			'address' => array(
				'addressCountry' => $data['address_addressCountry'],
				'addressLocality' => $data['address_addressLocality'],
				'streetAddress' => $data['address_streetAddress']
			),
			'dateObserved' => $data['dateObserved'],
			'areaServed' => $data['areaServed'],
			'relativeHumidity' => $data['relativeHumidity'],
			'temperature' => $data['temperature'],
			'soilMoisture' => $data['soilMoisture'],
			'conductivity' => $data['conductivity']
		);

		// print_r(json_encode($JSON_Modelo));

		url_post(json_encode($JSON_Modelo));
		// $context = stream_context_create( array(
	 //     'http' => array(
	 //     'method' => 'POST',
	 //     'header' => "Authorization: application/json\r\n".
	 //     "Content-Type: application/json\r\n",
	 //     'content' => json_encode($JSON_Modelo)
	 //    )));

	 //    $response = file_get_contents('http://207.249.127.94:1026/v2/entities', FALSE, $context);
	    //FALTA CONTINUAR ESTA PARTE
	});


	/**************************************************************
               TERMINAN LAS RUTAS DE NUESTRO SERVICIO WEB
	***************************************************************/

	/* FUNCIONES UTILIZADAS EN LAS RUTAS */

	/* Enviar respuesta de OK en formato json */
	function sendOkResponse($message,$response){
		$newResponse = $response->withStatus(200)->withHeader('Content-type','application/json');
		$newResponse->getBody()->write($message);
		return $newResponse;
	}

	/* Enviar respuesta de algún error o vacíos */
	function sendBadResponse($message,$response){
		$newResponse = $response->withStatus(500)->withHeader('Content-type','application/json');
		$newResponse->getBody()->write($message);
		return $newResponse;
	}

	/* /Enviar petición POST a FIWARE */
	function url_post($data){
		header("Content-Type: application/json");
		$url = 'http://207.249.127.94:1026/v2/entities';
		$ch = curl_init($url);
		@curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array(
	       	'Content-Type:application/json',
			'Content-Length:'.strlen($data))
		);
		$response = curl_exec($ch);
		return $response;
	}

	$app->run();

?>