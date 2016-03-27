<?php

namespace App\Models;

class OrderDetail {

	private $connection = null;

	function __construct(&$connection) {
		$this->connection = $connection;
	}

	public function create($orderDetail) {
		$statement = $this->connection->prepare(
            "INSERT into order_details (order_id, product_name, product_code, price, quantity, total) values(:order_id, :product_name, :product_code, :price, :quantity, :total)"
        );

        return $statement->execute([
        	":order_id" => $orderDetail["order_id"],
        	":product_code" => $orderDetail["product_code"],
        	":product_name" => $orderDetail["product_name"],
        	":price" => $orderDetail["price"],
        	":quantity" => $orderDetail["quantity"],
        	":total" => $orderDetail["total"]
    	]);
    	
	}

}

?>