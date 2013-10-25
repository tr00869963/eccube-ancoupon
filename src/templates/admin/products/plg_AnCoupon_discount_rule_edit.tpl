<!--{*
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
 *}-->

<form name="form1" id="form1" method="post">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="save" />
    <input type="hidden" name="page_context_id" value="<!--{$context->id|h}-->" />
    <input type="hidden" name="anchor_key" value="" />

    <div id="discount_rule" class="contents-main">
        <h2>割引情報</h2>
    
        <table class="form">
            <tr>
                <th><!--{$form.name.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.name.error}--><span class="attention"><!--{$form.name.error}--></span><!--{/if}-->
                    <input type="text" name="name" value="<!--{$form.name.value|h}-->" maxlength="<!--{$form.name.maxlength|h}-->" size="60" class="box60" <!--{if $form.name.error}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <!--{*
            <tr>
                <th><!--{$form.code.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.code.error}--><span class="attention"><!--{$form.code.error}--></span><!--{/if}-->
                    <input type="text" name="code" value="<!--{$form.code.value|h}-->" maxlength="<!--{$form.code.maxlength|h}-->" size="30" class="box30" <!--{if $form.code.error}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            *}-->
            <tr>
                <th><!--{$form.enabled.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.enabled.error}--><span class="attention"><!--{$form.enabled.error}--></span><!--{/if}-->
                    <!--{html_radios name="enabled" options=$form.enabled.options selected=$form.enabled.value separator='<br />'}-->
                </td>
            </tr>
            <tr>
                <th><!--{$form.memo.title|h}--></th>
                <td>
                    <!--{if $form.memo.error}--><span class="attention"><!--{$form.memo.error}--></span><!--{/if}-->
                    <textarea name="memo" maxlength="<!--{$form.memo.maxlength|h}-->" size="80" rows="6" class="area80" <!--{if $form.memo.error}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$form.memo.value|h}--></textarea>
                </td>
            </tr>
        </table>

        <h2>適用条件</h2>
        
        <table class="form">
            <tr>
                <th rowspan="2">適用期間 <span class="attention"> *</span></th>
                <td>
                    <!--{if $form.effective_from.error}--><span class="attention"><!--{$form.effective_from.error}--></span><!--{/if}-->
                    <select name="effective_from_year" <!--{if $form.effective_from_year.error || $form.effective_from.error}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                        <!--{html_options options=$form.effective_from_year.options selected=$form.effective_from_year.value}-->
                    </select>年
                    <select name="effective_from_month" <!--{if $form.effective_from_month.error || $form.effective_from.error}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                        <!--{html_options options=$form.effective_from_month.options selected=$form.effective_from_month.value}-->
                    </select>月
                    <select name="effective_from_day" <!--{if $form.effective_from_day.error || $form.effective_from.error}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                        <!--{html_options options=$form.effective_from_day.options selected=$form.effective_from_day.value}-->
                    </select>日 から
                </td>
            </tr>
            <tr>
                <td>
                    <!--{if $form.effective_to.error}--><span class="attention"><!--{$form.effective_to.error}--></span><!--{/if}-->
                    <select name="effective_to_year" <!--{if $form.effective_to_year.error || $form.effective_to.error}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                        <!--{html_options options=$form.effective_to_year.options selected=$form.effective_to_year.value}-->
                    </select>年
                    <select name="effective_to_month" <!--{if $form.effective_to_month.error || $form.effective_to.error}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                        <!--{html_options options=$form.effective_to_month.options selected=$form.effective_to_month.value}-->
                    </select>月
                    <select name="effective_to_day" <!--{if $form.effective_to_day.error || $form.effective_to.error}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                        <!--{html_options options=$form.effective_to_day.options selected=$form.effective_to_day.value}-->
                    </select>日 までの間に発生した注文に適用する
                </td>
            </tr>
            <tr>
                <th>対象ユーザー</th>
                <td>
                    <!--{if $form.allow_guest.error}--><span class="attention"><!--{$form.allow_guest.error}--></span><!--{/if}-->
                    <label><input type="checkbox" name="allow_guest" value="1" <!--{if $form.allow_guest.value}-->checked="checked"<!--{/if}--> <!--{if $form.allow_guest.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /> <!--{$form.allow_guest.title|h}--></label><br /> 
                    <!--{if $form.allow_member.error}--><span class="attention"><!--{$form.allow_member.error}--></span><!--{/if}-->
                    <label><input type="checkbox" name="allow_member" value="1" <!--{if $form.allow_member.value}-->checked="checked"<!--{/if}--> <!--{if $form.allow_member.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /> <!--{$form.allow_member.title|h}--></label><br /> 
                </td>
            </tr>
            <tr>
                <th><!--{$form.minimum_subtotal.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.minimum_subtotal.error}--><span class="attention"><!--{$form.minimum_subtotal.error}--></span><!--{/if}-->
                    購入する商品の小計が <input type="text" name="minimum_subtotal" value="<!--{$form.minimum_subtotal.value|h}-->" maxlength="<!--{$form.minimum_subtotal.maxlength|h}-->" size="6" class="box6" <!--{if $form.minimum_subtotal.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円以上
                </td>
            </tr>
        </table>

        <h2>全体割引</h2>
        
        <p>購入した商品を合計したものに対して割引を適用します。商品を問わずに割引したい時にご指定下さい。全体割引は商品割引の適用後に適用されます。</p>
        
        <table class="form">
            <tr>
                <th><!--{$form.total_discount_amount.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.total_discount_amount.error}--><span class="attention"><!--{$form.total_discount_amount.error}--></span><!--{/if}-->
                    小計から <input type="text" name="total_discount_amount" value="<!--{$form.total_discount_amount.value|h}-->" maxlength="<!--{$form.total_discount_amount.maxlength|h}-->" size="6" class="box6" <!--{if $form.total_discount_amount.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円引き
                </td>
            </tr>
            <tr>
                <th><!--{$form.total_discount_rate.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.total_discount_rate.error}--><span class="attention"><!--{$form.total_discount_rate.error}--></span><!--{/if}-->
                    小計から <input type="text" name="total_discount_rate" value="<!--{$form.total_discount_rate.value|h}-->" maxlength="<!--{$form.total_discount_rate.maxlength|h}-->" size="6" class="box6" <!--{if $form.total_discount_rate.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /> %引き<br />
                    ※比例割引の適用は定額割引の適用後に行われます。
                </td>
            </tr>
        </table>

        <h2 id="product">商品割引</h2>
        
        <p>指定した商品毎に割引を適用します。割引情報は商品の価格欄に表示されます。</p>
        
        <table class="form">
            <tr>
                <th><!--{$form.item_discount_amount.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.item_discount_amount.error}--><span class="attention"><!--{$form.item_discount_amount.error}--></span><!--{/if}-->
                    商品価格から <input type="text" name="item_discount_amount" value="<!--{$form.item_discount_amount.value|h}-->" maxlength="<!--{$form.item_discount_amount.maxlength|h}-->" size="6" class="box6" <!--{if $form.item_discount_amount.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円引き
                </td>
            </tr>
            <tr>
                <th><!--{$form.item_discount_rate.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.item_discount_rate.error}--><span class="attention"><!--{$form.item_discount_rate.error}--></span><!--{/if}-->
                    商品価格から <input type="text" name="item_discount_rate" value="<!--{$form.item_discount_rate.value|h}-->" maxlength="<!--{$form.item_discount_rate.maxlength|h}-->" size="6" class="box6" <!--{if $form.item_discount_rate.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /> %引き<br />
                    ※比例割引の適用は定額割引の適用後に行われます。
                </td>
            </tr>
            <tr>
                <th id="target_category">対象カテゴリ</th>
                <td>
                    <!--{if $form.category.error}--><span class="attention"><!--{$form.category.error}--></span><!--{/if}-->
                    <!--{html_checkboxes name="category" options=$form.category.options selected=$form.category.value assign=options}-->
                    <!--{foreach from=$options item=option key=key}-->
                        <!--{section loop=$form.category.levels[$key] name=indent start=1}--><span class="indent">&nbsp;&nbsp;&nbsp;&nbsp;</span><!--{/section}-->
                        <!--{$option}-->
                        (<!--{$form.category.product_numbers[$key]|number_format}-->点該当)<br />
                    <!--{/foreach}-->
                </td>
            </tr>
            <tr>
                <th id="target_product">対象商品</th>
                <td>
                    <!--{if $form.product.error}--><span class="attention"><!--{$form.product.error}--></span><!--{/if}-->
                    <!--{if $products}-->
                        <!--{html_checkboxes name="product_remove" options=$form.product_remove.options assign=delete}-->
                        <table class="list">
                            <tr>
                                <th>商品名</th>
                                <th>販売価格</th>
                                <th>操作</th>
                            </tr>
                            <!--{foreach from=$products item=product key=key}-->
                                <tr>
                                    <td>
                                        <!--{$product.name|h}-->
                                    </td>
                                    <td class="right">
                                        <!--{strip}-->
                                            <!--{if $product_min_price == $product.max_price}-->
                                                <!--{$product.min_price|number_format|h}-->円
                                            <!--{else}-->
                                                <!--{$product.min_price|number_format|h}-->～
                                                <!--{$product.max_price|number_format|h}-->円
                                            <!--{/if}-->
                                        <!--{/strip}-->
                                    </td>
                                    <td class="center">
                                        <!--{assign var=index value=$form.product_remove.map[$key]}--><!--{$delete[$index]}-->
                                    </td>
                                </tr>
                            <!--{/foreach}-->
                        </table>
                    <!--{/if}-->
                    <a class="btn-normal" href="javascript:;" name="product_add" onclick="win03('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/plg_AnCoupon_product_select.php?id_element=product_new', 'search', '615', '500'); return false;">商品の追加</a>
                    <input type="hidden" id="product_new" name="product_new" value="" />
                </td>
            </tr>
            <tr>
                <th id="target_product_class">対象規格</th>
                <td>
                    <!--{if $form.product_class.error}--><span class="attention"><!--{$form.product_class.error}--></span><!--{/if}-->
                    <!--{if $product_classes}-->
                        <!--{html_checkboxes name="product_class_remove" options=$form.product_class_remove.options assign=delete}-->
                        <table class="list">
                            <tr>
                                <th>商品コード</th>
                                <th>商品名/規格1/規格2</th>
                                <th>販売価格</th>
                                <th>操作</th>
                            </tr>
                            <!--{foreach from=$product_classes item=product_class key=key}-->
                                <tr>
                                    <td><!--{$product_class.product_code|h}--></td>
                                    <td>
                                        <!--{strip}-->
                                        <!--{$product_class.product_name|h}-->/
                                        <!--{$product_class.classcategory1_name|default:"(なし)"|h}-->/
                                        <!--{$product_class.classcategory2_name|default:"(なし)"|h}-->
                                        <!--{/strip}-->
                                    </td>
                                    <td class="right"><!--{$product_class.price02|number_format|h}-->円</td>
                                    <td class="center"><!--{assign var=index value=$form.product_class_remove.map[$key]}--><!--{$delete[$index]}--></td>
                                </tr>
                            <!--{/foreach}-->
                        </table>
                    <!--{/if}-->
                    <a class="btn-normal" href="javascript:;" name="product_class_add" onclick="win03('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/plg_AnCoupon_product_select.php?class_required=1&id_element=product_class_new', 'search', '615', '500'); return false;">規格の追加</a>
                    <input type="hidden" id="product_class_new" name="product_class_new" value="" />
                </td>
            </tr>
        </table>

        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnSetFormSubmit('form1', 'mode', 'save'); return false;"><span class="btn-next">この内容で保存する</span></a></li>
            </ul>
        </div>
    </div>
</form>