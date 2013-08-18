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
     * @var stdClass
     */
    static $settings;
    
    /**
     * コンストラクタ
     */
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
        
        // オートローダー用にライブラリへのパスを追加。
        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PLUGIN_UPLOAD_REALDIR . '/AnCoupon/library');
    }
    
    /**
     * プラグインをインストールします。
     *
     * @param array $info プラグイン情報(dtb_plugin)
     * @return void
     */
    public function install($info) {
        // プラグインロゴを配置。
        copy(PLUGIN_UPLOAD_REALDIR . "AnCoupon/logo.png", PLUGIN_HTML_REALDIR . "AnCoupon/logo.png");
    }
    
    /**
     * プラグインをアンインストールします。
     *
     * @param array $info プラグイン情報
     * @return void
     */
    public function uninstall($info) {
        // プラグイン用のHTMLディレクトリを削除。
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . 'AnCoupon');
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
     * @param SC_Helper_Plugin $plugin
     * @param int $priority
     */
    public function register(SC_Helper_Plugin $plugin, $priority) {
        parent::register($plugin, $priority);
    }
}
