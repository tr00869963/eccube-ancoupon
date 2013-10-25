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

class An_Eccube_DiscountRule extends An_Eccube_Model {
    public $discount_rule_id;
    public $code;
    public $name;
    public $enabled = true;
    public $memo;
    public $total_discount_amount = 0;
    public $total_discount_rate = 0;
    public $item_discount_amount = 0;
    public $item_discount_rate = 0;
    public $effective_from;
    public $effective_to;
    public $create_date;
    public $update_date;
    public $allow_member = true;
    public $allow_guest = true;
    public $minimum_subtotal = 0;

    public $products;
    public $product_classes;
    public $categories;
    
    public function __construct(array $data = array(), array $options = array()) {
        parent::__construct($data, $options);
        
        if ($this->effective_from === null) {
            $this->effective_from = date('Y-m-d H:i:s');
        }
        
        if ($this->effective_to === null) {
            $this->effective_to = date('Y-m-d H:i:s', 0x7fffffff - 60 * 60 * 24);
        }
        
        if ($this->products === null) {
            $this->products = array();
        }
        
        if ($this->product_classes === null) {
            $this->product_classes = array();
        }
        
        if ($this->categories === null) {
            $this->categories = array();
        }
    }
    
    protected function getStorableProperties() {
        $properties = parent::getStorableProperties();

        unset($properties['products']);
        unset($properties['product_classes']);
        unset($properties['categories']);
        
        return $properties;
    }
    
    protected function toStorableValues() {
        $values = parent::toStorableValues();

        $values['enabled'] = (int)$values['enabled'];
        $values['allow_guest'] = (int)$values['allow_guest'];
        $values['allow_member'] = (int)$values['allow_member'];
        
        return $values;
    }
    
    /**
     * 
     * @param string $coupon_id
     * @return An_Eccube_DiscountRule
     */
    public static function load($discount_rule_id, array $options = array()) {
        $where = 'discount_rule_id = ?';
        $params = array($discount_rule_id);
        $add = '';
        
        if (!empty($options['for_update'])) {
            $add .= ' FOR UPDATE';
        }
        
        $discount_rules = self::findByWhere('*', $where, $params, null, null, null, null, $add);
        
        if (empty($discount_rules)) {
            $message = sprintf("Not found %s at id = %s", __CLASS__, $discount_rule_id);
            throw new RuntimeException($message);
        }
        
        return reset($discount_rules);
    }
    
    /**
     * @param string $cols
     * @param string $where
     * @param array $params
     * @param array $options
     * @return array
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

        $discount_rules = array();
        $rows = $query->select($columns, 'plg_ancoupon_discount_rule', $where, $params);
        foreach ($rows as $row) {
            $discount_rule = new self($row);
        
            $discount_rule->ensureStored();
            $discount_rules[$discount_rule->discount_rule_id] = $discount_rule;
        }
        
        if ($discount_rules) {
            $discount_rule_ids = array_keys($discount_rules);

            $query = self::getQuery();
            $discount_rule_ids_holder = implode(',', array_pad(array(), count($discount_rule_ids), '?'));
            $product_where = "discount_rule_id IN ({$discount_rule_ids_holder})";
            $product_where_params = $discount_rule_ids;
            $rows = $query->select('*', 'plg_ancoupon_discount_rule_product', $product_where, $product_where_params);
            foreach ($rows as $row) {
                $discount_rules[$row['discount_rule_id']]->products[] = $row['product_id'];
            }

            $query = self::getQuery();
            $product_class_where = "discount_rule_id IN ({$discount_rule_ids_holder})";
            $product_class_where_params = $discount_rule_ids;
            $rows = $query->select('*', 'plg_ancoupon_discount_rule_product_class', $product_class_where, $product_class_where_params);
            foreach ($rows as $row) {
                $discount_rules[$row['discount_rule_id']]->product_classes[] = $row['product_class_id'];
            }

            $query = self::getQuery();
            $category_where = "discount_rule_id IN ({$discount_rule_ids_holder})";
            $category_where_params = $discount_rule_ids;
            $rows = $query->select('*', 'plg_ancoupon_discount_rule_category', $category_where, $category_where_params);
            foreach ($rows as $row) {
                $discount_rules[$row['discount_rule_id']]->categories[] = $row['category_id'];
            }
        }
        
        return $discount_rules;
    }
    
    /**
     * 
     * @param string $where
     * @param array $params
     * @return int
     */
    public static function count($where = '', array $params = array()) {
        $query = self::getQuery();
        return $query->count('plg_ancoupon_discount_rule', $where, $params);
    }
    
    /**
     * 
     */
    public function save() {
        $query = self::getQuery();
        
        if ($this->isStored()) {
            $this->update_date = date('Y-m-d H:i:s');
        } else {
            $this->discount_rule_id = $query->nextVal('plg_ancoupon_discount_rule_id');
            $this->code = $query->nextVal('plg_ancoupon_discount_code');
            $this->create_date = date('Y-m-d H:i:s');
            $this->update_date = $this->create_date;
        }

        $values = $this->toStorableValues();
        if ($this->isStored()) {
            $query->update('plg_ancoupon_discount_rule', $values, 'discount_rule_id = ?', array($this->discount_rule_id));
        } else {
            $query->insert('plg_ancoupon_discount_rule', $values);
            $this->ensureStored();
        }
        
        $query->delete('plg_ancoupon_discount_rule_product', 'discount_rule_id = ?', array($this->discount_rule_id));
        foreach ($this->products as $product_id) {
            $values = array(
                'discount_rule_id' => $this->discount_rule_id,
                'product_id' => $product_id,
            );
            $query->insert('plg_ancoupon_discount_rule_product', $values);
        }
        
        $query->delete('plg_ancoupon_discount_rule_product_class', 'discount_rule_id = ?', array($this->discount_rule_id));
        foreach ($this->product_classes as $product_class_id) {
            $values = array(
                'discount_rule_id' => $this->discount_rule_id,
                'product_class_id' => $product_class_id,
            );
            $query->insert('plg_ancoupon_discount_rule_product_class', $values);
        }
        
        $query->delete('plg_ancoupon_discount_rule_category', 'discount_rule_id = ?', array($this->discount_rule_id));
        foreach ($this->categories as $category_id) {
            $values = array(
                'discount_rule_id' => $this->discount_rule_id,
                'category_id' => $category_id,
            );
            $query->insert('plg_ancoupon_discount_rule_category', $values);
        }
        
        return $result;
    }
    
    /**
     * @param string $id
     */
    public static function delete($discount_rule_id) {
        $where = 'discount_rule_id = ?';
        $params = array($discount_rule_id);
        return self::deleteByWhere($where, $params);
    }
    
    /**
     * @param string $where
     * @param array $params
     */
    public static function deleteByWhere($where, array $params = array()) {
        $query = self::getQuery();

        $discount_rule_ids = $query->getSql('discount_rule_id', 'plg_ancoupon_discount_rule', $where);
        $product_where = "discount_rule_id IN ($discount_rule_ids)";
        $query->delete('plg_ancoupon_discount_rule_product', $product_where, $params);
        
        $category_where = "discount_rule_id IN ($discount_rule_ids)";
        $query->delete('plg_ancoupon_discount_rule_category', $category_where, $params);
        
        return $query->delete('plg_ancoupon_discount_rule', $where, $params);
    }
    
    public static function getDefaultUniqueName() {
        $query = self::getQuery();
        $no = $query->currVal('plg_ancoupon_discount_rule_id');
        $no = $no ? $no : 1;
        return sprintf('割引条件#%s', $no);
    }
    
    public function isAvailable($used_time) {
        // 有効か？
        if (!$this->enabled) {
            return false;
        }
        
        // 割引適用期間内か？
        $in_from = $used_time >= strtotime($this->effective_from);
        $in_to = $used_time < strtotime($this->effective_to);
        if (!$in_from || !$in_to) {
            return false;
        }
        
        return true;
    }
    
    public function canDiscountProductClass($product_class_id, $used_time) {
        if (!$this->isAvailable($used_time)) {
            return false;
        }
        
        // 対象商品か？        
        $query = $this->getQuery();
        
        $stmt = <<<__SQL__
SELECT
	1
FROM
	dtb_products_class
WHERE
    product_class_id = ?
	AND (
        product_id IN (SELECT product_id FROM plg_ancoupon_discount_rule_product AS rule_product WHERE discount_rule_id = ?)
        OR product_class_id IN (SELECT product_class_id FROM plg_ancoupon_discount_rule_product_class WHERE discount_rule_id = ?)
	    OR product_id IN (SELECT dtb_product_categories.product_id FROM dtb_product_categories JOIN plg_ancoupon_discount_rule_category AS rule_cat ON rule_cat.category_id = dtb_product_categories.category_id WHERE rule_cat.discount_rule_id = ?)
    )
__SQL__;

        $params = array($product_class_id, $this->discount_rule_id, $this->discount_rule_id, $this->discount_rule_id);
        $can = $query->getOne($stmt, $params);
        return $can;
    }
    
    public function canDiscountProduct($product_id, $used_time) {
        if (!$this->isAvailable($used_time)) {
            return false;
        }
        
        // 対象商品か？        
        $query = $this->getQuery();
        
        $stmt = <<<__SQL__
SELECT
	1 AS matched
FROM
	dtb_products
WHERE
    product_id = ?
	AND (
        product_id IN (SELECT product_id FROM plg_ancoupon_discount_rule_product AS rule_product WHERE discount_rule_id = ?)
        OR product_id IN (SELECT product_id FROM dtb_products_class JOIN plg_ancoupon_discount_rule_product_class AS rule_class ON rule_class.product_class_id = dtb_products_class.product_class_id WHERE discount_rule_id = ?)
	    OR product_id IN (SELECT product_id FROM dtb_product_categories JOIN plg_ancoupon_discount_rule_category AS rule_cat ON rule_cat.category_id = dtb_product_categories.category_id WHERE rule_cat.discount_rule_id = ?)
    )
__SQL__;

        $params = array($product_id, $this->discount_rule_id, $this->discount_rule_id, $this->discount_rule_id);
        $can = $query->getOne($stmt, $params);
        return $can;
    }
    
    public function calculateItemDiscount($price, $quantity) {
        $discount = $this->item_discount_amount;
        
        $inc_tax = SC_Helper_DB_Ex::sfCalcIncTax($price);
        $total = $inc_tax * $quantity - $this->item_discount_amount;
        $discount += $total * $this->item_discount_rate;
        
        return $discount;
    }
    
    public function calculateTotalDiscount($total) {
        $discount = $this->total_discount_amount;
        
        $discount += ($total - $this->total_discount_amount) * $this->total_discount_rate;
        
        return $discount;
    }
    
    public function calculateCartDiscount(array $cart, $used_time, $apply_restricts = false) {
        $total_discount = 0;
        $total_inctax = 0;
        
        if ($apply_restricts) {
            $subtotal = 0;
            foreach (array_filter(array_keys($cart), 'is_numeric') as $index) {
                $cart_item = $cart[$index];
                
                $valid = isset($cart_item['id']) && !is_array($cart_item['id']);
                if (!$valid) {
                    continue;
                }
                
                $subtotal += $cart_item['total_inctax'];
            }
            
            if ($subtotal < $this->minimum_subtotal) {
                return 0;
            }
        }
        
        foreach (array_filter(array_keys($cart), 'is_numeric') as $index) {
            $cart_item = $cart[$index];
            
            $valid = isset($cart_item['id']) && !is_array($cart_item['id']);
            if (!$valid) {
                continue;
            }
            
            if ($this->canDiscountProductClass($cart_item['id'], $used_time)) {
                $discount = $this->calculateItemDiscount($cart_item['price'], $cart_item['quantity']);
                $total_discount += $discount;
            }
            
            $total_inctax += $cart_item['total_inctax'] - $discount;
        }
        
        $total_discount += $this->calculateTotalDiscount($total_inctax);
        
        return $total_discount;
    }
    
    public static function getTargetProductClasses(array $discount_rule_ids, $used_time, $product_ids = array()) {
        if (empty($discount_rule_ids)) {
            return array();
        }
        
        $query = self::getQuery();
        
        $from = "
dtb_products_class AS product_class
JOIN dtb_classcategory AS classcategory1 ON classcategory1.classcategory_id = product_class.classcategory_id1
JOIN dtb_classcategory AS classcategory2 ON classcategory2.classcategory_id = product_class.classcategory_id2
";
        
        $ph_discount_rule_ids = implode(',', array_pad(array(), count($discount_rule_ids), '?'));
        
        if ($product_ids) {
            $ph = implode(',', array_pad(array(), count($product_ids), '?'));
            $product_condition = $product_ids ? "AND product_class.product_id IN ($ph)" : '';
        } else {
            $product_condition = '';
        }
        
        $where = "
(
    product_class.product_class_id IN (
        SELECT
            discount_rule_product_class.product_class_id
        FROM
            plg_ancoupon_discount_rule AS discount_rule
            JOIN plg_ancoupon_discount_rule_product_class AS discount_rule_product_class ON discount_rule_product_class.discount_rule_id = discount_rule.discount_rule_id
        WHERE
            discount_rule.discount_rule_id IN ($ph_discount_rule_ids)
            AND discount_rule.enabled = 1
            AND discount_rule.effective_from <= ?
            AND discount_rule.effective_to >= ?
    )
    OR product_class.product_id IN (
        SELECT
            discount_rule_product.product_id
        FROM
            plg_ancoupon_discount_rule AS discount_rule
            JOIN plg_ancoupon_discount_rule_product AS discount_rule_product ON discount_rule_product.discount_rule_id = discount_rule.discount_rule_id
        WHERE
            discount_rule.discount_rule_id IN ($ph_discount_rule_ids)
            AND discount_rule.enabled = 1
            AND discount_rule.effective_from <= ?
            AND discount_rule.effective_to >= ?
    )
    OR product_class.product_id IN (
        SELECT
            product_category.product_id
        FROM
            plg_ancoupon_discount_rule AS discount_rule
            JOIN plg_ancoupon_discount_rule_category AS discount_rule_category ON discount_rule_category.discount_rule_id = discount_rule.discount_rule_id
            JOIN dtb_product_categories AS product_category ON product_category.category_id = discount_rule_category.category_id
        WHERE
            discount_rule.discount_rule_id IN ($ph_discount_rule_ids)
            AND discount_rule.enabled = 1
            AND discount_rule.effective_from <= ?
            AND discount_rule.effective_to >= ?
    )
)
$product_condition
";

        $time = date('Y-m-d H:i:s', $used_time);
        $where_params = array_merge(
            $discount_rule_ids,
            array($time),
            array($time),
            $discount_rule_ids,
            array($time),
            array($time),
            $discount_rule_ids,
            array($time),
            array($time),
            $product_ids
        );
        
        $columns = implode(',', array(
            'product_class.product_id',
            'classcategory1.name AS classcategory1_name',
            'classcategory2.name AS classcategory2_name',
        ));

        $query->setOrder('classcategory1.rank, classcategory2.rank');
        
        $rows = $query->select($columns, $from, $where, $where_params);
        $classes = array();
        foreach ($rows as $row) {
            $classes[$row['product_id']][] = $row;
        }
        
        return $classes;
    }
}
