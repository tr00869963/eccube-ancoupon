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

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * クーポンの編集画面
 *
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class plg_AnCoupon_LC_Page_Cart_CouponStatus extends LC_Page_Ex {
    public function init() {
        parent::init();

        $this->tpl_title = 'クーポンの状態';

        $this->context = $this->getContext();
    }
    
    public function process() {
        $this->action();
        $this->sendResponse();
        $this->context->save();
    }
    
    public function action() {
        $mode = $this->getMode();
        switch ($mode) {
            default:
                $this->doDefault();
                break;
        }
    }
    
    /**
     * @return An_Eccube_PageContext
     */
    protected function getContext() {
        $page_context_id = $_REQUEST['page_context_id'];
        $context = An_Eccube_PageContext::load($page_context_id);
        
        if (!$context->isPrepared()) {
            $this->initializeContext($context);
            $context->prepare();
        }
        
        return $context;
    }
    
    protected function initializeContext(An_Eccube_PageContext $context) {
    }
    
    public function doDefault() {
        $plugin = AnCoupon::getInstance();
        $discount_rules = $plugin->getCurrentDiscountRules();
        $used_time = $plugin->getCouponUsedTime();
        $this->discount_info = $this->getDiscountInfo($discount_rules, $used_time);
    }
    
    /**
     * @param array<An_Eccube_DiscountRule> $discount_rules
     */
    protected function getDiscountInfo(array $discount_rules, $used_time) {
        $info = array(
            'total' => false,
            'total_amount' => 0,
            'total_rate' => 0,
            'minimum_subtotal' => 0,
        );
        
        $amount = 0;
        $rate = 0;
        $minimum_subtotal = 0;
        foreach ($discount_rules as $discount_rule) {
            if ($discount_rule->isAvailable($used_time)) {
                $amount += $discount_rule->total_discount_amount;
                $rate += $discount_rule->total_discount_rate;
                $minimum_subtotal = max($discount_rule->minimum_subtotal, $minimum_subtotal);
            }
        }
        
        $info['total'] = $amount || $rate;
        
        $info['total_amount'] = $amount;
        
        $rate = $rate * 100;
        $info['total_rate'] = $rate;
        
        
        $discount_rule_ids = array_keys($discount_rules);
        $categories = $this->getTargetCategories($discount_rule_ids, $used_time);
        $products = $this->getTargetProducts($discount_rule_ids, $used_time);
        
        $info['target'] = $categories || $products;
        
        $info['target_categories'] = $categories;
        $info['target_products'] = $products;
        
        
        $info['restrict_exists'] = (bool)$minimum_subtotal;
        
        $info['restricts'] = array(
            'minimum_subtotal' => $minimum_subtotal,
        );
        
        return $info;
    }
    
    protected function getTargetCategories(array $discount_rule_ids, $used_time) {
        if (empty($discount_rule_ids)) {
            return array();
        }
        
        $query = SC_Query_Ex::getSingletonInstance();
        
        $from = <<<__SQL__
dtb_category AS category
JOIN dtb_category_total_count as category_total_count ON category_total_count.category_id = category.category_id
__SQL__;
        
        $ph_discount_rule_ids = implode(',', array_pad(array(), count($discount_rule_ids), '?'));
        $where = <<<__SQL__
category.del_flg = 0
AND category_total_count.product_count > 0
AND category.category_id IN (
    SELECT
        discount_rule_category.category_id
    FROM
        plg_ancoupon_discount_rule AS discount_rule
        JOIN plg_ancoupon_discount_rule_category AS discount_rule_category ON discount_rule.discount_rule_id = discount_rule_category.discount_rule_id
    WHERE
        discount_rule.discount_rule_id IN ($ph_discount_rule_ids)
        AND discount_rule.enabled = 1
        AND discount_rule.effective_from <= ?
        AND discount_rule.effective_to >= ?
)
__SQL__;
        
        $where_params = array_values($discount_rule_ids);
        $time = date('Y-m-d H:i:s', $used_time);
        $where_params[] = $time;
        $where_params[] = $time;

        $columns = "category.category_id, category.category_name AS name, category_total_count.product_count AS products";
        $query->setOrder('category.rank ASC');
        $items = $query->select($columns, $from, $where, $where_params);
        
        return $items;
    }
    
    protected function getTargetProducts(array $discount_rule_ids, $used_time) {
        if (empty($discount_rule_ids)) {
            return array();
        }
        
        $query = SC_Query_Ex::getSingletonInstance();
        
        $from = <<<__SQL__
dtb_products AS product
__SQL__;

        $ph_discount_rule_ids = implode(',', array_pad(array(), count($discount_rule_ids), '?'));
        $where = <<<__SQL__
product.del_flg = 0
AND product.status = 1
AND (
        product.product_id IN (
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
        OR
        product.product_id IN (
            SELECT
                product_class.product_id
            FROM
                 plg_ancoupon_discount_rule AS discount_rule
                JOIN plg_ancoupon_discount_rule_product_class AS discount_rule_product_class ON discount_rule_product_class.discount_rule_id = discount_rule.discount_rule_id
                JOIN dtb_products_class AS product_class ON product_class.product_class_id = discount_rule_product_class.product_class_id
            WHERE
                discount_rule.discount_rule_id IN ($ph_discount_rule_ids)
                AND discount_rule.enabled = 1
                AND discount_rule.effective_from <= ?
                AND discount_rule.effective_to >= ?
        )
)
__SQL__;

        $time = date('Y-m-d H:i:s', $used_time);
        $where_params = array_merge(
            $discount_rule_ids,
            array($time),
            array($time),
            $discount_rule_ids,
            array($time),
            array($time)
        );
        
        $columns = "product.product_id, product.name";
        $query->setOrder('product.name ASC');
        $items = $query->select($columns, $from, $where, $where_params);
        
        return $items;
    }
    
    /**
     * @param SC_FormParam_Ex $params
     * @param array $errors
     * @return array
     */
    protected function buildForm(SC_FormParam_Ex $params, $errors = array()) {
        $form = array();
        
        foreach ($params->keyname as $index => $key) {
            $form[$key] = array(
                'title' => $params->disp_name[$index],
                'value' => $params->getValue($key),
                'maxlength' => $params->length[$index],
                'error' => null,
            );
        }
        
        foreach ($errors as $key => $error) {
            $form[$key]['error'] = $error;
        }
        
        return $form;
    }
    
    /**
     * @param string $coupon_code
     * @return SC_FormParam_Ex
     */
    protected function buildFormParam($coupon_code) {
        $params = new SC_FormParam_Ex();
        
        return $params;
    }
    
    /**
     * @param SC_FormParam_Ex $params
     * @return array キーにフォーム名、値にエラーメッセージを収めた連想配列。
     */
    function validateFormParam(SC_FormParam_Ex $params) {
        $errors = $params->checkError();
        
        return $errors;
    }
}
