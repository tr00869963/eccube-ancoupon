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

require_once PLUGIN_UPLOAD_REALDIR . '/AnCoupon/pages/plg_AnCoupon_LC_Page_Admin.php';

/**
 * クーポンの編集画面
 *
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class plg_AnCoupon_LC_Page_Admin_Products_DiscountRuleEdit extends plg_AnCoupon_LC_Page_Admin
{
    /**
     *
     * @var string
     */
    protected $defaultMode = 'edit';

    public function init()
    {
        parent::init();

        $this->tpl_mainpage = 'products/plg_AnCoupon_discount_rule_edit.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'discount_rule';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = '割引条件登録';
    }

    protected function initializeContext(An_Eccube_PageContext $context)
    {
        if (isset($_GET['discount_rule_id'])) {
            $discount_rule_id = (int)$_GET['discount_rule_id'];
            $discount_rule = An_Eccube_DiscountRule::load($discount_rule_id);
        } else {
            $discount_rule = new An_Eccube_DiscountRule();
            $discount_rule->name = An_Eccube_DiscountRule::getDefaultUniqueName();
        }

        $context['discount_rule'] = $discount_rule;
    }

    protected function doEdit($errors = array())
    {
        $discount_rule = $this->context['discount_rule'];
        $this->discount_rule = $discount_rule;

        $params = $this->buildFormParam($discount_rule);
        $form = $this->buildForm($params, $errors);
        $this->form = $form;

        $product_ids = $discount_rule->products;
        $this->products = $this->getProducts($product_ids);

        $product_class_ids = $discount_rule->product_classes;
        $this->product_classes = $this->getProductClasses($product_class_ids);
    }

    protected function getProducts(array $product_ids)
    {
        if (empty($product_ids)) {
            return;
        }

        $query = SC_Query_Ex::getSingletonInstance();
        $from = "
dtb_products AS product
JOIN (SELECT product_id, MIN(price02) AS min_price, MAX(price02) AS max_price FROM dtb_products_class GROUP BY product_id) AS product_class ON product_class.product_id = product.product_id
";
        $ph_product_ids = implode(',', array_pad(array(), count($product_ids), '?'));
        $where = "product.product_id IN ($ph_product_ids)";
        $where_params = $product_ids;
        $query->setOrder('product.name');
        $columns = implode(',', array(
            'product.product_id',
            'product.name',
            'product_class.min_price AS min_price',
            'product_class.max_price AS max_price',
        ));
        $rows = $query->select($columns, $from, $where, $where_params);
        foreach ($rows as $row) {
            $products[$row['product_id']] = $row;
        }

        return $products;
    }

    protected function getProductClasses(array $product_class_ids)
    {
        if (empty($product_class_ids)) {
            return;
        }

        $query = SC_Query_Ex::getSingletonInstance();
        $from = "
dtb_products_class AS product_class
JOIN dtb_products AS product ON product.product_id = product_class.product_id
LEFT JOIN dtb_classcategory AS classcategory1 ON classcategory1.classcategory_id = product_class.classcategory_id1
LEFT JOIN dtb_classcategory AS classcategory2 ON classcategory2.classcategory_id = product_class.classcategory_id2
";
        $ph_product_class_ids = implode(',', array_pad(array(), count($product_class_ids), '?'));
        $where = "product_class.product_class_id IN ($ph_product_class_ids)";
        $query->setOrder('product.name');
        $where_params = $product_class_ids;
        $columns = implode(',', array(
            'product_class.product_class_id',
            'product_class.product_code',
            'product_class.product_id',
            'product_class.price01',
            'product_class.price02',
            'product.name AS product_name',
            'classcategory1.name AS classcategory1_name',
            'classcategory2.name AS classcategory2_name',
        ));
        $rows = $query->select($columns, $from, $where, $where_params);
        foreach ($rows as $row) {
            $product_classes[$row['product_class_id']] = $row;
        }

        return $product_classes;
    }

    protected function doRefresh()
    {
        $discount_rule = $this->context['discount_rule'];

        $params = $this->buildFormParam($discount_rule);
        $values = $_POST + array(
            'category' => array(),
            'product' => array(),
            'product_remove' => array(),
            'product_class' => array(),
            'product_class_remove' => array(),
        );
        $params->setParam($values);
        $this->applyFormParam($params, $discount_rule);
        $this->doEdit();

        $this->tpl_onload = "location.hash='#product'";
    }

    protected function doSave()
    {
        try {
            $tx = An_Eccube_Model::beginTransaction();

            $discount_rule = $this->context['discount_rule'];
            if ($discount_rule->isStored()) {
                $lock = An_Eccube_DiscountRule::load($discount_rule->discount_rule_id, array('for_update' => true));
            }

            $params = $this->buildFormParam($discount_rule);
            $values = $_POST + array(
                'allow_guest' => 0,
                'allow_member' => 0,
                'category' => array(),
                'product' => array(),
                'product_remove' => array(),
                'product_class' => array(),
                'product_class_remove' => array(),
            );
            $params->setParam($values);

            $errors = $this->validateFormParam($params);
            if ($errors) {
                $tx->rollback();
                $this->doEdit($errors);
                return;
            }

            $this->applyFormParam($params, $discount_rule);

            $discount_rule->save();

            $tx->commit();

            $this->discount_rule = $discount_rule;
            $this->tpl_mainpage = 'products/plg_AnCoupon_discount_rule_edit_complete.tpl';
        } catch (Exception $e) {
            $tx->rollback();

            throw $e;
        }
    }

    /**
     * @param SC_FormParam_Ex $params
     * @param array $errors
     * @return array
     */
    protected function buildForm(SC_FormParam_Ex $params, $errors = array())
    {
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

        $form['enabled']['options'] = array(
            1 => '有効',
            0 => '無効',
        );

        $product_ids = $form['product']['value'];
        $options = array();
        if ($product_ids) {
            foreach ($product_ids as $product_id) {
                $map[$product_id] = count($options);
                $options[$product_id] = '削除';
            }
        }
        $form['product_remove']['options'] = $options;
        $form['product_remove']['map'] = $map;

        $product_class_ids = $form['product_class']['value'];
        $options = array();
        if ($product_class_ids) {
            foreach ($product_class_ids as $product_class_id) {
                $map[$product_class_id] = count($options);
                $options[$product_class_id] = '削除';
            }
        }
        $form['product_class_remove']['options'] = $options;
        $form['product_class_remove']['map'] = $map;

        $categories = SC_Helper_DB::sfGetCatTree(0);
        $levels = array();
        $options = array();
        foreach ($categories as $category) {
            $options[$category['category_id']] = $category['category_name'];
            $levels[] = $category['level'];
        }
        $form['category']['options'] = $options;
        $form['category']['levels'] = $levels;

        $product_numbers = array();
        $query = SC_Query_Ex::getSingletonInstance();
        $rows = $query->select('category_id, product_count', 'dtb_category_count');
        $map = array_keys($options);
        foreach ($rows as $row) {
            $index = array_search($row['category_id'], $map);
            $product_numbers[$index] = $row['product_count'];
        }
        $form['category']['product_numbers'] = $product_numbers;

        $date = new SC_Date();
        $date->setStartYear(RELEASE_YEAR);
        $date->setEndYear(date('Y', 0x7fffffff));
        $form['effective_from_year']['options'] = $date->getYear();
        $form['effective_from_month']['options'] = $date->getMonth();
        $form['effective_from_day']['options'] = $date->getDay();

        $date = new SC_Date();
        $date->setStartYear(RELEASE_YEAR);
        $date->setEndYear(date('Y', 0x7fffffff - 60 * 60 * 24));
        $form['effective_to_year']['options'] = $date->getYear();
        $form['effective_to_month']['options'] = $date->getMonth();
        $form['effective_to_day']['options'] = $date->getDay();

        return $form;
    }

    /**
     * @param An_Eccube_DiscountRule $discount_rule
     * @return SC_FormParam_Ex
     */
    protected function buildFormParam(An_Eccube_DiscountRule $discount_rule)
    {
        $params = new SC_FormParam_Ex();

        $params->addParam('割引条件名', 'name', 100, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $discount_rule->name);
//         $params->addParam('割引条件コード', 'code', 60, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $discount_rule->code);
        $params->addParam('状態', 'enabled', 1, 'n', array('MAX_LENGTH_CHECK', 'SELECT_CHECK'), (int)$discount_rule->enabled);
        $params->addParam('管理者メモ', 'memo', 1000, 'n', array('MAX_LENGTH_CHECK'), $discount_rule->memo);

        $params->addParam('ゲスト', 'allow_guest', 1, 'n', array('MAX_LENGTH_CHECK'), (int)$discount_rule->allow_guest);
        $params->addParam('会員', 'allow_member', 1, 'n', array('MAX_LENGTH_CHECK'), (int)$discount_rule->allow_member);
        $params->addParam('最低購入金額', 'minimum_subtotal', PRICE_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK', 'SPTAB_CHECK'), $discount_rule->minimum_subtotal);

        $params->addParam('定額小計割引', 'total_discount_amount', PRICE_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK', 'SPTAB_CHECK'), $discount_rule->total_discount_amount);
        $params->addParam('比例小計割引', 'total_discount_rate', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK', 'SPTAB_CHECK'), $discount_rule->total_discount_rate * 100);
        $params->addParam('定額商品割引', 'item_discount_amount', PRICE_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK', 'SPTAB_CHECK'), $discount_rule->item_discount_amount);
        $params->addParam('比例商品割引', 'item_discount_rate', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK', 'SPTAB_CHECK'), $discount_rule->item_discount_rate * 100);

        list($year, $month, $day) = explode('-', date('Y-n-j', strtotime($discount_rule->effective_from)));
        $params->addParam('適用開始年', 'effective_from_year', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $year);
        $params->addParam('適用開始月', 'effective_from_month', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $month);
        $params->addParam('適用開始日', 'effective_from_day', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $day);

        list($year, $month, $day) = explode('-', date('Y-n-j', strtotime($discount_rule->effective_to)));
        $params->addParam('適用終了年', 'effective_to_year', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $year);
        $params->addParam('適用終了月', 'effective_to_month', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $month);
        $params->addParam('適用終了日', 'effective_to_day', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $day);

        $params->addParam('対象カテゴリ', 'category', '', '', array(), $discount_rule->categories);

        $params->addParam('対象商品', 'product', '', '', array(), $discount_rule->products);
        $params->addParam('追加対象商品', 'product_new');
        $params->addParam('削除対象商品', 'product_remove');

        $params->addParam('対象規格', 'product_class', '', '', array(), $discount_rule->product_classes);
        $params->addParam('追加対象規格', 'product_class_new');
        $params->addParam('削除対象規格', 'product_class_remove');
        $params->addParam('アンカーキー', 'anchor_key', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));

        return $params;
    }

    /**
     * @param SC_FormParam_Ex $params
     * @return array キーにフォーム名、値にエラーメッセージを収めた連想配列。
     */
    protected function validateFormParam($params)
    {
        $errors = $params->checkError();

        // 割引条件コード
//         $name = 'code';
//         $value = $params->getValue($name);
//         $title = $params->disp_name[array_search($name, $params->keyname)];
//         if ($value == '') {
//         } elseif (preg_match('/[^0-9A-Za-z-]/u', $value)) {
//             $errors[$name] = "※ {$title}に使用できない文字が含まれています。<br />";
//         }

        // 状態
        $name = 'enabled';
        $value = $params->getValue($name);
        $title = $params->disp_name[array_search($name, $params->keyname)];
        if (!in_array($value, array(0, 1))) {
            $errors[$name] = "※ {$title}の選択肢が不正です。<br />";
        }

        // 割引額
        $name = 'total_discount_amount';
        $value = $params->getValue($name);
        $title = $params->disp_name[array_search($name, $params->keyname)];
        if ($value == '') {
        } elseif ($value < 0) {
            $errors[$name] = "※ {$title}を 0 未満にする事は出来ません。<br />";
        }

        // 割引率
        $name = 'total_discount_rate';
        $value = $params->getValue($name);
        $title = $params->disp_name[array_search($name, $params->keyname)];
        if ($value == '') {
        } elseif ($value < 0) {
            $errors[$name] = "※ {$title}を 0 未満にする事は出来ません。<br />";
        } elseif ($value > 100) {
            $errors[$name] = "※ {$title}を 100 以上にする事は出来ません。<br />";
        }

        // 割引額
        $name = 'item_discount_amount';
        $value = $params->getValue($name);
        $title = $params->disp_name[array_search($name, $params->keyname)];
        if ($value == '') {
        } elseif ($value < 0) {
            $errors[$name] = "※ {$title}を 0 未満にする事は出来ません。<br />";
        }

        // 割引率
        $name = 'item_discount_rate';
        $value = $params->getValue($name);
        $title = $params->disp_name[array_search($name, $params->keyname)];
        if ($value == '') {
        } elseif ($value < 0) {
            $errors[$name] = "※ {$title}を 0 未満にする事は出来ません。<br />";
        } elseif ($value > 100) {
            $errors[$name] = "※ {$title}を 100 以上にする事は出来ません。<br />";
        }

        // 割引適用期間開始
        $year = $params->getValue('effective_from_year');
        $month = $params->getValue('effective_from_month');
        $day = $params->getValue('effective_from_day');
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            $errors['effective_from'] = "※ 割引適用期間の開始日が不正です。<br />";
        } else {
            $effective_from = mktime(0, 0, 0, $month, $day, $year);
            if ($effective_from <= 0) {
                $errors['effective_from'] = "※ 割引適用期間の開始日が指定できる範囲を超えています。<br />";
            }
        }

        // 割引適用期間終了
        $year = $params->getValue('effective_to_year');
        $month = $params->getValue('effective_to_month');
        $day = $params->getValue('effective_to_day');
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            $errors['effective_to'] = "※ 割引適用期間の終了日が不正です。<br />";
        } elseif (empty($errors['effective_from'])) {
            $effective_to = mktime(0, 0, 0, $month, $day + 1, $year) - 1;
            if ($effective_to <= 0) {
                $errors['effective_to'] = "※ 割引適用期間の終了日が指定できる範囲を超えています。<br />";
            } elseif ($effective_to < $effective_from) {
                $errors['effective_to'] = "※ 割引適用期間の開始日以前にはできません。<br />";
            }
        }

        // 最低購入金額
        $name = 'minimum_subtotal';
        $value = $params->getValue($name);
        $title = $params->disp_name[array_search($name, $params->keyname)];
        if ($value == '') {
        } elseif ($value < 0) {
            $errors[$name] = "※ {$title}を 0 未満にする事は出来ません。<br />";
        }

        return $errors;
    }

    protected function applyFormParam(SC_FormParam_Ex $params, An_Eccube_DiscountRule $discount_rule)
    {
//         $discount_rule->code = $params->getValue('code');
        $discount_rule->name = $params->getValue('name');
        $discount_rule->enabled = $params->getValue('enabled');
        $discount_rule->memo = $params->getValue('memo');
        $discount_rule->total_discount_amount = $params->getValue('total_discount_amount');
        $discount_rule->total_discount_rate = $params->getValue('total_discount_rate') / 100;
        $discount_rule->item_discount_amount = $params->getValue('item_discount_amount');
        $discount_rule->item_discount_rate = $params->getValue('item_discount_rate') / 100;

        $discount_rule->allow_guest = (bool)$params->getValue('allow_guest');
        $discount_rule->allow_member = (bool)$params->getValue('allow_member');
        $discount_rule->minimum_subtotal = $params->getValue('minimum_subtotal');

        $year = $params->getValue('effective_from_year');
        $month = $params->getValue('effective_from_month');
        $day = $params->getValue('effective_from_day');
        $discount_rule->effective_from = SC_Utils_Ex::sfGetTimestamp($year, $month, $day);

        $year = $params->getValue('effective_to_year');
        $month = $params->getValue('effective_to_month');
        $day = $params->getValue('effective_to_day');
        $discount_rule->effective_to = SC_Utils_Ex::sfGetTimestamp($year, $month, $day, true);

        $products = $discount_rule->products;
        $removes = (array)$params->getValue('product_remove');
        $products = array_diff($products, $removes);
        $new = $params->getValue('product_new');
        if ($new) {
            $products[] = $new;
        }
        $discount_rule->products = array_values(array_unique($products));


        $product_classes = $discount_rule->product_classes;
        $removes = (array)$params->getValue('product_class_remove');
        $product_classes = array_diff($product_classes, $removes);
        $new = $params->getValue('product_class_new');
        if ($new) {
            $product_classes[] = $new;
        }
        $discount_rule->product_classes = array_values(array_unique($product_classes));

        $discount_rule->categories = $params->getValue('category');
    }
}
