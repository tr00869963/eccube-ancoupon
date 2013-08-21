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

<h2>クーポン設定</h2>

<div id="coupon" class="contents-main">
    <table class="form">
        <tr>
            <th><!--{$form.code.title|h}--><span class="attention"> *</span></th>
            <td>
                <!--{if $form.code.error}--><span class="attention"><!--{$form.code.error}--></span><!--{/if}-->
                <input type="text" name="code" value="<!--{$form.code.value|h}-->" maxlength="<!--{$form.code.maxlength|h}-->" size="30" class="box30" <!--{if $form.code.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
                クーポンコードに使用出来る文字: <!--{$coupon_code_chars|h}-->
            </td>
        </tr>
        <tr>
            <th><!--{$form.discount_method.title|h}--><span class="attention"> *</span></th>
            <td>
                <!--{if $form.discount_method.error}--><span class="attention"><!--{$form.discount_method.error}--></span><!--{/if}-->
                <!--{html_radios name="discount_method" options=$form.discount_method.options selected=$form.discount_method.value separator='<br />'}-->
            </td>
        </tr>
        <tr>
            <th><!--{$form.discount_price.title|h}--><span class="attention"> *</span></th>
            <td>
                <!--{if $form.discount_price.error}--><span class="attention"><!--{$form.discount_price.error}--></span><!--{/if}-->
                <input type="text" name="discount_price" value="<!--{$form.discount_price.value|h}-->" maxlength="<!--{$form.discount_price.maxlength|h}-->" size="6" class="box6" <!--{if $form.discount_price.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円引き
            </td>
        </tr>
        <tr>
            <th><!--{$form.discount_rate.title|h}--><span class="attention"> *</span></th>
            <td>
                <!--{if $form.discount_rate.error}--><span class="attention"><!--{$form.discount_rate.error}--></span><!--{/if}-->
                <input type="text" name="discount_rate" value="<!--{$form.discount_rate.value|h}-->" maxlength="<!--{$form.discount_rate.maxlength|h}-->" size="6" class="box6" <!--{if $form.discount_rate.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /> %引き
            </td>
        </tr>
        <tr>
            <th><!--{$form.available_from.title|h}--><span class="attention"> *</span></th>
            <td>
                <!--{if $form.available_from.error}--><span class="attention"><!--{$form.available_from.error}--></span><!--{/if}-->
                <select name="available_from_year" <!--{if $form.available.error}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                    <!--{html_options options=$form.available_from.year_options selected=$form.available_from.year_value}-->
                </select>年
                <select name="available_from_month" <!--{if $form.available_from.error}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                    <!--{html_options options=$form.available_from.year_options selected=$form.available_from.month_value}-->
                </select>月
                <select name="available_from_day" <!--{if $form.available_from.error}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                    <!--{html_options options=$form.available_from.year_options selected=$form.available_from.day_value}-->
                </select>日
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnSetFormSubmit('form1', 'mode', 'save'); return false;"><span class="btn-next">この内容で保存する</span></a></li>
        </ul>
    </div>
</div>
