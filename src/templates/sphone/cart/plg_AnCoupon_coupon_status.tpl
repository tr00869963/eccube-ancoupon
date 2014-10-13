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

<div id="undercolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    
    <div class="coupon-discount-status">
        <!--{if $discount_info.restrict_exists}-->
            <h2 class="title_block">割引条件</h2>
            <ul>
                <!--{if $discount_info.restricts.minimum_subtotal}-->
                    <li>
                        <!--{$discount_info.restricts.minimum_subtotal|number_format}-->円以上ご購入時にのみ割引を受けられます。<br />
                        ※各種値引きを適用する前の商品金額で計算します。
                    </tr>
                <!--{/if}-->
            </ul>
        <!--{/if}-->
        
        <!--{if $discount_info.total}-->
            <h2 class="title_block">全体割引</h2>
            
            <p>ご購入金額から割引される価格はこちらです。割引額がご購入金額より多い場合でも返金扱いにはなりませんのでご注意下さい。</p>
            
            <ul>
                <!--{if $discount_info.total_amount}-->
                    <li>
                        ※お買い上げ商品の合計から最大<!--{$discount_info.total_amount|number_format}-->円まで割り引かれます。
                    </li>
                <!--{/if}-->
                <!--{if $discount_info.total_rate}-->
                    <li>
                        ※お買い上げ商品の合計から<!--{$discount_info.total_rate|number_format}-->%割り引かれます。<br />
                        <span class="note">※商品によっては割引率が変わる場合がございます。価格欄に表示される割引情報を必ずご確認下さい。</span>
                    </li>
                <!--{/if}-->
            </ul>
        <!--{/if}-->
        
        <!--{if $discount_info.target}-->
            <h2 class="title_block">商品個別割引</h2>
        
            <p>割引を受けられる商品はこちらです。商品の種類や組み合わせによっては割引を受けられなかったり、割引額が変わる場合があります。価格欄に表示される割引情報を必ずご確認下さい。</p>
    
            <!--{if $discount_info.target_categories}-->
                <navi id="categorytree">
                    <ul class="categorytreelist">
                        <!--{foreach from=$discount_info.target_categories item=category}-->
                            <li class="level1">
                                <span class="category_header"></span>
                                <span class="category_body"><a href="<!--{$smarty.const.ROOT_URLPATH|h}-->products/list.php?category_id=<!--{$category.category_id|h}-->"><!--{$category.name|h}--></a></span>
                            </li>
                        <!--{/foreach}-->
                    </ul>
                </navi>
            <!--{/if}-->
            
            <!--{if $discount_info.target_products}-->
                <navi id="categorytree">
                    <ul class="categorytreelist">
                        <!--{foreach from=$discount_info.target_products item=product}-->
                            <li class="level1">
                                <span class="category_header"></span>
                                <span class="category_body"><a href="<!--{$smarty.const.ROOT_URLPATH|h}-->products/detail.php?product_id=<!--{$product.product_id|h}-->"><!--{$product.name|h}--></a></span>
                            </li>
                        <!--{/foreach}-->
                    </ul>
                </navi>
            <!--{/if}-->
        <!--{/if}-->
    </div>
</div>
