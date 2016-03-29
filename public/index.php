<?php
	require "../vendor/autoload.php";
	require "./configs/Database.php";
	require "./models/Order.php";
	require "./models/OrderDetail.php";

	// connection
	$connectionString = \App\Configs\Database::$engine . 
						":host=" . \App\Configs\Database::$host . 
						";port=" . \App\Configs\Database::$port . 
						";dbname=" . \App\Configs\Database::$database . 
						";charset=UTF8;";
	$connection = new PDO(
		$connectionString,
		\App\Configs\Database::$username,
		\App\Configs\Database::$password
	);
	$connection->query("SET NAMES utf8;");

	// models
	$Order = new \App\Models\Order($connection);
	$OrderDetail = new \App\Models\OrderDetail($connection);

	$Order->addChildModel($OrderDetail);


	// app
	$app = new \Slim\App([
		'Order' => $Order,
		'OrderDetail' => $OrderDetail
	]);


	// allow origin
	$app->add(function ($request, $response, $next) {
	    // Use the PSR 7 $response object
		$response = $response->withHeader('Access-Control-Allow-Origin', '*');

	    return $next($request, $response);
	});

	/**
	* Create new order
	* @param customer_email
	* @param customer_name
	* @param customer_address
	* @param items : Array
	*/
	$app->post("/order", function ($request, $response, $args) {
		try {
			$order = $request->getParsedBody();

			if($this->get('Order')->create($order)) {

				$message = [
					"message" => "New order has been created"
				];
		   		
		   		return $response->withJson($message);
				
			} else {
				throw new Exception();
			}
		   	
		} catch (Exception $ex) {
			$message = [
				"message" => "Order could not be created. Please try again."
			];
	   		
	   		return $response->withStatus(500)->withJson($message);
		}
	});


	/**
	* Find all orders
	*/
	$app->get("/order", function ($request, $response, $args) {
		$orders = $this->get("Order")->findAll();

		return $response->withJson($orders);
	});

	$app->run();

?>