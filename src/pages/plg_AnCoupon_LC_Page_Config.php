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
 * プラグインの設定画面
 *
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class plg_AnCoupon_LC_Page_Config extends plg_AnCoupon_LC_Page_Admin
{
    /**
     * @var string
     */
    protected $defaultMode = 'edit';

    public function init()
    {
        parent::init();

        $this->template = TEMPLATE_ADMIN_REALDIR . 'ownersstore/plg_AnCoupon_config.tpl';
        $this->tpl_subtitle = 'アフィリナビクーポンプラグイン設定';

        // プラグインが無効になっている状態で呼び出される場合があるため。
        if (!class_exists('AnCoupon')) {
            require_once PLUGIN_UPLOAD_REALDIR . '/AnCoupon/AnCoupon.php';
            AnCoupon::setupAutoloader();
        }
    }

    protected function initializeContext(An_Eccube_PageContext $context)
    {
        $context['acceptable_chars'] = AnCoupon::getSetting('acceptable_chars', '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $context['ignorable_chars'] = AnCoupon::getSetting('ignorable_chars', '-');
        $context['api_key'] = AnCoupon::getSetting('api_key');
        $context['an7_api_endpoint'] = AnCoupon::getSetting('an7_api_endpoint');
        $context['an7_api_key'] = AnCoupon::getSetting('an7_api_key');
    }

    protected function doEdit($errors = array())
    {
        $params = $this->buildFormParam($this->context);
        $form = $this->buildForm($params, $errors);
        $this->form = $form;
    }

    protected function doSave()
    {
        try {
            $tx = An_Eccube_Model::beginTransaction();

            $params = $this->buildFormParam();
            $params->setParam($_POST);
            $this->storeOldInput($params);

            $errors = $this->validateFormParam($params);
            if ($errors) {
                $tx->rollback();
                $this->doEdit($errors);
                return;
            }

            $acceptable_chars = implode('', array_unique(str_split($this->context['acceptable_chars'])));
            AnCoupon::setSetting('acceptable_chars', $acceptable_chars);

            $ignorable_chars = implode('', array_unique(str_split($this->context['ignorable_chars'])));
            AnCoupon::setSetting('ignorable_chars', $ignorable_chars);

            AnCoupon::setSetting('api_key', $this->context['api_key']);
            AnCoupon::setSetting('an7_url', $this->context['an7_url']);
            AnCoupon::setSetting('an7_api_endpoint', $this->context['an7_api_endpoint']);
            AnCoupon::setSetting('an7_api_key', $this->context['an7_api_key']);

            AnCoupon::saveSettings();

            $tx->commit();

            $this->tpl_javascript = "$(window).load(function () { alert('登録しました。'); });";
            $this->doEdit();
        } catch (Exception $e) {
            $tx->rollback();

            throw $e;
        }
    }

    protected function storeOldInput(SC_FormParam $params)
    {
        $this->context['acceptable_chars'] = $params->getValue('acceptable_chars');
        $this->context['ignorable_chars'] = $params->getValue('ignorable_chars');

        if ($params->getValue('generate_api_key')) {
            $api_key = sha1(mt_rand() . time());
            $this->context['api_key'] = $api_key;
        }

        $this->context['an7_api_endpoint'] = $params->getValue('an7_api_endpoint');
        $this->context['an7_api_key'] = $params->getValue('an7_api_key');
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

        $params->addParam('クーポンコードに使用する文字', 'acceptable_chars', 256, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $this->context['acceptable_chars']);
        $params->addParam('クーポンコードから無視する文字', 'ignorable_chars', 256, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $this->context['ignorable_chars']);

        $params->addParam('API鍵', 'api_key', 256, '', array(), $this->context['api_key']);
        $params->addParam('API鍵生成', 'generate_api_key', 16, '', array());

        $params->addParam('AN7のAPIエンドポイント', 'an7_api_endpoint', 2048, '', array('MAX_LENGTH_CHECK'), $this->context['an7_api_endpoint']);
        $params->addParam('AN7のAPI鍵', 'an7_api_key', 2048, '', array('MAX_LENGTH_CHECK'), $this->context['an7_api_key']);

        return $params;
    }

    /**
     * @param SC_FormParam_Ex $params
     * @return array キーにフォーム名、値にエラーメッセージを収めた連想配列。
     */
    protected function validateFormParam(SC_FormParam_Ex $params)
    {
        $errors = $params->checkError();

        // クーポンコードに使用する文字
        $name = 'acceptable_chars';
        $value = $params->getValue($name);
        $title = htmlspecialchars($params->disp_name[array_search($name, $params->keyname)], ENT_QUOTES, 'UTF-8');
        if ($value == '') {
        } elseif (preg_match('/[^0-9A-Za-z]/', $value)) {
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

        // AN7のAPIエンドポイント
        $name = 'an7_api_endpoint';
        $value = $params->getValue($name);
        $title = htmlspecialchars($params->disp_name[array_search($name, $params->keyname)], ENT_QUOTES, 'UTF-8');
        if ($value == '') {
        } elseif (!preg_match('#^https?://.+#', $value)) {
            $errors[$name] = "※ {$title}が正しいURLではありません。";
        } else {
            $an7_api_endpoint = $value;
        }

        // AN7のAPI鍵
        $name = 'an7_api_key';
        $value = $params->getValue($name);
        $title = htmlspecialchars($params->disp_name[array_search($name, $params->keyname)], ENT_QUOTES, 'UTF-8');
        if ($value == '') {
        } else {
            $an7_api_key = $value;
        }

        if (isset($an7_api_endpoint) && isset($an7_api_key)) {
            $options = array(
                'endpoint' => $an7_api_endpoint,
                'api_key' => $an7_api_key,
            );
            $result = AnCoupon::invokeAn7Api('tests/connection', 'GET', array(), null, true, $options);
            if (!$result->successed) {
                switch ($result->content->code) {
                    case '401':
                        $reason = htmlspecialchars($result->content->message, ENT_QUOTES, 'UTF-8');
                        $errors['an7_api_key'] = "※ 認証に失敗しました。API鍵を確認して下さい。{$reason}";
                        break;

                    default:
                        $reason = htmlspecialchars($result->content->message, ENT_QUOTES, 'UTF-8');
                        $errors['an7_api_endpoint'] = "※ AN7との接続に失敗しました。設定を確認して下さい。{$reason}";
                        break;
                }
            }
        }

        return $errors;
    }
}
