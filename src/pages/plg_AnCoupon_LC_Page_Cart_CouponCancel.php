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

require_once PLUGIN_UPLOAD_REALDIR . '/AnCoupon/pages/plg_AnCoupon_LC_Page.php';

/**
 * クーポンの編集画面
 *
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class plg_AnCoupon_LC_Page_Cart_CouponCancel extends plg_AnCoupon_LC_Page
{
    /**
     *
     * @var string
     */
    protected $defaultMode = 'confirm';

    public function init()
    {
        parent::init();

        $this->tpl_title = 'クーポンの使用を止める';
    }

    protected function doConfirm($errors = array())
    {
        $params = $this->buildFormParam();
        $form = $this->buildForm($params, $errors);
        $this->form = $form;
    }

    protected function doExecute()
    {
        try {
            $params = $this->buildFormParam();
            $params->setParam($_POST);

            $errors = $this->validateFormParam($params);
            if ($errors) {
                $tx->rollback();
                $this->doConfirm($errors);
                return;
            }

            $plugin = AnCoupon::getInstance();
            $plugin->clearUsingCouponCode();

            SC_Response_Ex::sendRedirect(CART_URLPATH);
        } catch (Exception $e) {
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
     * @return SC_FormParam_Ex
     */
    protected function buildFormParam()
    {
        $params = new SC_FormParam_Ex();

        return $params;
    }

    /**
     * @param SC_FormParam_Ex $params
     * @return array キーにフォーム名、値にエラーメッセージを収めた連想配列。
     */
    protected function validateFormParam(SC_FormParam_Ex $params)
    {
        $errors = $params->checkError();

        return $errors;
    }
}
