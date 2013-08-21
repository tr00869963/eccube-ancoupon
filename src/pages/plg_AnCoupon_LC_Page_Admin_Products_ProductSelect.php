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

require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_Order_ProductSelect_Ex.php';

/**
 * 商品選択画面
 *
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class plg_AnCoupon_LC_Page_Admin_Products_ProductSelect extends LC_Page_Admin_Order_ProductSelect_Ex {
    function init() {
        parent::init();
        
        $this->tpl_mainpage = 'products/plg_AnCoupon_product_select.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = '';
        $this->tpl_maintitle = 'クーポン管理';
        $this->tpl_subtitle = '商品選択';
    }
    
    public function action() {
        parent::action();
        
        $this->class_required = (int)!empty($_REQUEST['class_required']);
    }
}
