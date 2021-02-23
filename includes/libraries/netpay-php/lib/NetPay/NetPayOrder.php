<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright 2018 NetPay. All rights reserved.
 */

namespace NetPay;

class NetPayOrder {
    /**
     * Count the total complete orders.
     */
    public static function total_completed_orders()
    {
        $orders = self::completed_orders();

        return count( $orders );
    }

    /**
     * Get the days since the first and last order complete.
     */
    public static function days_first_last_order()
    {
        $first_order_date = null;
        $first_order_days = 0;
        $last_order_date = null;
        $last_order_days = 0;

        $orders = self::completed_orders();

        if (empty($orders)) {
            return compact('first_order_days', 'last_order_days');
        }

        foreach($orders as $tmp_order) {
            $order = wc_get_order($tmp_order->ID);
            $order_date = $order->order_date;

            if (is_null($first_order_date)) {
                $first_order_date = $order_date;
            }

            $last_order_date = $order_date;
        }

        $first_order_days = self::days_diff_from_now($first_order_date);

        $last_order_days = self::days_diff_from_now($last_order_date);

        return compact('first_order_days', 'last_order_days');
    }

    /**
     * Search all the complete orders of the current user.
     */
    private function completed_orders()
    {
        return get_posts( array(
            'numberposts' => -1,
            'order' => 'ASC',
            'meta_key'    => '_customer_user',
            'meta_value'  => get_current_user_id(),
            'post_type'   => 'shop_order',
            'post_status' => 'wc-completed'
        ) );
    }

    /**
     * Get the days diffence from now of a date.
     */
    private function days_diff_from_now($date)
    {
        $now = time();
        $date_time = strtotime($date);
        $date_diff = $now - $date_time;

        return round($date_diff / (60 * 60 * 24));
    }
}