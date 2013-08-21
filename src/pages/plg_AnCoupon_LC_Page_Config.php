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

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * プラグインの設定画面
 *
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class plg_AnCoupon_LC_Page_Config extends LC_Page_Admin_Ex {
    /**
     * フォームの値を収めた連想配列。
     * テンプレートで使用します。
     * 
     * @var array 
     */
    var $form_values;

    /**
     * フォームのエラーを収めた連想配列。
     * テンプレートで使用します。
     * 
     * @var array
     */
    var $form_errors;
    
    function init() {
        parent::init();

        $this->template = TEMPLATE_ADMIN_REALDIR . 'ownersstore/plg_AnCoupon_config.tpl';
        $this->tpl_subtitle = 'アフィリナビクーポンプラグイン設定';
    }
    
    function process() {
        $this->action();
        $this->sendResponse();
    }
    
    /**
     * 現在の画面モードに従ってアクションを呼び出します。
     */
    function action() {
        $mode = $this->getMode();
        switch ($mode) {
            case 'save':
                $this->actionSave();
                break;
                
            case 'edit':
            default:
                $this->actionEdit();
                break;
        }
    }
    
    /**
     * 編集アクションを実行します。
     */
    public function actionEdit() {
        $form = $this->buildForm();

        $this->form_values = $form->getHashArray();
    }
    
    /**
     * 保存アクションを実行します。
     */
    public function actionSave() {
        $form = $this->buildForm();
        $form->setParam($_POST);
        
        $errors = $this->validateForm($form);
        if ($errors) {
            $this->form_values = $form->getHashArray();
            $this->form_errors = $errors;
            return;
        }

        $form = $this->buildForm();
        $this->form_values = $form->getHashArray();
        $this->tpl_javascript = "$(window).load(function () { alert('登録しました。'); });";
    }
    
    /**
     * 設定フォームを構築します。
     * 
     * @return SC_FormParam_Ex
     */
    protected function buildForm() {
        $form = new SC_FormParam_Ex();
        return $form;
    }
    
    /**
     * 設定フォームを検証し、問題のある個所を配列で返します。
     * 
     * @param SC_FormParam_Ex $form
     * @return array キーにフォーム名、値にエラーメッセージを収めた連想配列。
     */
    function validateForm($form) {
        $errors = $form->checkError();
        if ($errors) {
            return $errors;
        }

        $errors = array();
        
        return $errors;
    }
}
