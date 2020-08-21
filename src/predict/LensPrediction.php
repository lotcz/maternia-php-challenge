<?php

declare(strict_types=1);

namespace MaterniaTest\Predict;

interface LensPrediction {
	/**
	 * Predict when our customer will need their next package of contact lenses.
	 *
	 * @param array<int, int> $goods
	 * List of available product types.
	 * Key is the ID of the good and the value is the duration of the good in days.
	 *
	 * @param array<string, array<int, array<int, int|string>>> $orders
	 * List of customers previous orders.
	 * The key is the date and the value is the array of orders.
	 * Each subarray has three values: ID of the good, quantity and contact lens power.
	 *
	 * @return \DateTime Estimated date of when customer will be out of lenses. May return null is prediction cannot be made.
	 */
	public function predictDate(array $goods, array $orders): ?\DateTime;
}
