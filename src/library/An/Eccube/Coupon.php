<?php
/*
 * EC-CUBEアフィリナビクーポンプラグイン
* Copyright (C) 2013 M-soft All Rights Reserved.
* http://m-soft.jp/
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class An_Eccube_Coupon extends An_Eccube_Model {
    public $coupon_id;
    public $code;
    public $enabled = true;
    public $uses = 0;
    public $limit_uses = false;
    public $max_uses = 1;
    public $effective_from;
    public $effective_to;
    public $memo;
    public $create_date;
    public $update_date;

    public $discount_rules;

    /**
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = array(), array $options = array()) {
        parent::__construct($data, $options);

        if ($this->effective_from === null) {
            $this->effective_from = date('Y-m-d 0:0:0');
        }

        if ($this->effective_to === null) {
            $this->effective_to = date('Y-m-d H:i:s', 0x7fffffff - 60 * 60 * 24);
        }

        if ($this->discount_rules === null) {
            $this->discount_rules = array();
        }
    }

    /**
     * @see An_Eccube_Model::getStorableProperties()
     */
    protected function getStorableProperties() {
        $properties = parent::getStorableProperties();

        unset($properties['discount_rules']);

        return $properties;
    }

    protected function toStorableValues() {
        $values = parent::toStorableValues();

        $values['enabled'] = (int)$values['enabled'];

        return $values;
    }

    /**
     *
     * @param string $coupon_id
     * @return An_Eccube_Coupon
     */
    public static function load($coupon_id, array $options = array()) {
        $where = 'coupon_id = ?';
        $params = array($coupon_id);
        $add = '';

        if (!empty($options['for_update'])) {
            $add .= ' FOR UPDATE';
        }

        $coupons = self::findByWhere('*', $where, $params, null, null, null, null, $add);

        if (empty($coupons)) {
            $message = sprintf("Not found %s at id = %s", __CLASS__, $coupon_id);
            throw new RuntimeException($message);
        }

        return reset($coupons);
    }

    /**
     * @param string $cols
     * @param string $where
     * @param array $params
     * @param array $options
     * @return array <An_Eccube_Coupon>
     */
    public static function findByWhere($columns, $where, array $params = array(), $limit = null, $offset = null, $sort_key = null, $sort_order = 'ASC', $additional = null) {
        $query = self::getQuery();

        if ($limit && $offset) {
            $query->setLimitOffset($limit, $offset);
        } elseif ($limit) {
            $query->setLimit($limit);
        } elseif ($offset) {
            $query->setOffset($offset);
        }

        if ($sort_key) {
            $order = $sort_order ? "{$sort_key} {$sort_order}" : $sort_key;
            $query->setOrder($order);
        }

        if ($additional) {
            $query->setOption($additional);
        }

        $rows = $query->select($columns, 'plg_ancoupon_coupon', $where, $params);
        $coupons = array();
        foreach ($rows as $row) {
            $coupon = new self($row);

            $coupon->ensureStored();
            $coupons[$coupon->coupon_id] = $coupon;
        }

        if ($coupons) {
            $coupon_ids = array_keys($coupons);

            $query = self::getQuery();
            $coupon_ids_holder = implode(',', array_pad(array(), count($coupon_ids), '?'));
            $discount_rule_where = "coupon_id IN ({$coupon_ids_holder})";
            $discount_rule_where_params = $coupon_ids;
            $rows = $query->select('*', 'plg_ancoupon_coupon_discount_rule', $discount_rule_where, $discount_rule_where_params);
            foreach ($rows as $row) {
                $coupons[$row['coupon_id']]->discount_rules[] = $row['discount_rule_id'];
            }
        }

        return $coupons;
    }

    /**
     * @param An_Eccube_Coupon $cupon_code
     * @return An_Eccube_Coupon
     */
    public static function findByCode($cupon_code) {
        $where = 'code = ?';
        $where_params = array($cupon_code);
        $coupons = self::findByWhere('*', $where, $where_params);
        return reset($coupons);
    }

    /**
     * @param string $where
     * @param array $params
     * @return bool
     */
    public static function exists($where = '', array $params = array()) {
        $query = self::getQuery();
        return $query->exists('plg_ancoupon_coupon', $where, $params);
    }

    /**
     * @param string $where
     * @param array $params
     * @return int
     */
    public static function count($where = '', array $params = array()) {
        $query = self::getQuery();
        return $query->count('plg_ancoupon_coupon', $where, $params);
    }

    /**
     *
     */
    public function save() {
        $query = self::getQuery();

        if ($this->isStored()) {
            $this->update_date = date('Y-m-d H:i:s');
        } else {
            $this->coupon_id = $query->nextVal('plg_ancoupon_coupon_coupon_id');
            $this->create_date = date('Y-m-d H:i:s');
            $this->update_date = $this->create_date;
        }

        $values = $this->toStorableValues();

        if ($this->isStored()) {
            $query->update('plg_ancoupon_coupon', $values, 'coupon_id = ?', array($this->coupon_id));
        } else {
            $query->insert('plg_ancoupon_coupon', $values);
            $this->ensureStored();
        }

        $query->delete('plg_ancoupon_coupon_discount_rule', 'coupon_id = ?', array($this->coupon_id));
        foreach ($this->discount_rules as $discount_rule_id) {
            $values = array(
                'coupon_id' => $this->coupon_id,
                'discount_rule_id' => $discount_rule_id,
            );
            $query->insert('plg_ancoupon_coupon_discount_rule', $values);
        }
    }

    /**
     * @param string $id
     * @param int
     */
    public static function delete($coupon_id) {
        $where = 'coupon_id = ?';
        $params = array($coupon_id);
        return self::deleteByWhere($where, $params);
    }

    /**
     * @param string $where
     * @param array $params
     * @return int
     */
    public static function deleteByWhere($where, array $params = array()) {
        $query = self::getQuery();

        $coupon_ids = $query->getSql('coupon_id', 'plg_ancoupon_coupon', $where);
        $discount_rule_where = "coupon_id IN ($coupon_ids)";
        $query->delete('plg_ancoupon_coupon_discount_rule', $discount_rule_where, $params);

        return $query->delete('plg_ancoupon_coupon', $where, $params);
    }

    /**
     * @param int $used_time
     * @param array $session
     * @return boolean
     */
    public function isAvailable($used_time, SC_Customer_Ex $customer = null) {
        if ($customer === null) {
            $customer = new SC_Customer_Ex();
        }

        // 利用可能か？
        $available = $this->enabled;

        // 使用日時が有効期間内か？
        $available = $available && $this->isInPeriod($used_time);

        // 使用回数が上限に達していないか？
        $available = $available && !$this->isUsesLimitReached();

        // ユーザーが対象か？
        $available = $available && $this->isUserTargeted($customer);

        return $available;
    }

    public function isUserTargeted(SC_Customer_Ex $customer) {
        $loggedin = $customer->isLoginSuccess(true);

        $targeted = false;
        $discount_rules = $this->getDiscountRules();
        foreach ($discount_rules as $discount_rule) {
            $targeted = $targeted || (($discount_rule->allow_guest && !$loggedin) || ($discount_rule->allow_member && $loggedin));
        }

        return $targeted;
    }

    public function isInPeriod($used_time) {
        $in_from = $used_time >= strtotime($this->effective_from);
        $in_to = $used_time < strtotime($this->effective_to);
        return $in_from && $in_to;
    }

    public function isUsesLimitReached() {
        if (!$this->limit_uses) {
            return false;
        }

        return $this->uses >= $this->max_uses;
    }

    /**
     * @return array <An_Eccube_DiscountRule>
     */
    public function getDiscountRules() {
        $where = <<<__SQL__
discount_rule_id IN (
    SELECT
        discount_rule_id
    FROM
        plg_ancoupon_coupon_discount_rule AS coupon_discount_rule
    WHERE
        coupon_discount_rule.coupon_id = ?
)
__SQL__;
        $where_params = array($this->coupon_id);
        $discount_rules = An_Eccube_DiscountRule::findByWhere('*', $where, $where_params);
        return $discount_rules;
    }

    /**
     * @param array $coupon_codes
     * @return array <An_Eccube_DiscountRule>
     */
    public static function getDiscountRulesByCouponCode(array $coupon_codes) {
        if (!$coupon_codes) {
            return array();
        }

        $coupon_codes_placeholder = implode(',', array_pad(array(), count($coupon_codes), '?'));
        $where = <<<__SQL__
discount_rule_id IN (
    SELECT
        discount_rule_id
    FROM
        plg_ancoupon_coupon_discount_rule as coupon_discount_rule
        JOIN plg_ancoupon_coupon as coupon ON coupon.coupon_id = coupon_discount_rule.coupon_id
    WHERE
        coupon.code IN ($coupon_codes_placeholder)
)
__SQL__;
        $where_params = $coupon_codes;
        $discount_rules = An_Eccube_DiscountRule::findByWhere('*', $where, $where_params);
        return $discount_rules;
    }

    /**
     * @param string $coupon_code
     * @return string
     */
    public static function normalizeCode($coupon_code) {
        $ignorable_chars = "\t\n\r\0\x0B " . AnCoupon::getSetting('ignorable_chars', '-');
        $pattern = '/[' . preg_quote($ignorable_chars, '/g') . ']/u';
        $coupon_code = preg_replace($pattern, '', $coupon_code);
        return $coupon_code;
    }

    /**
     * @param int $order_id
     * @param float $discount
     */
    public function useToOrder($order_id, $discount) {
        $query = self::getQuery();

        $this->uses++;
        $query->query('UPDATE plg_ancoupon_coupon SET uses = uses + 1 WHERE coupon_id = ?', array($this->coupon_id));

        $values = array(
            'coupon_id' => $this->coupon_id,
            'order_id' => $order_id,
            'discount' => $discount,
        );
        $query->insert('plg_ancoupon_order_coupon', $values);
    }
}
