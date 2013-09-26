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
 * プラグインのプラグインアップデート
 *
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class plugin_update {
    /**
     * プラグインをアップデートします。
     *
     * @param array $info プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function update($info) {
        $new_info = self::invokeUpTo($info);
        self::savePluginInfo($new_info, $info);
    }
    
    /**
     * プラグインの現在のバージョンに合わせて必要なアップデートを実行します。
     * 
     * @param array $info プラグイン情報の連想配列(dtb_plugin)
     * @return array アップデート後のプラグイン情報の連想配列(dtb_plugin)
     */
    protected static function invokeUpTo($info) {
        $points = self::getMigrationPoints();
        foreach ($points as $point) {
            if (version_compare($info['plugin_version'], $point['version'], '<')) {
                $info = call_user_func($point['up_to'], $info);
            }
        }
        
        return $info;
    }
    
    private static function getMigrationPoints() {
        $points = array();
        $sortkey = array();
        $class = new ReflectionClass(__CLASS__);
        $methods = $class->getMethods();
        $prefix_len = strlen('upTo_');
        foreach ($methods as $method) {
            if (strncmp($method->name, 'upTo_', $prefix_len) == 0) {
                $version = substr($method->name, $prefix_len);
                $version = strtr($version, array('_' => '.'));
                $points[$version] = array(
                    'version' => $version,
                    'up_to' => array($method->class, $method->name),
                );
                $sortkey[] = $version;
            }
        }
        
        natsort($sortkey);
        $sorted = array();
        foreach ($sortkey as $key) {
            $sorted[$key] = $points[$key];
        }
        return $sorted;
    }
    
    /**
     * プラグイン情報を保存します。
     * 
     * @param array $new_info 新しいプラグイン情報の連想配列(dtb_plugin)
     * @param array $old_info 以前のプラグイン情報の連想配列(dtb_plugin)
     */
    protected static function savePluginInfo($new_info, $old_info) {
        $query = SC_Query_Ex::getSingletonInstance();
        $fields = array(
            'plugin_name',
            'plugin_code',
            'class_name',
            'author',
            'author_site_url',
            'plugin_site_url',
            'plugin_version',
            'compliant_version',
            'plugin_description',
            'free_field1',
            'free_field2',
            'free_field3',
            'free_field4',
        );
        $values = array();
        foreach ($fields as $field) {
            $values[$field] = $new_info[$field];
        }
        $query->update('dtb_plugin', $values, 'plugin_code = ?', array($old_info['plugin_code']));
    }

    /**
     * @param array $info
     * @return array
     */
    protected static function upTo_1_1_0($info) {
        $info['plugin_version'] = '1.1.0';
        
        // 不要になった管理者用ページを削除
        $plugin_code = $info['plugin_code'];
        $files = array(
            'system/plg_AnCoupon_debug_sandbox.php' => 'plg_AnCoupon_LC_Page_Admin_System_DebugSandbox.php',
        );
        foreach ($files as $file => $page) {
            $path = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/pages/$page";
            SC_Helper_FileManager_Ex::deleteFile($path, false);
            
            $path = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/html/admin/$file";
            SC_Helper_FileManager_Ex::deleteFile($path, false);
        
            $path = HTML_REALDIR . ADMIN_DIR . $file;
            SC_Helper_FileManager_Ex::deleteFile($path, false);
        }
        
        // 不要になったテンプレートを削除
        $plugin_code = $info['plugin_code'];
        $files = array(
            'admin/system/plg_AnCoupon_debug_sandbox.tpl',
            'admin/system/plg_AnCoupon_subnavi_item.tpl',
        );
        foreach ($files as $file) {
            $path = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/templates/$file";
            SC_Helper_FileManager_Ex::deleteFile($path, false);
        
            $path = SMARTY_TEMPLATES_REALDIR . $file;
            SC_Helper_FileManager_Ex::deleteFile($path, false);
        }
        
        // 管理用のページを更新。
        $plugin_code = $info['plugin_code'];
        $src_dir = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/html/admin/";
        $dest_dir = HTML_REALDIR . ADMIN_DIR;
        SC_Utils::copyDirectory($src_dir, $dest_dir);
        
        // 顧客用のページを更新。
        $src_dir = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/html/customer/";
        $dest_dir = HTML_REALDIR;
        SC_Utils::copyDirectory($src_dir, $dest_dir);
        
        // 公開ファイルを更新。
        $src_dir = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/html/assets/";
        $dest_dir = PLUGIN_HTML_REALDIR . "{$plugin_code}/";
        SC_Utils::copyDirectory($src_dir, $dest_dir);
        
        // テンプレートを更新。
        $src_dir = PLUGIN_UPLOAD_REALDIR . "{$plugin_code}/templates/";
        $dest_dir = SMARTY_TEMPLATES_REALDIR;
        SC_Utils::copyDirectory($src_dir, $dest_dir);
        
        return $info;
    }
}
