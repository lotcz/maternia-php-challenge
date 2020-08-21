<?php

declare(strict_types=1);

namespace MaterniaTest\Predict;

/**
 * Simple naive implementation of LensPrediction interface.
 * This will only calculate average (arithmetic mean) duration of customer's lenses from previous purchases.
 * When customer only made one purchase, we use default manufacturer's estimation of lenses duration.
 * When there were no previous purchases, we return null (prediction cannot be made).
 *
 * Note: Might be interesting to use median or weighted avarege instead of arithmetic mean.
 * Or even some machine learning taking seasons of the year and other parameters into consideration.
 * But that is not subject to this very simple implementation.
 */
class SimpleLensPrediction implements LensPrediction {
	/**
	 * Predict when our customer will need their next package of contact lenses.
	 *
	 * @param array<int, int> $goods
	 * List of available product types.
	 * Key is the ID of the good and the value is the duration of the good in days.
	 *
	 * @param array<string, array<int, array<int|string>>> $orders
	 * List of customers previous orders.
	 * The key is the date and the value is the array of orders.
	 * Each subarray has three values: ID of the good, quantity and contact lens power.
	 *
	 * @return \DateTime Estimated date of when customer will be out of lenses. May return null if prediction cannot be made.
	 */
	public function predictDate(array $goods, array $orders): ?\DateTime {
		$result = null;
		if (count($orders) > 0) {
			/**
			 * first, split by lens type (power) and fill in "time units" (total expected duration)
			 * type => [[date, total time units], ...]
			 * @var array<string, array<\DateTime|int>>
			 */
			$orders_by_type = [];
			foreach ($orders as $order_date => $orders_data) {
				foreach ($orders_data as $order_data) {
					$orders_by_type[$order_data[2]][] = [
						\DateTime::createFromFormat('Y-m-d', $order_date),
						$order_data[1] * $goods[$order_data[0]],
					];
				}
			}

			/**
			 * calculate next estimated purchase for each type
			 * @var array<\DateTime>
			 */
			$estimations_by_type = [];
			foreach ($orders_by_type as $lens_type => $order_type_orders) {
				$first_purchase_date = null;
				$last_purchase_date = null;
				$last_purchase_amount = 0;
				$total_purchased_units = 0;

				foreach ($order_type_orders as $order_data) {
					if (
						$first_purchase_date === null ||
						$first_purchase_date > $order_data[0]
					) {
						$first_purchase_date = $order_data[0];
					}
					if (
						$last_purchase_date === null ||
						$last_purchase_date < $order_data[0]
					) {
						$last_purchase_date = $order_data[0];
						$last_purchase_amount = $order_data[1];
					}
					$total_purchased_units += $order_data[1];
				}

				$total_days = round(
					$first_purchase_date->diff($last_purchase_date)->days,
				);

				$total_purchased_units -= $last_purchase_amount;

				if ($total_days > 0 && $total_purchased_units > 0) {
					$avg_units_per_day = $total_purchased_units / $total_days;
				} else {
					$avg_units_per_day = 1;
				}

				$days_estimation = round(
					$last_purchase_amount / $avg_units_per_day,
				);

				// when customer only buys a single type of lenses, we assume he uses them on both eyes
				if (count($orders_by_type) == 1) {
					$days_estimation = round($days_estimation / 2);
				}

				$estimations_by_type[] = $last_purchase_date->add(
					new \DateInterval('P' . $days_estimation . 'D'),
				);
			}

			/**
			 * choose smallest estimated date
			 * @var ?\DateTime
			 */
			$nearest_next_purchase_date = null;
			foreach ($estimations_by_type as $estimation_date) {
				if (
					$nearest_next_purchase_date === null ||
					$nearest_next_purchase_date < $estimation_date
				) {
					$nearest_next_purchase_date = $estimation_date;
				}
			}

			$result = $nearest_next_purchase_date;
		}

		return $result;
	}
}
