<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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

<p class="postage">
    <!--{if $tpl_coupon_using}-->
        <span class="point_announce">クーポンで商品金額から</span><span class="price"><!--{$tpl_coupon_total_discount}-->円</span>割引されます！
        <ul>
            <li><a href="<!--{$smarty.const.ROOT_URLPATH}-->cart/plg_AnCoupon_coupon_status.php" class="coupon_status_link">クーポンを確認する</a></li>
            <li><a href="<!--{$smarty.const.ROOT_URLPATH}-->cart/plg_AnCoupon_coupon_cancel.php" class="coupon_cancel_link">クーポンを使わない</a></li>
        </ul>
    <!--{else}-->
        <span class="point_announce"><a href="<!--{$smarty.const.ROOT_URLPATH}-->cart/plg_AnCoupon_coupon_use.php" class="coupon_use_link">
        クーポンのご使用で割引を受けられます！お持の方はこちらから</a></span>
    <!--{/if}-->
</p>
