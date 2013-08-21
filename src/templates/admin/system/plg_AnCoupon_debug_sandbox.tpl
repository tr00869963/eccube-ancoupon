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

<!--{if $executed}-->
<h2>実行結果</h2>
    <div class="result-commit">
        <b>トランザクション: <!--{if $transaction == 'use_commit'}-->コミット<!--{elseif $transaction == 'use_rollback'}-->ロールバック<!--{else}-->使用しない<!--{/if}--></b>
        / <b>表示: <!--{$output_format}--></b>

    </div>
    <!--{if $error}-->
        <div class="result-error">
            <p><span class="attention"><!--{$error|h}--></span></p>
        </div>
    <!--{/if}-->
    <div class="result-contents" style="margin: 2em 0">
        <!--{if $output_format == "text/html"}-->
            <!--{$result}-->
        <!--{elseif $output_format == "application/json"}-->
            <!--{$result}-->
        <!--{else}-->
            <pre><!--{$result|h}--></pre>
        <!--{/if}-->
    </div>
<!--{/if}-->

<h2>デバッグ</h2>

<form name="form1" id="form1" method="post">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="execute" />
    <input type="hidden" name="page_context_id" value="<!--{$context->id|h}-->" />

    <table class="form">
        <tr>
            <th colspan="4"><!--{$form.code.title|h}--><span class="attention"> *</span></th>
        </tr>
        <tr>
            <td colspan="4">
                <!--{if $form.code.error}--><span class="attention"><!--{$form.code.error}--></span><!--{/if}-->
                <textarea name="code" maxlength="<!--{$form.code.maxlength|h}-->" size="80" rows="30" class="area90" wrap="off" style="width: 100%;" <!--{if $form.code.error}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$form.code.value|h}--></textarea>
            </td>
        </tr>
        <tr>
            <th><!--{$form.transaction.title|h}--></th>
            <td>
                <!--{if $form.transaction.error}--><span class="attention"><!--{$form.transaction.error}--></span><!--{/if}-->
                <!--{html_radios name="transaction" options=$form.transaction.options selected=$form.transaction.value separator=' '}-->
            </td>
            <th><!--{$form.output_format.title|h}--><span class="attention"> *</span></th>
            <td>
                <!--{if $form.output_format.error}--><span class="attention"><!--{$form.output_format.error}--></span><!--{/if}-->
                <!--{html_radios name="output_format" options=$form.output_format.options selected=$form.output_format.value separator=' '}-->
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnSetFormSubmit('form1', 'mode', 'execute'); return false;"><span class="btn-next">実行する</span></a></li>
        </ul>
    </div>
</form>
