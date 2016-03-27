<?php

namespace App\Models;

class Order {

	private $connection = null;
	private $OrderDetail = null;

	function __construct(&$connection) {
		$this->connection = $connection;
	}

	public function addChildModel($OrderDetail) {
		$this->OrderDetail = $OrderDetail;
	}

	public function create($order) {

		$this->connection->beginTransaction();

		try {

			if(!$this->createOrder($order)) {
				throw new \Exception();
			}
			
			$orderId = $this->connection->lastInsertId();

			foreach($order['items'] as $orderDetail) {
				$orderDetail['order_id'] = $orderId;

				if(!$this->OrderDetail->create($orderDetail)) {
					throw new \Exception();
					break;
				}
			}

			$this->connection->commit();

			return true;

		} catch(\Exception $ex) {

			$this->connection->rollBack();
			
			return false;

		}

	}

	public function findAll() {
		$orders = [];

		$statement = $this->connection->prepare(
            "SELECT * from orders"
        );

        $statement->execute();

    	while($result = $statement->fetch(\PDO::FETCH_ASSOC)) {
    		$orders[] = $result;
    	}

		return $orders;
	}

	private function createOrder($order) {
		$statement = $this->connection->prepare(
            "INSERT into orders (customer_name, customer_email, customer_address, total) values (:customer_name, :customer_email, :customer_address, :total)"
        );

		$total = 0;
        foreach($order["items"] as $orderDetail) {
        	$total += $orderDetail["total"];
        }

        return $statement->execute([
        	":customer_name" => $order["customer_name"],
        	":customer_email" => $order["customer_email"],
        	":customer_address" => $order["customer_address"],
        	":total" => $total
    	]);
	}

}

?>