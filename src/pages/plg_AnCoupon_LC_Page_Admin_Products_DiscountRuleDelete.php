<?php
/*
 * EC-CUBEアフィリナビクーポンプラグイン
 * Copyright (C) 2014 M-soft All Rights Reserved.
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
 * 割引条件の削除画面
 *
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class plg_AnCoupon_LC_Page_Admin_Products_DiscountRuleDelete extends plg_AnCoupon_LC_Page_Admin
{
    /**
     *
     * @var string
     */
    protected $defaultMode = 'confirm';

    public function init()
    {
        parent::init();

        $this->tpl_mainpage = 'products/plg_AnCoupon_discount_rule_delete.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'discount_rule';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = '割引条件の削除';
    }

    protected function initializeContext(An_Eccube_PageContext $context)
    {
        $discount_rule_ids = array_map('intval', (array)@$_GET['discount_rule_id']);
        $context['discount_rule_ids'] = $discount_rule_ids;
    }

    protected function doConfirm()
    {
        $discount_rule_ids = $this->context['discount_rule_ids'];
        $params = $this->buildFormParam($discount_rule_ids);

        $columns = 'name, update_date';
        list($where, $where_params) = $this->buildDeleteQueryCondition($discount_rule_ids);
        $discount_rules = An_Eccube_DiscountRule::findByWhere($columns, $where, $where_params, null, null, 'name', 'ASC');


        $this->discount_rules = $discount_rules;

        $form = $this->buildForm($params);
        $this->form = $form;
    }

    protected function buildDeleteQueryCondition($discount_rule_ids)
    {
        $wheres = array();
        $values = array();

        if ($discount_rule_ids) {
            $placeholder = implode('?', array_pad(array(), count($discount_rule_ids), '?'));
            $wheres[] = "discount_rule_id IN ($placeholder)";
            $values = array_merge($values, $discount_rule_ids);
        }

        $where = implode(' AND ', $wheres);
        return array($where, $values);
    }

    protected function doDelete()
    {
        try {
            $tx = An_Eccube_Model::beginTransaction();

            $discount_rule_ids = $this->context['discount_rule_ids'];
            $params = $this->buildFormParam($discount_rule_ids);
            $params->setParam($_POST);

            $errors = $this->validateFormParam($params);
            if ($errors) {
                $tx->rollback();
                $this->doConfirm();
                return;
            }

            list($where, $where_params) = $this->buildDeleteQueryCondition($discount_rule_ids);
            $deleted_items_number = An_Eccube_DiscountRule::deleteByWhere($where, $where_params);

            $tx->commit();

            $this->deleted_items_number = $deleted_items_number;
            $this->tpl_mainpage = 'products/plg_AnCoupon_discount_rule_delete_complete.tpl';
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

        return $form;
    }

    /**
     * @param object $coupon
     * @return SC_FormParam_Ex
     */
    protected function buildFormParam(array $discount_rule_ids)
    {
        $params = new SC_FormParam_Ex();

        return $params;
    }

    /**
     * @param SC_FormParam_Ex $params
     * @return array キーにフォーム名、値にエラーメッセージを収めた連想配列。
     */
    protected function validateFormParam($params)
    {
        $errors = $params->checkError();

        return $errors;
    }
}
