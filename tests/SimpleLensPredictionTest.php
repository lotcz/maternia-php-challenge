<?php

declare(strict_types=1);

namespace MaterniaTest\Predict;

use PHPUnit\Framework\TestCase;

final class SimpleLensPredictionTest extends TestCase {
	public function testCanCreateInstance(): void {
		$this->assertInstanceOf(
			SimpleLensPrediction::class,
			new SimpleLensPrediction(),
		);
	}

	/**
	 * Return default list of goods.
	 * @return array<int, int> ID => duration
	 */
	private function getGoods(): array {
		$goods = [
			1 => 180, // Biofinity (6 lenses)
			2 => 90, // Biofinity (3 lenses)
			3 => 30, // Focus Dailies (30)
		];
		return $goods;
	}

	public function testEmptyOrders(): void {
		$predict = new SimpleLensPrediction();
		$prediction_result = $predict->predictDate($this->getGoods(), []);
		$this->assertNull($prediction_result);
	}

	public function testCustomer1(): void {
		$orders = [
			'2015-04-01' => [[1, 2, '-2.00'], [1, 2, '-3.00']],
		];
		$predict = new SimpleLensPrediction();
		$prediction_result = $predict->predictDate($this->getGoods(), $orders);
		$this->assertInstanceOf(\DateTime::class, $prediction_result);
		$this->assertEquals(
			'2016-03-26', // 360 days from the purchase - default value
			$prediction_result->format('Y-m-d'),
		);
	}

	public function testCustomer2(): void {
		$orders = [
			'2014-10-01' => [[3, 2, '-1.50'], [3, 2, '-3.50']],
			'2015-01-01' => [[3, 2, '-1.50'], [3, 2, '-3.50']], // 60 units in 92 days (0,652 units/day)
			'2015-04-15' => [[3, 1, '-1.50'], [3, 1, '-3.50']], // 60 units in 104 days (0,577 units/day)
		];
		// in total 120 units in 196 days (0,612 units/day)
		// he has purchased only 30 units last time so we expect him to run out of lenses in 49 days

		$predict = new SimpleLensPrediction();
		$prediction_result = $predict->predictDate($this->getGoods(), $orders);
		$this->assertInstanceOf(\DateTime::class, $prediction_result);
		$this->assertEquals(
			'2015-06-03', // 49 days from the last purchase
			$prediction_result->format('Y-m-d'),
		);
	}

	public function testCustomer3(): void {
		$orders = [
			'2014-08-01' => [[2, 2, '+0.50']],
		];
		$predict = new SimpleLensPrediction();
		$prediction_result = $predict->predictDate($this->getGoods(), $orders);
		$this->assertInstanceOf(\DateTime::class, $prediction_result);
		$this->assertEquals(
			'2014-10-30', // 90 days from the purchase
			$prediction_result->format('Y-m-d'),
		);
	}
}
