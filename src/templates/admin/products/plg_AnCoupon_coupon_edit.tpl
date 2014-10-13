<!--{*
 * アフィリナビクーポンプラグイン
 * Copyright (C) 2014 M-soft All Rights Reserved.
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

<h2>クーポンの内容</h2>

<form name="form1" id="form1" method="post">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="save" />
    <input type="hidden" name="context" value="<!--{$context|h}-->" />

    <div id="coupon" class="contents-main">
        <table class="form">
            <tr>
                <th><!--{$form.code.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.code.error}--><span class="attention"><!--{$form.code.error}--></span><!--{/if}-->
                    <input type="text" name="code" value="<!--{$form.code.value|h}-->" maxlength="<!--{$form.code.maxlength|h}-->" size="30" class="box30" <!--{if $form.code.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
                    クーポンコードに使用出来る文字: <code><!--{$acceptable_chars|h}--></code>
                </td>
            </tr>
            <tr>
                <th><!--{$form.enabled.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.enabled.error}--><span class="attention"><!--{$form.enabled.error}--></span><!--{/if}-->
                    <!--{html_radios name="enabled" options=$form.enabled.options selected=$form.enabled.value separator='<br />'}-->
                </td>
            </tr>
            <tr>
                <th><!--{$form.discount_rule.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.discount_rule.error}--><span class="attention"><!--{$form.discount_rule.error}--></span><!--{/if}-->
                    <select name="discount_rule" <!--{if $form.discount_rule.error}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                        <!--{html_options options=$form.discount_rule.options selected=$form.discount_rule.value}-->
                    </select>
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

        <h2>使用条件</h2>

        <table class="form">
            <tr>
                <th><!--{$form.limit_uses.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.limit_uses.error}--><span class="attention"><!--{$form.limit_uses.error}--></span><!--{/if}-->
                    <!--{html_radios name="limit_uses" options=$form.limit_uses.options selected=$form.limit_uses.value separator=' '}-->
                </td>
            </tr>
            <tr>
                <th><!--{$form.max_uses.title|h}--><span class="attention"> *</span></th>
                <td>
                    <!--{if $form.max_uses.error}--><span class="attention"><!--{$form.max_uses.error}--></span><!--{/if}-->
                    <input type="text" name="max_uses" value="<!--{$form.max_uses.value|h}-->" maxlength="<!--{$form.max_uses.maxlength|h}-->" size="10" class="box10" <!--{if $form.max_uses.error}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th rowspan="2">有効期間 <span class="attention"> *</span></th>
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
                    </select>日 まで使用可能<br />
                    <span class="attention">※割引条件の適用期間とは別にチェックされます。</span>
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
