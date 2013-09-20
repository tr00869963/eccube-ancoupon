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
     * @var An_Eccube_PageContext
     */
    public $context;
    
    function init() {
        parent::init();

        $this->template = TEMPLATE_ADMIN_REALDIR . 'ownersstore/plg_AnCoupon_config.tpl';
        $this->tpl_subtitle = 'アフィリナビクーポンプラグイン設定';
        
        // プラグインが無効になっている状態で呼び出される場合があるため。
        if (!class_exists('AnCoupon')) {
            require_once PLUGIN_UPLOAD_REALDIR . '/AnCoupon/AnCoupon.php';
            AnCoupon::setupAutoloader();
        }
        
        $this->context = $this->getContext();
    }
    
    function process() {
        $this->action();
        $this->sendResponse();
        $this->context->save();
    }
    
    /**
     * 現在の画面モードに従ってアクションを呼び出します。
     */
    function action() {
        $mode = $this->getMode();
        switch ($mode) {
            case 'save':
                $this->doSave();
                break;
                
            case 'edit':
            default:
                $this->doEdit();
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
        $context->session = array();

        $context->session['acceptable_chars'] = AnCoupon::getSetting('acceptable_chars', '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $context->session['ignorable_chars'] = AnCoupon::getSetting('ignorable_chars', '-');
    }
    
    protected function doEdit($errors = array()) {
        $params = $this->buildFormParam($this->context);
        $form = $this->buildForm($params, $errors);
        $this->form = $form;
    }
    
    protected function doSave() {
        try {
            $tx = An_Eccube_Model::beginTransaction();
            
            $params = $this->buildFormParam($this->context);
            $params->setParam($_POST);
            
            $errors = $this->validateFormParam($params, $this->context);
            if ($errors) {
                $tx->rollback();

                $this->context->session['acceptable_chars'] = $params->getValue('acceptable_chars');
                $this->context->session['ignorable_chars'] = $params->getValue('ignorable_chars');
                $this->doEdit($errors);
                return;
            }

            $acceptable_chars = implode('', array_unique(str_split($params->getValue('acceptable_chars'))));
            AnCoupon::setSetting('acceptable_chars', $acceptable_chars);
            
            $ignorable_chars = implode('', array_unique(str_split($params->getValue('ignorable_chars'))));
            AnCoupon::setSetting('ignorable_chars', $ignorable_chars);
            
            AnCoupon::saveSettings();
            
            $tx->commit();
            
            $this->context->dispose();

            $this->tpl_javascript = "$(window).load(function () { alert('登録しました。'); });";
            $this->context->session['acceptable_chars'] = $acceptable_chars;
            $this->context->session['ignorable_chars'] = $ignorable_chars;
            $this->doEdit();
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
     * @param string $coupon_code
     * @return SC_FormParam_Ex
     */
    protected function buildFormParam(An_Eccube_PageContext $context) {
        $params = new SC_FormParam_Ex();
        
        $params->addParam('クーポンコードに使用する文字', 'acceptable_chars', 256, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $context->session['acceptable_chars']);
        $params->addParam('クーポンコードから無視する文字', 'ignorable_chars', 256, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $context->session['ignorable_chars']);
        
        return $params;
    }
    
    /**
     * @param SC_FormParam_Ex $params
     * @param An_Eccube_PageContext $context
     * @return array キーにフォーム名、値にエラーメッセージを収めた連想配列。
     */
    protected function validateFormParam(SC_FormParam_Ex $params, An_Eccube_PageContext $context) {
        $errors = $params->checkError();

        // クーポンコードに使用する文字
        $name = 'acceptable_chars';
        $value = $params->getValue($name);
        $title = htmlspecialchars($params->disp_name[array_search($name, $params->keyname)], ENT_QUOTES, 'UTF-8');
        if ($value == '') {
        } elseif (preg_match('/[^0-9A-Za-z]/')) {
            $errors[$name] = "※ {$title}に半角英数字以外の文字は指定できません。";
        }

        // クーポンコードから無視する文字
        $name = 'ignorable_chars';
        $value = $params->getValue($name);
        $title = htmlspecialchars($params->disp_name[array_search($name, $params->keyname)], ENT_QUOTES, 'UTF-8');
        if ($value == '') {
        }
        
        if (!isset($errors['acceptable_chars']) && !isset($errors['ignorable_chars'])) {
            $acceptable_chars = array_unique(str_split($params->getValue('acceptable_chars')));
            $ignorable_chars = array_unique(str_split($params->getValue('ignorable_chars')));
            $available_chars = array_diff($acceptable_chars, $ignorable_chars);
            if (!$available_chars) {
                $errors['acceptable_chars'] = "※ クーポンコードとして使用できる文字がありません。使用する文字と無視する文字を見直して下さい。";
            }
        }
        
        return $errors;
    }
}
