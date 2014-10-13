<!--{*
 * アフィリナビクーポンプラグイン
 * Copyright (C) 2014 M-soft All Rights Reserved.
 * http://m-soft.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *}-->

<hr class="dashed" />
<!--{if $tpl_coupon_using}-->
    <p class="attention coupon_discount_area">
        クーポンで<span class="coupon_discount"><!--{$tpl_coupon_total_discount|number_format|h}--></span>円割引されます
        <ul>
            <li><a href="<!--{$smarty.const.ROOT_URLPATH}-->cart/plg_AnCoupon_coupon_status.php" class="coupon_status_link">クーポンを確認する</a></li>
            <li><a href="<!--{$smarty.const.ROOT_URLPATH}-->cart/plg_AnCoupon_coupon_cancel.php" class="coupon_cancel_link">クーポンを使わない</a></li>
        </ul>
    </p>
<!--{else}-->
    <p class="attention coupon_discount_area">
        <a href="<!--{$smarty.const.ROOT_URLPATH}-->cart/plg_AnCoupon_coupon_use.php" class="coupon_use_link">クーポンを使用する</a>
    </p>
<!--{/if}-->
