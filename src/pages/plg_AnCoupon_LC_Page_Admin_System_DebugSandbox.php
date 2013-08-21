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
 * クーポンの編集画面
 *
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class plg_AnCoupon_LC_Page_Admin_System_DebugSandbox extends LC_Page_Admin_Ex {
    /**
     * @var An_Eccube_PageContext
     */
    protected $context;
    
    public function init() {
        parent::init();
        
        $this->tpl_mainpage = 'system/plg_AnCoupon_debug_sandbox.tpl';
        $this->tpl_mainno = 'system';
        $this->tpl_subno = 'debug';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = 'デバッグ - PHPの実行';
    }
    
    public function process() {
        parent::process();
        
        $this->context = $this->getContext();
        $this->action();
        $this->context->save();
        $this->sendResponse();
    }
    
    public function action() {
        if (!DEBUG_MODE) {
            $message = 'このページはデバッグモードが有効時にのみ利用可能です。';
            SC_Helper_HandleError_Ex::displaySystemError($message);
        }
        
        $mode = $this->getMode();
        switch ($mode) {
            case 'execute':
                $this->doExecute();
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
        $context->session = array(
            'code' => '',
            'transaction' => 'use_rollback',
            'output_format' => 'text/plain',
        );
    }
    
    public function doEdit($errors = array()) {
        $params = $this->buildFormParam($this->context);
        $form = $this->buildForm($params, $errors);
        $this->form = $form;
    }
    
    public function doExecute() {
        try {
            $params = $this->buildFormParam($this->context);
            $params->setParam($_POST);
            
            $errors = $this->validateFormParam($params);
            if ($errors) {
                $this->doEdit($errors);
                return;
            }
            
            $this->executed = true;

            try {
                $tx_mode = $params->getValue('transaction');
                $this->transaction = $tx_mode;
                switch ($tx_mode) {
                    case 'use_commit':
                    case 'use_rollback':
                        $tx = AN_Eccube_Model::beginTransaction();
                        break;
                
                    default:
                        break;
                }
    
                $code = $params->getValue('code');
                $this->context->session['code'] = $code;
            
                $result = $this->executeCode($code);

                switch ($tx_mode) {
                    case 'use_commit':
                        $tx->commit();
                        break;
                        
                    case 'use_rollback':
                        $tx->rollback();
                        break;
                        
                    default:
                        break;
                }
            } catch (Exception $e) {
                $this->error = $e->__toString();
            }
            
            $output_format =$params->getValue('output_format');
            $this->context->session['output_format'] = $output_format;
            $this->output_format = $output_format;

            switch ($output_format) {
                case 'application/json':
                    $json = json_decode($result);
                    if ($json === false) {
                        $this->error = $json;
                    }
                    $this->result = self::convertJsonToHtml($json);
                    break;
                    
                default:
                    $this->result = $result;
            }
            
            $this->doEdit();
        } catch (Exception $e) {
            if (isset($tx) && $tx->isAlive()) {
                $tx->rollback();
            }
            
            throw $e;
        }
    }
    
    protected function executeCode($code) {
        ob_start();
        eval($code);
        $result = ob_get_contents();
        ob_end_clean();
        
        return $result;
    }
    
    protected function convertJsonToHtml($json) {
        $html = '';
        if (is_array($json)) {
            $html .= '<ol style="padding-left: 20px;">';
            foreach ($json as $item) {
                $html .= '<li>' . self::convertJsonToHtml($item) . '</li>';
            }
            $html .= '</ol>';
        } elseif (is_object($json)) {
            $html .= '<ul style="padding-left: 20px;">';
            foreach ($json as $name => $item) {
                $html .= '<li><strong>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</strong>: ' . self::convertJsonToHtml($item) . '</li>';
            }
            $html .= '</ul>';
        } elseif (is_int($json)) {
            $html .= htmlspecialchars($json, ENT_QUOTES, 'UTF-8') . ' <span style="color: dimgray;">(int)</span>';
        } elseif (is_float($json)) {
            $html .= htmlspecialchars($json, ENT_QUOTES, 'UTF-8') . ' <span style="color: dimgray;">(float)</span>';
        } else {
            $html .= '"' . htmlspecialchars($json, ENT_QUOTES, 'UTF-8') . '" <span style="color: dimgray;">(string)</span>';
        }
        
        return $html;
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
        
        $form['transaction']['options'] = array(
            'none' => '使用しない',
            'use_commit' => 'コミット',
            'use_rollback' => 'ロールバック',
        );
        
        $form['output_format']['options'] = array(
            'text/plain' => 'テキスト',
            'text/html' => 'HTML',
            'application/json' => 'JSON',
        );
        
        return $form;
    }
    
    /**
     * @param An_Eccube_PageContext $context
     * @return SC_FormParam_Ex
     */
    protected function buildFormParam(An_Eccube_PageContext $context) {
        $params = new SC_FormParam_Ex();
        
        $params->addParam('コード', 'code', LLTEXT_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $context->session['code']);
        $params->addParam('トランザクション', 'transaction', 10, '', array('EXIST_CHECK'), $context->session['transaction']);
        $params->addParam('出力フォーマット', 'output_format', 100, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $context->session['output_format']);
        
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
