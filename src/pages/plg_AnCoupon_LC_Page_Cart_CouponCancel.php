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
class plg_AnCoupon_LC_Page_Cart_CouponCancel extends LC_Page_Ex {
    public function init() {
        parent::init();

        $this->tpl_title = 'クーポンの使用を止める';
    }
    
    public function process() {
        $this->action();
        $this->sendResponse();
    }
    
    public function action() {
        $this->context = $this->getContext();
        
        $mode = $this->getMode();
        switch ($mode) {
            case 'execute':
                $this->doExecute();
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
    }
    
    public function doConfirm($validate) {
        $params = $this->buildFormParam();
        
        if ($validate) {
            $params->setParam($_POST);
            $errors = $this->validateFormParam($params);
        } else {
            $errors = array();
        }

        $form = $this->buildForm($params, $errors);
        $this->form = $form;
    }
    
    public function doExecute() {
        try {
            $params = $this->buildFormParam();
            $params->setParam($_POST);
            
            $errors = $this->validateFormParam($params);
            if ($errors) {
                $tx->rollback();
                $this->doInput(true);
                return;
            }

            $plugin = AnCoupon::getInstance();
            $plugin->clearUsingCouponCode();
            
            $this->context->dispose();
            
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
    protected function buildFormParam() {
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
