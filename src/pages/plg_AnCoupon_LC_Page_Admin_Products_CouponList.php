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
 * 割引条件の一覧画面。
 *
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class plg_AnCoupon_LC_Page_Admin_Products_CouponList extends plg_AnCoupon_LC_Page_Admin
{
    /**
     *
     * @var string
     */
    protected $defaultMode = 'list';

    public function init()
    {
        parent::init();

        $this->tpl_mainpage = 'products/plg_AnCoupon_coupon_list.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'coupon';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = '発行済クーポンコード';
        $this->tpl_pager = 'pager.tpl';
    }

    protected function doList()
    {
        $params = $this->buildFormParam();
        $params->setParam($_POST);
        $errors = $this->validateFormParam($params);
        $form = $this->buildForm($params, $errors);
        $this->form = $form;

        $search_pageno = $params->getValue('search_pageno');
        $search_page_max = $params->getValue('search_page_max');

        $columns = 'coupon_id, code, enabled, uses, limit_uses, max_uses, effective_from, effective_to, create_date';
        list($where, $where_params) = $this->buildFindCondition($params);
        $sort_key = 'coupon_id';
        $sort_order = 'DESC';
        $offset = max(0, $search_page_max * ($search_pageno - 1));
        $limit = min(1000, $search_page_max);
        $coupons = An_Eccube_Coupon::findByWhere($columns, $where, $where_params, $limit, $offset, $sort_key, $sort_order);
        $this->coupons = $coupons;

        $total = An_Eccube_Coupon::count($where, $where_params);

        $pager = new SC_PageNavi_Ex($search_pageno, $total, $search_page_max, 'fnNaviSearchPage');
        $this->arrPagenavi = $pager->arrPagenavi;
    }

    protected function buildFindCondition(SC_FormParam_Ex $params)
    {
        $wheres = array();
        $values = array();
        $errors = $this->validateFormParam($params);

        if (empty($errors['code'])) {
            $code = $params->getValue('code');
            if ($code != '') {
                $wheres[] = "code LIKE ?";
                $values[] = "%{$code}%";
            }
        }

        if (empty($errors['effective_from'])) {
            $year = $params->getValue('effective_from_year');
            $month = $params->getValue('effective_from_month');
            $day = $params->getValue('effective_from_day');
            if ($year && $month && $day) {
                $wheres[] = "effective_from > ?";
                $values[] = SC_Utils_Ex::sfGetTimestamp($year, $month, $day);
            }
        }

        if (empty($errors['effective_to'])) {
            $year = $params->getValue('effective_to_year');
            $month = $params->getValue('effective_to_month');
            $day = $params->getValue('effective_to_day');
            if ($year && $month && $day) {
                $wheres[] = "effective_to <= ?";
                $values[] = SC_Utils_Ex::sfGetTimestamp($year, $month, $day, true);
            }
        }

        $where = implode(' AND ', $wheres);
        return array($where, $values);
    }

    /**
     * @param SC_FormParam_Ex $params
     * @param array $errors
     * @return array
     */
    protected function buildForm(SC_FormParam_Ex $params, array $errors = array())
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

        $masterData = new SC_DB_MasterData_Ex();
        $form['search_page_max']['options'] = $masterData->getMasterData('mtb_page_max');

        $date = new SC_Date();
        $date->setStartYear(RELEASE_YEAR);
        $date->setEndYear(date('Y', 0x7fffffff));
        $form['effective_from_year']['options'] = array('' => '----') + $date->getYear();
        $form['effective_from_month']['options'] = array('' => '--') + $date->getMonth();
        $form['effective_from_day']['options'] = array('' => '--') + $date->getDay();

        $date = new SC_Date();
        $date->setStartYear(RELEASE_YEAR);
        $date->setEndYear(date('Y', 0x7fffffff - 60 * 60 * 24));
        $form['effective_to_year']['options'] = array('' => '----') + $date->getYear();
        $form['effective_to_month']['options'] = array('' => '--') + $date->getMonth();
        $form['effective_to_day']['options'] = array('' => '--') + $date->getDay();

        return $form;
    }

    /**
     * @param object $coupon
     * @return SC_FormParam_Ex
     */
    protected function buildFormParam()
    {
        $params = new SC_FormParam_Ex();

        $params->addParam('ページ番号', 'search_pageno', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $params->addParam('表示件数', 'search_page_max', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), 10);

        $params->addParam('クーポンコード', 'code', 100, '', array('MAX_LENGTH_CHECK'));

        $params->addParam('有効開始年', 'effective_from_year', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $params->addParam('有効開始月', 'effective_from_month', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $params->addParam('有効開始日', 'effective_from_day', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));

        $params->addParam('有効終了年', 'effective_to_year', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $params->addParam('有効終了月', 'effective_to_month', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $params->addParam('有効終了日', 'effective_to_day', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));

        return $params;
    }

    /**
     * @param SC_FormParam_Ex $params
     * @return array キーにフォーム名、値にエラーメッセージを収めた連想配列。
     */
    protected function validateFormParam($params)
    {
        $errors = $params->checkError();

        // クーポンコード
        $name = 'code';
        $value = $params->getValue($name);
        $title = $params->disp_name[array_search($name, $params->keyname)];
        if ($value == '') {
        } elseif (preg_match('/[^0-9A-Za-z-]/u', $value)) {
            $errors[$name] = "※ {$title}に使用できない文字が含まれています。<br />";
        }

        // 有効期間開始
        $year = $params->getValue('effective_from_year');
        $month = $params->getValue('effective_from_month');
        $day = $params->getValue('effective_from_day');
        if ($year == '' && $month == '' && $day == '') {
        } elseif (!checkdate((int)$month, (int)$day, (int)$year)) {
            $errors['effective_from'] = "※ 有効期間開始の日付が不正です。<br />";
        } else {
            $effective_from = mktime(0, 0, 0, $month, $day, $year);
            if ($effective_from <= 0) {
                $errors['effective_from'] = "※ 有効期間開始の日付が指定できる範囲を超えています。<br />";
            }
        }

        // 有効期間終了
        $year = $params->getValue('effective_to_year');
        $month = $params->getValue('effective_to_month');
        $day = $params->getValue('effective_to_day');
        if ($year == '' && $month == '' && $day == '') {
        } elseif (!checkdate((int)$month, (int)$day, (int)$year)) {
            $errors['effective_to'] = "※ 有効期間終了の日付が不正です。<br />";
        } elseif (empty($errors['effective_from'])) {
            $effective_to = mktime(0, 0, 0, $month, $day + 1, $year) - 1;
            if ($effective_to <= 0) {
                $errors['effective_to'] = "※ 有効期間終了の日付が指定できる範囲を超えています。<br />";
            }
        }

        return $errors;
    }
}
