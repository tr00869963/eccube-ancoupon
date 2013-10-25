<!--{*
 * アフィリナビクーポンプラグイン
 * Copyright (C) 2013 M-soft All Rights Reserved.
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
    
    <div id="undercolumn_coupon">
        <!--{if $discount_info.restrict_exists}-->
            <h3>割引条件</h3>
            <table summary="割引条件">
                <tbody>
                    <!--{if $discount_info.restricts.minimum_subtotal}-->
                        <tr>
                            <th>最低購入金額</th>
                            <td>
                                <!--{$discount_info.restricts.minimum_subtotal|number_format}-->円以上ご購入時にのみ割引を受けられます。<br />
                                ※各種値引きを適用する前の商品金額で計算します。
                            </td>
                        </tr>
                    <!--{/if}-->
                </tbody>
            </table>
        <!--{/if}-->
        
        <!--{if $discount_info.total}-->
            <h3>全体割引</h3>
            
            <p>ご購入金額から割引される価格はこちらです。割引額がご購入金額より多い場合でも返金扱いにはなりませんのでご注意下さい。</p>
            
            <table summary="割引額">
                <thead>
                    <tr>
                        <th class="alignC">割引額</th>
                    </tr>
                </thead>
                <tbody>
                    <!--{if $discount_info.total_amount}-->
                        <tr>
                            <td>お買い上げ商品の合計から最大<!--{$discount_info.total_amount|number_format}-->円まで割り引かれます。</td>
                        </tr>
                    <!--{/if}-->
                    <!--{if $discount_info.total_rate}-->
                        <tr>
                            <td>
                                お買い上げ商品の合計から<!--{$discount_info.total_rate|number_format}-->%割り引かれます。<br />
                                <span class="note">※商品によっては割引率が変わる場合がございます。価格欄に表示される割引情報を必ずご確認下さい。</span>
                            </td>
                        </tr>
                    <!--{/if}-->
                </tbody>
            </table>
        <!--{/if}-->
        
        <!--{if $discount_info.target}-->
            <h3>商品個別割引</h3>
        
            <p>割引を受けられる商品はこちらです。商品の種類や組み合わせによっては割引を受けられなかったり、割引額が変わる場合があります。価格欄に表示される割引情報を必ずご確認下さい。</p>
    
            <!--{if $discount_info.target_categories}-->
                <table summary="割引対象商品カテゴリ">
                    <thead>
                        <tr>
                            <th class="alignC">カテゴリ別</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--{foreach from=$discount_info.target_categories item=category}-->
                            <tr>
                                <td><a href="<!--{$smarty.const.ROOT_URLPATH|h}-->products/list.php?category_id=<!--{$category.category_id|h}-->"><!--{$category.name|h}--></a></td>
                            </tr>
                        <!--{/foreach}-->
                    </tbody>
                </table>
            <!--{/if}-->
            
            <!--{if $discount_info.target_products}-->
                <table summary="割引対象商品">
                    <thead>
                        <tr>
                            <th class="alignC">商品別</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--{foreach from=$discount_info.target_products item=product}-->
                            <tr>
                                <td><a href="<!--{$smarty.const.ROOT_URLPATH|h}-->products/detail.php?product_id=<!--{$product.product_id|h}-->"><!--{$product.name|h}--></a></td>
                            </tr>
                        <!--{/foreach}-->
                    </tbody>
                </table>
            <!--{/if}-->
        <!--{/if}-->
    </div>
</div>
