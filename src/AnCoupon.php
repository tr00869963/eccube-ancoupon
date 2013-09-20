<?php
/*
 * アフィリナビクーポンプラグイン
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

/**
 * プラグインのメインクラス
 *
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class AnCoupon extends SC_Plugin_Base {
    /**
     * プラグイン設定
     * 
     * @var array
     */
    protected static $settings;
    
    /**
     * @var string
     */
    private static $plugin_id;
    
    /**
     * コンストラクタ
     */
    public function __construct(array $info) {
        parent::__construct($info);

        self::setupAutoloader();
        
        // @see AnCoupon::getInstance()
        if (isset($info['plugin_id'])) {
            self::$plugin_id = $info['plugin_id'];
        }
        
        self::loadSettings();
    }
    
    protected static $isAutoloaderRegistered = false;
    
    /**
     * @param array $info
     */
    public static function setupAutoloader() {
        if (!self::$isAutoloaderRegistered) {
            $path = dirname(__FILE__) . "/library";
            ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . $path);
        }
    }
    
    /**
     * プラグインをインストールします。
     *
     * @param array $info プラグイン情報(dtb_plugin)
     * @return void
     */
    public function install($info, $mode = 0x775) {
        self::setupAutoloader($info);
        
        $plugin_code = $info['plugin_code'];

        // ロゴを配置。
        $src = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/logo.png";
        $dest = PLUGIN_HTML_REALDIR . "{$plugin_code}/logo.png";
        copy($src, $dest);
        
        // 管理用のページを配置。 
        $src_dir = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/html/admin/";
        $dest_dir = HTML_REALDIR . ADMIN_DIR;
        SC_Utils::copyDirectory($src_dir, $dest_dir);
        
        // 顧客用のページを配置。 
        $src_dir = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/html/customer/";
        $dest_dir = HTML_REALDIR;
        SC_Utils::copyDirectory($src_dir, $dest_dir);
        
        // 公開ファイルを配置。 
        $src_dir = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/html/assets/";
        $dest_dir = PLUGIN_HTML_REALDIR . "{$plugin_code}/";
        SC_Utils::copyDirectory($src_dir, $dest_dir);

        // テンプレートを配置。
        $src_dir = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/templates/";
        $dest_dir = SMARTY_TEMPLATES_REALDIR;
        SC_Utils::copyDirectory($src_dir, $dest_dir);
        
        // プラグイン用のデータベースを作成。
        $json = file_get_contents(PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/data/db/schema.json");
        $schema = An_Eccube_Utils::decodeJson($json, true);
        $query = SC_Query_Ex::getSingletonInstance();
        An_Eccube_Utils::createDatabase($query, $schema);
        
        // データベースに初期データを投入。
        $json = file_get_contents(PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/data/db/initial_data.json");
        $data = An_Eccube_Utils::decodeJson($json, true);
        self::installInitialData($query, $data);
        
        // 設定を初期化。
        AnCoupon::loadSettings();
        AnCoupon::setSetting('acceptable_chars', '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        AnCoupon::setSetting('ignorable_chars', '-');
        AnCoupon::saveSettings();
    }
    
    /**
     * プラグインをアンインストールします。
     *
     * @param array $info プラグイン情報
     * @return void
     */
    public function uninstall($info) {
        self::setupAutoloader($info);
        
        $plugin_code = $info['plugin_code'];

        // ロゴを削除。
        $path = PLUGIN_HTML_REALDIR . "{$plugin_code}/logo.png";
        unlink($path);
        
        // 管理用のページを削除。 
        $target_dir = HTML_REALDIR . ADMIN_DIR;
        $source_dir = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/html/admin/";
        An_Eccube_Utils::deleteFileByMirror($target_dir, $source_dir);

        // 顧客用のページを削除。
        $target_dir = HTML_REALDIR;
        $source_dir = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/html/customer/";
        An_Eccube_Utils::deleteFileByMirror($target_dir, $source_dir);
        
        // 公開ファイルを削除。 
        $target_dir = PLUGIN_HTML_REALDIR . "{$plugin_code}/";
        $source_dir = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/html/assets/";
        An_Eccube_Utils::deleteFileByMirror($target_dir, $source_dir);
        
        // テンプレートを削除。 
        $target_dir = SMARTY_TEMPLATES_REALDIR;
        $source_dir = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/templates/";
        An_Eccube_Utils::deleteFileByMirror($target_dir, $source_dir);

        try {
            // プラグイン用のデータベースを削除。
            $json = file_get_contents(PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/data/db/schema.json");
            $schema = An_Eccube_Utils::decodeJson($json, true);
            $query = SC_Query_Ex::getSingletonInstance();
            An_Eccube_Utils::deleteDatabase($query, $schema);
            
            // データベースから初期データを削除。
            self::deleteInitialData($query);
        } catch (Exception $e) {
            GC_Utils_Ex::gfPrintLog($e->__toString());
        }
    }
    
    /**
     * プラグインを有効化します。
     *
     * @param array $info プラグイン情報
     * @return void
     */
    public function enable($info) {
        
    }
    
    /**
     * プラグインを無効化します。
     *
     * @param array $info プラグイン情報
     * @return void
     */
    public function disable($info) {
    }
    
    /**
     * フックを登録します。
     *
     * @param SC_Helper_Plugin $plugin_helper
     * @param int $priority
     */
    public function register(SC_Helper_Plugin $plugin_helper, $priority) {
        parent::register($plugin_helper, $priority);

        // プラグイン関連の画面を挿入する。
        $plugin_helper->addAction('prefilterTransform', array($this, 'hook_prefilterTransform'));

        // 管理画面
        $plugin_helper->addAction('LC_Page_Admin_Order_Edit_action_after', array($this, 'hook_LC_Page_Admin_Order_Edit_action_after'));

        // 顧客画面
        $plugin_helper->addAction('LC_Page_Products_List_action_after', array($this, 'hook_LC_Page_Products_List_after'));
        $plugin_helper->addAction('LC_Page_Products_Detail_action_after', array($this, 'hook_LC_Page_Products_Detail_after'));
        $plugin_helper->addAction('LC_Page_Cart_action_after', array($this, 'hook_LC_Page_Cart_action_after'));
        $plugin_helper->addAction('LC_Page_Shopping_Payment_action_before', array($this, 'hook_LC_Page_Shopping_Payment_action_before'));
        $plugin_helper->addAction('LC_Page_Shopping_Payment_action_after', array($this, 'hook_LC_Page_Shopping_Payment_action_after'));
        $plugin_helper->addAction('LC_Page_Shopping_Payment_action_confirm', array($this, 'hook_LC_Page_Shopping_Payment_action_confirm'));
        $plugin_helper->addAction('LC_Page_Shopping_Confirm_action_before', array($this, 'hook_LC_Page_Shopping_Confirm_action_before'));
        $plugin_helper->addAction('LC_Page_Shopping_Confirm_action_after', array($this, 'hook_LC_Page_Shopping_Confirm_action_after'));
        $plugin_helper->addAction('LC_Page_Shopping_Confirm_action_confirm', array($this, 'hook_LC_Page_Shopping_Confirm_action_confirm'));

        $plugin_helper->addAction('LC_Page_FrontParts_Bloc_Cart_action_after', array($this, 'hook_LC_Page_FrontParts_Bloc_Cart_action_after'));
        $plugin_helper->addAction('LC_Page_FrontParts_Bloc_NaviHeader_action_after', array($this, 'hook_LC_Page_FrontParts_Bloc_Cart_action_after'));
    }
    
    /**
     * @param SC_Query_Ex $query
     * @param array $data
     * @throws RuntimeException
     */
    public static function installInitialData(SC_Query_Ex $query, array $data) {
        foreach ($data['dtb_pagelayouts'] as $pagelayout) {
            $device_type_id = $pagelayout['device_type_id'];
            
            $page_id = $query->max('page_id', 'dtb_pagelayout', 'device_type_id = ?', array($device_type_id));
            if (PEAR::isError($page_id)) {
                throw new RuntimeException($page_id->toString());
            }
            $pagelayout['page_id'] = $page_id + 1;

            $values = $pagelayout;
            $values['create_date'] = $values['update_date'] = 'CURRENT_TIMESTAMP';
            unset($values['dtb_blocpositions']);
            $result = $query->insert('dtb_pagelayout', $values);
            if (PEAR::isError($result)) {
                throw new RuntimeException($result->toString());
            }
            
            foreach ($pagelayout['dtb_blocpositions'] as $blocposition) {
                $blocposition['device_type_id'] = $pagelayout['device_type_id'];
                $blocposition['page_id'] = $pagelayout['page_id'];
                $values = $blocposition;
                $result = $query->insert('dtb_blocposition', $values);
                if (PEAR::isError($result)) {
                    throw new RuntimeException($result->toString());
                }
            }
        }
    }
    
    /**
     * @param SC_Query_Ex $query
     * @throws RuntimeException
     */
    public static function deleteInitialData(SC_Query_Ex $query) {
        $hach_multi_table = '';
        $hach_multi_using = '';
        switch (DB_TYPE) {
            case 'mysql':
                $hach_multi_table = 'dtb_blocposition';
                $hach_multi_using = 'JOIN';
                break;
                
            case 'pgsql':
                $hach_multi_using = 'USING';
                break;
        }
        $stmt = <<<__SQL__
DELETE
    {$hach_multi_table}
FROM
    dtb_blocposition
{$hach_multi_using}
    dtb_pagelayout
WHERE
    dtb_pagelayout.url LIKE ?
    AND dtb_pagelayout.page_id = dtb_blocposition.page_id
    AND dtb_pagelayout.device_type_id = dtb_blocposition.device_type_id
__SQL__;
        $params = array('%/plg_AnCoupon%');
        $result = $query->query($stmt, $params);
        if (PEAR::isError($result)) {
            throw new RuntimeException($result->toString());
        }
        
        $result = $query->delete('dtb_pagelayout', 'dtb_pagelayout.url LIKE ?', $params);
        if (PEAR::isError($result)) {
            throw new RuntimeException($result->toString());
        }
    }
    
    /**
     * プラグインのインスタンスを取得します。
     * 
     * @return AnCoupon
     */
    public static function getInstance() {
        if (self::$plugin_id === null) {
            return null;
        }
        
        $helper = SC_Helper_Plugin_Ex::getSingletonInstance();
        $self = $helper->arrPluginInstances[self::$plugin_id];
        return $self;
    }
    
    /**
     * フックアクション。プラグイン関連の画面を挿入します。
     * 
     * @param string $source
     * @param LC_Page_Ex $page
     * @param string $filename
     */
    public function hook_prefilterTransform(&$source, LC_Page_Ex $page, $filename) {
        $transformer = new SC_Helper_Transform($source);

        $device_type_id = GC_Utils_Ex::isAdminFunction()
            ? DEVICE_TYPE_ADMIN
            : (isset($page->arrPageLayout['device_type_id']) ? $page->arrPageLayout['device_type_id'] : SC_Display_Ex::detectDevice());
        
        switch ($device_type_id) {
            case DEVICE_TYPE_PC:
                if (An_Eccube_Utils::isStringEndWith($filename, 'frontparts/bloc/cart.tpl')) {
                    $template_path = "frontparts/bloc/plg_AnCoupon_cart_coupon.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('div.information')->appendChild($template);
                    break;
                }
                
                if (An_Eccube_Utils::isStringEndWith($filename, 'products/list.tpl')) {
                    $template_path = "products/plg_AnCoupon_list_discount.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('.pricebox')->appendChild($template);
                    break;
                }
                
                if (An_Eccube_Utils::isStringEndWith($filename, 'products/detail.tpl')) {
                    $template_path = "products/plg_AnCoupon_detail_discount.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('.relative_cat')->insertBefore($template);
                    break;
                }

                if (An_Eccube_Utils::isStringEndWith($filename, 'cart/index.tpl')) {
                    $template_path = "cart/plg_AnCoupon_index_discount_row.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('tr', 3)->insertBefore($template);
                    
                    $template_path = "cart/plg_AnCoupon_index_coupon_row.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('.form_area table')->appendChild($template);
                    break;
                }

                if (An_Eccube_Utils::isStringEndWith($filename, 'shopping/confirm.tpl')) {
                    $template_path = "shopping/plg_AnCoupon_confirm_discount_row.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('tr', 3)->insertBefore($template);
                    break;
                }
                
                break;
                
            case DEVICE_TYPE_SMARTPHONE:
                if (An_Eccube_Utils::isStringEndWith($filename, 'frontparts/bloc/navi_header.tpl')) {
                    $template_path = "frontparts/bloc/plg_AnCoupon_navi_header_coupon_status.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('.popup_cart')->appendChild($template);
                    break;
                }

                if (An_Eccube_Utils::isStringEndWith($filename, 'products/list.tpl')) {
                    $template_path = "products/plg_AnCoupon_list_discount.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('.listcomment')->insertBefore($template);
                    break;
                }
                
                if (An_Eccube_Utils::isStringEndWith($filename, 'products/detail.tpl')) {
                    $template_path = "products/plg_AnCoupon_detail_discount.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('.product_detail')->appendChild($template);
                    break;
                }
                
                if (An_Eccube_Utils::isStringEndWith($filename, 'cart/index.tpl')) {
                    $template_path = "cart/plg_AnCoupon_index_coupon_info.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('.information')->appendChild($template);

                    $template_path = "cart/plg_AnCoupon_index_discount_row.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('.total_area')->appendFirst($template);
                    break;
                }

                if (An_Eccube_Utils::isStringEndWith($filename, 'shopping/confirm.tpl')) {
                    $template_path = "shopping/plg_AnCoupon_confirm_discount_row.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('.result_area li', 1)->insertAfter($template);
                    break;
                }
                break;
                
            case DEVICE_TYPE_MOBILE:
                if (An_Eccube_Utils::isStringEndWith($filename, 'cart/index.tpl')) {
                    $template_path = "cart/plg_AnCoupon_index_discount_row.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $source = preg_replace('#商品合計:.+<br>#u', "\$0{$template}", $source);
                    
                    $template_path = "cart/plg_AnCoupon_index_coupon_info.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $source .= $template;

                    $transformer = new SC_Helper_Transform_Ex($source);
                    break;
                }

                if (An_Eccube_Utils::isStringEndWith($filename, 'products/list.tpl')) {
                    $template_path = "products/plg_AnCoupon_list_discount.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $source = preg_replace('#<div align="right">#u', "{$template}\$0", $source);
                    
                    $transformer = new SC_Helper_Transform_Ex($source);
                    break;
                }
                
                if (An_Eccube_Utils::isStringEndWith($filename, 'products/detail.tpl')) {
                    $template_path = "products/plg_AnCoupon_detail_discount.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $source = preg_replace('#<!--★関連カテゴリ★-->#u', "{$template}\$0", $source);

                    $transformer = new SC_Helper_Transform_Ex($source);
                    break;
                }

                if (An_Eccube_Utils::isStringEndWith($filename, 'shopping/confirm.tpl')) {
                    $template_path = "shopping/plg_AnCoupon_confirm_discount_row.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $source = preg_replace('#送料：#u', "{$template}\$0", $source);
                    
                    $transformer = new SC_Helper_Transform_Ex($source);
                    break;
                }
                
                break;
                
            case DEVICE_TYPE_ADMIN:
            default:
                if (DEBUG_MODE && An_Eccube_Utils::isStringEndWith($filename, 'system/subnavi.tpl')) {
                    $template_path = "system/plg_AnCoupon_subnavi_item.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('ul')->appendChild($template);
                    break;
                }
                
                if (An_Eccube_Utils::isStringEndWith($filename, 'products/subnavi.tpl')) {
                    $template_path = "products/plg_AnCoupon_subnavi_item.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('ul')->appendChild($template);
                    break;
                }
                
//                 if (An_Eccube_Utils::isStringEndWith($filename, 'products/product.tpl')) {
//                     $template_path = "products/plg_AnCoupon_product_edit.tpl";
//                     $template = "<!--{include file='{$template_path}'}-->";
//                     $transformer->select('#product')->insertBefore($template);
//                     break;
//                 }
                
                if (An_Eccube_Utils::isStringEndWith($filename, 'order/edit.tpl')) {
                    $template_path = "order/plg_AnCoupon_edit_coupon.tpl";
                    $template = "<!--{include file='{$template_path}'}-->";
                    $transformer->select('#order-edit-products')->insertAfter($template);
                    break;
                }
                
                break;
        }
        
        $source = $transformer->getHTML();
    }
    
    public static function getSetting($key, $default = null) {
        if (self::$settings === null) {
            self::loadSettings();
        }
        
        return isset(self::$settings[$key]) ? self::$settings[$key] : $default;
    }
    
    public static function setSetting($key, $value) {
        if (self::$settings === null) {
            self::loadSettings();
        }
        
        self::$settings[$key] = $value;
    }
    
    public static function loadSettings() {
        $query = SC_Query::getSingletonInstance();
        $row = $query->getCol('free_field1', 'dtb_plugin', 'plugin_code = ?', array(__CLASS__));
        if (PEAR::isError($row)) {
            throw new RuntimeException($row->toString());
        }
        
        if (empty($row[0])) {
            $settings = array();
        } else {
            $settings = An_Eccube_Utils::decodeJson($row[0], true);
        }
        
        self::$settings = $settings;
    }
    
    public static function saveSettings() {
        $json = An_Eccube_Utils::encodeJson(self::$settings);
        
        $query = SC_Query::getSingletonInstance();
        $values = array(
            'free_field1' => $json,
        );
        $result = $query->update('dtb_plugin', $values, 'plugin_code = ?', array(__CLASS__));
        if (PEAR::isError($result)) {
            throw new RuntimeException($result->toString());
        }
    }
    
    /**
     * @return array <An_Eccube_DiscountRule>
     */
    public function getCurrentDiscountRules() {
        $coupon_codes = $this->getUsingCouponCodes();
        $discount_rules = An_Eccube_Coupon::getDiscountRulesByCouponCode($coupon_codes);
        return $discount_rules;
    }
    
    /**
     * @return int
     */
    public function getCouponUsedTime() {
        $using_coupons =& $this->getSessionValue('using_coupons', array());
        $used_time = 0;
        foreach ($using_coupons as $using_coupon) {
            $used_time = max($used_time, $using_coupon['used_time']);
        }
        return $used_time;
    }
    
    public function calculateDiscountOfCart(array $cart, array $discount_rules, $used_time) {
        $discount = 0;
        
        foreach ($discount_rules as $discount_rule) {
            $discount += $discount_rule->calculateCartDiscount($cart, $used_time);
        }

        return floor($discount);
    }
    
    public function & getSession() {
        if (!isset($_SESSION['plg_AnCoupon'])) {
            $_SESSION['plg_AnCoupon'] = array();
        }
        
        return $_SESSION['plg_AnCoupon'];
    }
    
    public function & getSessionValue($key, $default = null) {
        $session =& $this->getSession();
        
        if (array_key_exists($key, $session)) {
            return $session[$key];
        }
        
        $session[$key] = $default;
        return $session[$key];
    }
    
    public function useCouponCode($coupon_code, $time = null) {
        if ($time === null) {
            $time = time();
        }
        
        $using_coupons =& $this->getSessionValue('using_coupons', array());
        
        if (isset($using_coupons[$coupon_code])) {
            $using_coupons[$coupon_code]['used_time'] = $time;
            return;
        }
        
        $using = array(
            'coupon_code' => $coupon_code,
            'used_time' => $time,
        );
        
        $using_coupons[$coupon_code] = $using;
    }
    
    /**
     * @return array:
     */
    public function getUsingCouponCodes() {
        $using_coupons =& $this->getSessionValue('using_coupons', array());
        return array_keys($using_coupons);
    }
    
    /**
     * 現在使用しているクーポンを取り消します。
     */
    public function clearUsingCouponCode() {
        $using_coupons =& $this->getSessionValue('using_coupons', array());
        $using_coupons = array();
    }
    
    public function hook_LC_Page_Products_List_after(LC_Page_Ex $page) {
        $discount_rules = $this->getCurrentDiscountRules();
        $used_time = $this->getCouponUsedTime();
        
        $discount_rule_ids = array_keys($discount_rules);
        $product_ids = array_keys($page->arrProducts);
        $classes = An_Eccube_DiscountRule::getTargetProductClasses($discount_rule_ids, $used_time, $product_ids);
        
        foreach ($product_ids as $product_id) {
            $discount = array(
                'available' => false,
                'amount'    => 0,
                'rate'      => 0,
                'classes'   => $classes[$product_id],
            );
            foreach ($discount_rules as $discount_rule) {
                if ($discount_rule->canDiscountProduct($product_id, $used_time)) {
                    $discount['available'] = true;
                    $discount['amount'] += $discount_rule->item_discount_amount;
                    $discount['rate'] += $discount_rule->item_discount_rate;
                    $discount['rate'] += $discount_rule->total_discount_rate;
                }
            }
            $discount['rate'] = $discount['rate'] * 100;
            
            $discounts[$product_id] = $discount;
        }
        
        $page->coupon_discounts = $discounts;
    }
    
    public function hook_LC_Page_Products_Detail_after(LC_Page_Ex $page) {
        $discount_rules = $this->getCurrentDiscountRules();
        $used_time = $this->getCouponUsedTime();
        $product_id = $page->tpl_product_id;

        $discount_rule_ids = array_keys($discount_rules);
        $classes = An_Eccube_DiscountRule::getTargetProductClasses($discount_rule_ids, $used_time, array($product_id));

        $discount = array(
            'available' => false,
            'amount'    => 0,
            'rate'      => 0,
            'classes'   => $classes[$product_id],
        );
        
        foreach ($discount_rules as $discount_rule) {
            if ($discount_rule->canDiscountProduct($product_id, $used_time)) {
                $discount['available'] = true;
                $discount['amount'] += $discount_rule->item_discount_amount;
                $discount['rate'] += $discount_rule->item_discount_rate;
                $discount['rate'] += $discount_rule->total_discount_rate;
            }
        }
        
        $discount['rate'] = $discount['rate'] * 100;

        
        $page->coupon_discount = $discount;
    }
    
    public function hook_LC_Page_Cart_action_after(LC_Page_Ex $page) {
        $discount_rules = $this->getCurrentDiscountRules();
        $page->tpl_coupon_using = (bool)$discount_rules;
        
        $carts = new SC_CartSession_Ex();
        $totalIncTax = 0;
        $used_time = $this->getCouponUsedTime();
        foreach ($carts->getKeys() as $cart_key) {
            $cart = $carts->cartSession[$cart_key];
            $discount = $this->calculateDiscountOfCart($cart, $discount_rules, $used_time);
            $discount = floor($discount);
            
            $total = $page->arrData[$cart_key]['total'];
            $discount = min($discount, $total);
            
            $page->tpl_coupon_discount[$cart_key] = -$discount;
            $page->arrData[$cart_key]['total'] -= $discount;
        }
    }
    
    public function hook_LC_Page_FrontParts_Bloc_Cart_action_after(LC_Page_Ex $page) {
        $discount_rules = $this->getCurrentDiscountRules();
        $page->tpl_coupon_using = (bool)$discount_rules;
        
        $carts = new SC_CartSession_Ex();
        $discount = 0;
        $used_time = $this->getCouponUsedTime();
        foreach ($carts->getKeys() as $cart_key) {
            $cart = $carts->cartSession[$cart_key];
            $discount += $this->calculateDiscountOfCart($cart, $discount_rules, $used_time);
        }
        $discount = floor($discount);
        
        $page->tpl_coupon_total_discount = $discount;
    }
    
    /**
     * @param LC_Page_EX $page
     */
    public function hook_LC_Page_Shopping_Payment_action_before(LC_Page_EX $page) {
    }
    
    /**
     * @param LC_Page_EX $page
     */
    public function hook_LC_Page_Shopping_Payment_action_after(LC_Page_EX $page) {
        switch ($page->getMode()) {
            case 'confirm':
                if ($this->arrErr) {
                    break;
                }

                $carts = new SC_CartSession_Ex();
                $cart = $carts->cartSession[$page->cartKey];
                $discount_rules = $this->getCurrentDiscountRules();
                $used_time = $this->getCouponUsedTime();
                $discount = $this->calculateDiscountOfCart($cart, $discount_rules, $used_time);

                $purchase = new SC_Helper_Purchase_Ex();
                $params = array(
                    'discount' => $discount,
                );
                $purchase->saveOrderTemp($page->tpl_uniqid, $params);
                return;
        }
        
        $carts = new SC_CartSession_Ex();
        $discount_rules = $this->getCurrentDiscountRules();
        $used_time = $this->getCouponUsedTime();
        $cart_key = $page->cartKey;
        $cart = $carts->cartSession[$cart_key];
        $discount = $this->calculateDiscountOfCart($cart, $discount_rules, $used_time);
        $discount = floor($discount);
        $discount = min($discount, $page->arrPrices['subtotal']);
        $_SESSION['plg_AnCoupon']['cart'][$cart_key]['discount'] = $discount;
        
        $page->arrPrices['subtotal'] -= $discount;
        
        if (isset($_REQUEST['coupon_point_error'])) {
            $page->arrErr['use_point'] = '※ ご利用ポイントがご購入金額を超えています。';
        }
    }
    
    /**
     * @param LC_Page_EX $page
     */
    public function hook_LC_Page_Shopping_Payment_action_confirm(LC_Page_EX $page) {
        $purchase = new SC_Helper_Purchase_Ex();
        $order_temp_id = $page->tpl_uniqid;
        $order = $purchase->getOrderTemp($order_temp_id);
        $session = unserialize($order['session']);
        $cart_key = $session['cartKey'];
        $discount = $session['plg_AnCoupon']['cart'][$cart_key]['discount'];
        
        if (USE_POINT) {
            $subtotal = $page->arrPrices['subtotal'];
            $used_point = $order['use_point'];

            $max_point = floor(($subtotal - $discount) / POINT_VALUE);
            if ($used_point > $max_point) {
                SC_Response_Ex::sendRedirect(SHOPPING_PAYMENT_URLPATH . '?coupon_point_error=1');
            }
        }
        
        $params = array(
            'discount' => $discount,
        );
        $purchase->saveOrderTemp($order_temp_id, $params);
    }
    
    /**
     * @param LC_Page_EX $page
     */
    public function hook_LC_Page_Admin_Order_Edit_action_after(LC_Page_EX $page) {
        $order_id = $page->arrForm['order_id']['value'];
        
        $query = SC_Query_Ex::getSingletonInstance();
        $columns = "coupon.coupon_id, coupon.code AS coupon_code, order_coupon.discount";
        $from = <<<__SQL__
plg_AnCoupon_order_coupon AS order_coupon
LEFT JOIN plg_AnCoupon_coupon AS coupon ON coupon.coupon_id = order_coupon.coupon_id
__SQL__;
        $where = <<<__SQL__
order_coupon.order_id = ?
__SQL__;
        $where_params = array($order_id);
        @list($order_coupon) = $query->select($columns, $from, $where, $where_params);
        $page->tpl_order_coupon = $order_coupon;
    }
    
    /**
     * @param LC_Page_EX $page
     */
    public function hook_LC_Page_Shopping_Confirm_action_before(LC_Page_EX $page) {
        switch ($page->getMode()) {
            case 'confirm':
                $coupon_codes = $this->getUsingCouponCodes();
                $used_time = $this->getCouponUsedTime();
                foreach ($coupon_codes as $coupon_code) {
                    $coupon = An_Eccube_Coupon::findByCode($coupon_code);
                    if (!$coupon || !$coupon->isAvailable($used_time)) {
                        $this->clearUsingCouponCode();
                        $destination = CART_URLPATH;
                        $path = ROOT_URLPATH . 'cart/plg_AnCoupon_coupon_use.php?coupon_expired_error=1&destination=' . rawurldecode($destination);
                        SC_Response_Ex::sendRedirect($path);
                    }
                }
                break;
        }
    }
    
    /**
     * @param LC_Page_EX $page
     */
    public function hook_LC_Page_Shopping_Confirm_action_after(LC_Page_EX $page) {
        switch ($page->getMode()) {
            case 'return':
            case 'confirm':
                break;
                
            default:
                $purchase = new SC_Helper_Purchase_Ex();
                $order_temp_id = $page->tpl_uniqid;
                $order = $purchase->getOrderTemp($order_temp_id);
                $discount = $order['discount'];
                $page->tpl_coupon_discount = -$discount;
                break;
        }
    }
    
    /**
     * @param LC_Page_EX $page
     */
    public function hook_LC_Page_Shopping_Confirm_action_confirm(LC_Page_EX $page) {
        $purchase = new SC_Helper_Purchase_Ex();
        $order_temp_id = $page->tpl_uniqid;
        $order = $purchase->getOrderTemp($order_temp_id);
        $session = unserialize($order['session']);
        $cart_key = $session['cartKey'];
        $discount = $session['plg_AnCoupon']['cart'][$cart_key]['discount'];

        $order_id = $_SESSION['order_id'];

        $coupon_codes = array_keys($session['plg_AnCoupon']['using_coupons']);
        foreach ($coupon_codes as $coupon_code) {
            $coupon = An_Eccube_Coupon::findByCode($coupon_code);
            $coupon->useToOrder($order_id, $discount);
        }
        
        $this->clearUsingCouponCode();
    }
}
