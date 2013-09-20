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

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<h2><!--{$tpl_subtitle|h}--></h2>

<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|h}-->">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="save" />
    <input type="hidden" name="page_context_id" value="<!--{$context->id|h}-->" />

    <div class="coupon-settings-form">
        <table class="form">
            <tr>
                <th><!--{$form.acceptable_chars.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.acceptable_chars.error}--><span class="attention"><!--{$form.acceptable_chars.error}--></span><!--{/if}-->
                    <input type="text" name="acceptable_chars" value="<!--{$form.acceptable_chars.value|h}-->" maxlength="<!--{$form.acceptable_chars.maxlength|h}-->" size="60" class="box60" <!--{if $form.acceptable_chars.error}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th><!--{$form.ignorable_chars.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.ignorable_chars.error}--><span class="attention"><!--{$form.ignorable_chars.error}--></span><!--{/if}-->
                    <input type="text" name="ignorable_chars" value="<!--{$form.ignorable_chars.value|h}-->" maxlength="<!--{$form.ignorable_chars.maxlength|h}-->" size="60" class="box60" <!--{if $form.ignorable_chars.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
                    ここで指定した文字はクーポンコード入力時に取り除かれます。クーポンコードに区切り文字を入れたい時にご使用下さい。
                </td>
            </tr>
        </table>

        <div class="btn-area">
            <ul>
                <li>
                    <a class="btn-action" href="#" onclick="window.close(); return false;"><span class="btn-next">閉じる</span></a>
                </li>
                <li>
                    <a class="btn-action" href="#" onclick="document.form1.submit(); return false;"><span class="btn-next">登録する</span></a>
                </li>
            </ul>
        </div>
    </div>
</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
