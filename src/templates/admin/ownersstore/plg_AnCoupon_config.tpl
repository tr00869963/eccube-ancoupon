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
    <input type="hidden" name="context" value="<!--{$context|h}-->" />

    <div class="coupon-settings-form">
        <h3>基本設定</h3>
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

        <h3>AN7との連携</h3>
        <p>当プラグインを介してEC-CUBEとAN7を連携させる際に必要な設定項目です。連携せずに運用される場合は設定する必要はありません。</p>
        <table class="form">
            <tr>
                <th><!--{$form.api_key.title|h}--></th>
                <td>
                    <!--{if $form.api_key.error}--><span class="attention"><!--{$form.api_key.error}--></span><!--{/if}-->
                    <input type="text" name="api_key" value="<!--{$form.api_key.value|h}-->" maxlength="<!--{$form.api_key.maxlength|h}-->" size="30" readonly="readonly" class="box50" <!--{if $form.api_key.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
                    <input type="submit" name="generate_api_key" value="API鍵を生成する" /><br />
                    AN7に対してクーポンや割引情報へのアクセス権限を与えるための文字列です。生成したAPI鍵はAN7の連携アプリケーションの管理で登録して下さい。
                </td>
            </tr>
            <tr>
                <th><!--{$form.an7_api_endpoint.title|h}--></th>
                <td>
                    <!--{if $form.an7_api_endpoint.error}--><span class="attention"><!--{$form.an7_api_endpoint.error}--></span><!--{/if}-->
                    <input type="text" name="an7_api_endpoint" value="<!--{$form.an7_api_endpoint.value|h}-->" maxlength="<!--{$form.an7_api_endpoint.maxlength|h}-->" size="30" class="box50" <!--{if $form.an7_api_endpoint.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
                    AN7のAPIエンドポイントを入力して下さい。APIエンドポイントはAN7の連携アプリケーションの管理から取得できます。
                </td>
            </tr>
            <tr>
                <th><!--{$form.an7_api_key.title|h}--></th>
                <td>
                    <!--{if $form.an7_api_key.error}--><span class="attention"><!--{$form.an7_api_key.error}--></span><!--{/if}-->
                    <input type="text" name="an7_api_key" value="<!--{$form.an7_api_key.value|h}-->" maxlength="<!--{$form.an7_api_key.maxlength|h}-->" size="30" class="box50" <!--{if $form.an7_api_key.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
                    AN7のAPI鍵を入力して下さい。API鍵はAN7の連携アプリケーションの管理から取得できます。
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
