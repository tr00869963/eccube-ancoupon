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

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class plg_AnCoupon_LC_Page_Api extends LC_Page_Ex
{
    public function init()
    {
        parent::init();

        // プラグインが無効になっている状態で呼び出される場合があるため。
        if (!class_exists('AnCoupon')) {
            require_once PLUGIN_UPLOAD_REALDIR . '/AnCoupon/AnCoupon.php';
            AnCoupon::setupAutoloader();
        }
    }

    public function process()
    {
        $request = An_Eccube_ApiRequest::createFromCurrentRequest();
        $response = new An_Eccube_ApiResponse();

        $plugin = AnCoupon::getInstance();
        if (!$plugin) {
            $response->setError('Plugin is unavailable.');
            $this->sendResponse($response);
            return;
        }

        $info = $plugin->getPluginInfo();
        if (!$info['enable']) {
            $response->setError('Plugin is unavailable.');
            $this->sendResponse($response);
            return;
        }

        $apiClass = An_Eccube_Api::getApiClass($request->resource);
        if (!class_exists($apiClass)) {
            $response->setError('Resource not found. resource=' . $request->resource, 400, 'Not Found');
            $this->sendResponse($response);
            return;
        }

        /* @var $api An_Eccube_Api */
        $api = new $apiClass($plugin);
        if ($api->isAuthenticationRequired()) {
            $correct_api_key = $plugin->getSetting('api_key');
            if (!$request->authenticate($correct_api_key)) {
                $response->setError('api_key is invalid token.', 401, 'Unauthorized');
                $this->sendResponse($response);
                return;
            }

            $request->authorize();
        }

        try {
            $api->invoke($request, $response);
            $this->sendResponse($response);
        } catch (BadMethodCallException $e) {
            $response->setError('Unsupported method given. method=' . $request->method, 405, 'Method Not Allowed');
            $this->sendResponse($response);
        } catch (Exception $e) {
            $response->setError('Server error occered.', 500, 'Internal Server Error');
            $this->sendResponse($response);
        }
    }

    public function sendResponse(An_Eccube_ApiResponse $response)
    {
        $plugin_helper = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
        $this->doLocalHookpointAfter($plugin_helper);

        $plugin_helper->doAction('LC_Page_process', array($this));

        $response->send();
    }
}
