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

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * フロント画面の基底クラス
 *
 * @package AnCoupon
 * @author M-soft
 */
class plg_AnCoupon_LC_Page extends LC_Page_Ex
{
    /**
     * @var An_Eccube_PageContext
     */
    public $context;

    /**
     *
     * @var string
     */
    protected $defaultMode = 'default';

    public function process()
    {
        parent::process();

        $this->context = $this->restoreContext();

        $this->invoke($this->getMode(), $this->defaultMode);

        $this->sendResponse();
    }

    /**
     *
     * @param string $mode
     * @param string $default
     */
    protected function invoke($mode, $default)
    {
        $mode = $mode != '' ? $mode : $default;
        $method = 'do' . ucfirst(strtolower($mode));
        call_user_func(array($this, $method));
    }

    /**
     * @return Zenith_Eccube_PageContext
     */
    protected function restoreContext()
    {
        $context = $this->createContext();

        if (array_key_exists('context', $_REQUEST)) {
            $context->restore($_REQUEST['context']);
        } else {
            $this->initializeContext($context);
        }

        return $context;
    }

    /**
     * @return An_Eccube_PageContext
     */
    protected function createContext()
    {
        return new An_Eccube_PageContext(array(), $this->getContextSecretKey());
    }

    /**
     *
     * @param An_Eccube_PageContext $context
     */
    protected function initializeContext(An_Eccube_PageContext $context)
    {

    }

    /**
     *
     * @return string
     */
    protected function getContextSecretKey()
    {
        if (isset($_SESSION['plg_AnCoupon']['page_context_secret_key'])) {
            return $_SESSION['plg_AnCoupon']['page_context_secret_key'];
        }

        return $_SESSION['plg_AnCoupon']['page_context_secret_key'] = uniqid();
    }
}
