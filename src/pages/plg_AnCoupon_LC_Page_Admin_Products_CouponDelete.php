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
 * 割引条件の削除画面
 *
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class plg_AnCoupon_LC_Page_Admin_Products_CouponDelete extends plg_AnCoupon_LC_Page_Admin {
    /**
     * @var An_Eccube_PageContext
     */
    public $context;

    public function init() {
        parent::init();

        $this->tpl_mainpage = 'products/plg_AnCoupon_coupon_delete.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'coupon';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = 'クーポンコードの削除';
    }

    public function process() {
        $this->action();
        $this->sendResponse();
    }

    public function action() {
        $this->context = $this->getContext();

        $mode = $this->getMode();
        switch ($mode) {
            case 'delete':
                $this->doDelete();
                break;

            default:
                $this->doConfirm();
                break;
        }

        $this->context->save();
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
        $coupon_ids = array_map('strval', (array)@$_GET['coupon_id']);
        $context->session['coupon_ids'] = $coupon_ids;
    }

    protected function doConfirm() {
        $coupon_ids = $this->context->session['coupon_ids'];
        $params = $this->buildFormParam($coupon_ids);

        $columns = 'code, update_date';
        list($where, $where_params) = $this->buildDeleteQueryCondition($coupon_ids);
        $coupons = An_Eccube_Coupon::findByWhere($columns, $where, $where_params);

        $this->coupons = $coupons;

        $form = $this->buildForm($params);
        $this->form = $form;
    }

    protected function buildDeleteQueryCondition($coupon_ids) {
        $wheres = array();
        $values = array();

        if ($coupon_ids) {
            $placeholder = implode('?', array_pad(array(), count($coupon_ids), '?'));
            $wheres[] = "coupon_id IN ($placeholder)";
            $values = array_merge($values, $coupon_ids);
        }

        $where = implode(' AND ', $wheres);
        return array($where, $values);
    }

    protected function doDelete() {
        try {
            $tx = An_Eccube_Model::beginTransaction();

            $coupon_ids = $this->context->session['coupon_ids'];
            $params = $this->buildFormParam($coupon_ids);
            $params->setParam($_POST);

            $errors = $this->validateFormParam($params);
            if ($errors) {
                $tx->rollback();
                $this->doConfirm();
                return;
            }

            list($where, $where_params) = $this->buildDeleteQueryCondition($coupon_ids);
            $deleted_items_number = An_Eccube_Coupon::deleteByWhere($where, $where_params);

            $tx->commit();

            $this->context->dispose();

            $this->deleted_items_number = $deleted_items_number;
            $this->tpl_mainpage = 'products/plg_AnCoupon_coupon_delete_complete.tpl';
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
     * @param object $coupon
     * @return SC_FormParam_Ex
     */
    protected function buildFormParam(array $coupon_ids) {
        $params = new SC_FormParam_Ex();

        return $params;
    }

    /**
     * @param SC_FormParam_Ex $params
     * @return array キーにフォーム名、値にエラーメッセージを収めた連想配列。
     */
    protected function validateFormParam($params) {
        $errors = $params->checkError();

        return $errors;
    }
}
