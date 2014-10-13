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
class plg_AnCoupon_LC_Page_Admin_Products_CouponEdit extends plg_AnCoupon_LC_Page_Admin
{
    /**
     *
     * @var string
     */
    protected $defaultMode = 'edit';

    public function init()
    {
        parent::init();

        $this->tpl_mainpage = 'products/plg_AnCoupon_coupon_edit.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'coupon';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = 'クーポン登録';
    }

    protected function initializeContext(An_Eccube_PageContext $context)
    {
        if (isset($_GET['coupon_id'])) {
            $coupon_id = (int)$_GET['coupon_id'];
            $coupon = An_Eccube_Coupon::load($coupon_id);
        } else {
            $coupon = new An_Eccube_Coupon();

            $discount_rule_ids = (array)@$_GET['discount_rule_id'];
            if ($discount_rule_ids) {
                $discount_rule_id = array_pop($discount_rule_ids);
                try {
                    $discount_rule = An_Eccube_DiscountRule::load($discount_rule_id);
                    $coupon->effective_from = $discount_rule->effective_from;
                    $coupon->effective_to = $discount_rule->effective_to;
                    $coupon->discount_rules = array($discount_rule->discount_rule_id);
                } catch (Exception $e) {
                    trigger_error($e->getMessage(), E_USER_WARNING);
                }
            }
        }
        $context['coupon'] = $coupon;
    }

    protected function doEdit($errors = array())
    {
        $coupon = $this->context['coupon'];
        $this->coupon = $coupon;

        $params = $this->buildFormParam();
        $form = $this->buildForm($params, $errors);
        $this->form = $form;

        $this->acceptable_chars = AnCoupon::getSetting('acceptable_chars');
    }

    protected function doSave()
    {
        try {
            $tx = An_Eccube_Model::beginTransaction();

            $coupon = $this->context['coupon'];
            if ($coupon->isStored()) {
                $lock = An_Eccube_Coupon::load($coupon->coupon_id, array('for_update' => true));
            }

            $params = $this->buildFormParam($this->context);
            $values = $_POST + array(
            );
            $params->setParam($values);

            $errors = $this->validateFormParam($params, $this->context);
            if ($errors) {
                $tx->rollback();
                $this->applyFormParam($params, $this->context);
                $this->doEdit($errors);
                return;
            }

            $this->applyFormParam($params, $this->context);

            $coupon->save();

            $tx->commit();

            $this->coupon = $coupon;
            $this->tpl_mainpage = 'products/plg_AnCoupon_coupon_edit_complete.tpl';
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

        $form['limit_uses']['options'] = array(
            1 => '制限する',
            0 => '制限しない',
        );

        $discount_rules = An_Eccube_DiscountRule::findByWhere('discount_rule_id, name', 'enabled = ?', array(1), null, null, 'name');
        $options = array('' => '');
        foreach ($discount_rules as $discount_rule) {
            $options[$discount_rule->discount_rule_id] = $discount_rule->name;
        }
        $form['discount_rule']['options'] = $options;

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
     * @return SC_FormParam_Ex
     */
    protected function buildFormParam()
    {
        $coupon = $this->context['coupon'];

        $params = new SC_FormParam_Ex();

        $params->addParam('クーポンコード', 'code', 64, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $coupon->code);
        $params->addParam('状態', 'enabled', 1, 'n', array('MAX_LENGTH_CHECK', 'SELECT_CHECK'), (int)$coupon->enabled);
        $params->addParam('管理者メモ', 'memo', 1000, 'n', array('MAX_LENGTH_CHECK'), $coupon->memo);

        $params->addParam('使用回数制限', 'limit_uses', INT_LEN, 'n', array('EXIST_CHECK', 'SELECT_CHECK'), (int)$coupon->limit_uses);
        $params->addParam('使用回数上限', 'max_uses', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'SPTAB_CHECK'), $coupon->max_uses);

        list($year, $month, $day) = explode('-', date('Y-n-j', strtotime($coupon->effective_from)));
        $params->addParam('有効期間開始年', 'effective_from_year', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $year);
        $params->addParam('有効期間開始月', 'effective_from_month', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $month);
        $params->addParam('有効期間開始日', 'effective_from_day', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $day);

        list($year, $month, $day) = explode('-', date('Y-n-j', strtotime($coupon->effective_to)));
        $params->addParam('有効期間終了年', 'effective_to_year', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $year);
        $params->addParam('有効期間終了月', 'effective_to_month', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $month);
        $params->addParam('有効期間終了日', 'effective_to_day', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $day);

        $params->addParam('割引条件', 'discount_rule', null, '', array('EXIST_CHECK'), reset($coupon->discount_rules));

        return $params;
    }

    /**
     * @param SC_FormParam_Ex $params
     * @return array キーにフォーム名、値にエラーメッセージを収めた連想配列。
     */
    protected function validateFormParam(SC_FormParam_Ex $params)
    {
        $errors = $params->checkError();
        $coupon = $this->context['coupon'];

        // クーポンコード
        $name = 'code';
        $value = $params->getValue($name);
        $title = $params->disp_name[array_search($name, $params->keyname)];
        $acceptable_chars = AnCoupon::getSetting('acceptable_chars', '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $pattern = '/[^' . preg_quote($acceptable_chars, '/') . ']/u';
        if ($value == '') {
        } elseif (preg_match($pattern, $value)) {
            $errors[$name] = "※ {$title}に使用できない文字が含まれています。<br />";
        } else {
            if ($coupon->isStored()) {
                $exists = An_Eccube_Coupon::exists('code = ? AND coupon_id <> ?', array($value, $coupon->coupon_id));
            } else {
                $exists = An_Eccube_Coupon::exists('code = ?', array($value));
            }
            if ($exists) {
                $code = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                $errors[$name] = "※ {$title}の <code>{$code}</code> は既に使用されています。<br />";
            }
        }

        // 状態
        $name = 'enabled';
        $value = $params->getValue($name);
        $title = $params->disp_name[array_search($name, $params->keyname)];
        if (!in_array($value, array(0, 1))) {
            $errors[$name] = "※ {$title}の選択肢が不正です。<br />";
        }

        // 仕様回数制限
        $name = 'limit_uses';
        $value = $params->getValue($name);
        $title = $params->disp_name[array_search($name, $params->keyname)];
        if (!in_array($value, array(0, 1))) {
            $errors[$name] = "※ {$title}の選択肢が不正です。<br />";
        }

        // 仕様回数上限
        $name = 'max_uses';
        $value = $params->getValue($name);
        $title = $params->disp_name[array_search($name, $params->keyname)];
        if ($value == '') {
        } elseif ($value < $coupon->uses) {
            $minimum = number_format($coupon->uses);
            $errors[$name] = "※ {$title}を {$minimum} 未満にする事は出来ません。<br />";
        }

        // 割引条件
        $name = 'discount_rule';
        $value = $params->getValue($name);
        $title = $params->disp_name[array_search($name, $params->keyname)];
        if ($value == '') {
        } else {
            $where = 'discount_rule_id = ?';
            $where_params = array($value);
            $exists = An_Eccube_DiscountRule::findByWhere('1', $where, $where_params, 1, 0);
            if (!$exists) {
                $errors[$name] = "※ {$title}の選択肢が不正です。<br />";
            }
        }

        // 有効期間開始
        $year = $params->getValue('effective_from_year');
        $month = $params->getValue('effective_from_month');
        $day = $params->getValue('effective_from_day');
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            $errors['effective_from'] = "※ 有効期間の開始日が不正です。<br />";
        } else {
            $effective_from = mktime(0, 0, 0, $month, $day, $year);
            if ($effective_from <= 0) {
                $errors['effective_from'] = "※ 有効期間の開始日が指定できる範囲を超えています。<br />";
            }
        }

        // 有効期間終了
        $year = $params->getValue('effective_to_year');
        $month = $params->getValue('effective_to_month');
        $day = $params->getValue('effective_to_day');
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            $errors['effective_to'] = "※ 有効期間の終了日が不正です。<br />";
        } elseif (empty($errors['effective_from'])) {
            $effective_to = mktime(0, 0, 0, $month, $day + 1, $year) - 1;
            if ($effective_to <= 0) {
                $errors['effective_to'] = "※ 有効期間の終了日が指定できる範囲を超えています。<br />";
            } elseif ($effective_to < $effective_from) {
                $errors['effective_to'] = "※ 有効期間の開始日以前にはできません。<br />";
            }
        }

        return $errors;
    }

    /**
     *
     * @param SC_FormParam_Ex $params
     */
    protected function applyFormParam(SC_FormParam_Ex $params)
    {
        $coupon = $this->context['coupon'];

        $coupon->code = $params->getValue('code');
        $coupon->enabled = $params->getValue('enabled');
        $coupon->memo = $params->getValue('memo');

        $coupon->limit_uses = $params->getValue('limit_uses');
        $coupon->max_uses = $params->getValue('max_uses');

        $discount_rules = (array)$params->getValue('discount_rule');
        $coupon->discount_rules = $discount_rules;

        $year = $params->getValue('effective_from_year');
        $month = $params->getValue('effective_from_month');
        $day = $params->getValue('effective_from_day');
        $coupon->effective_from = SC_Utils_Ex::sfGetTimestamp($year, $month, $day);

        $year = $params->getValue('effective_to_year');
        $month = $params->getValue('effective_to_month');
        $day = $params->getValue('effective_to_day');
        $coupon->effective_to = SC_Utils_Ex::sfGetTimestamp($year, $month, $day, true);
    }
}
