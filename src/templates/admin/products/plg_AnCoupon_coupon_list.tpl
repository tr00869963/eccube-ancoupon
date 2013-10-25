<div class="btn">
    <a class="btn-action" href="plg_AnCoupon_coupon_edit.php"><span class="btn-next">クーポンを新規発行</span></a>
    <a class="btn-action" href="plg_AnCoupon_discount_rule_list.php"><span class="btn-next">割引条件の一覧</span></a>
    <a class="btn-action" href="plg_AnCoupon_discount_rule_edit.php"><span class="btn-next">割引条件を追加</span></a>
</div>

<form name="form1" id="form1" method="post" action="?">    
    <div id="coupon" class="contents-main">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="" />
        <input type="hidden" name="page_context_id" value="<!--{$context->id|h}-->" />
        <input name="search_pageno" type="hidden" value="" />

        <h2>発行済みクーポン一覧</h2>
        
        <!--{include file=$tpl_pager}-->
    
        <table class="list center">
            <colgroup>
                <col width="25%" />
                <col width="25%" />
                <col width="5%" />
                <col width="10%" />
                <col width="15%" />
                <col width="10%" />
                <col width="10%" />
            </colgroup>
            <thead>
                <tr>
                    <th>クーポンコード</th>
                    <th>有効期間</th>
                    <th>状態</th>
                    <th>使用回数</th>
                    <th>発行日時</th>
                    <th>編集</th>
                    <th>削除</th>
                </tr>
            </thead>
            <tbody>
                <!--{foreach from=$coupons|smarty:nodefaults item=coupon key=index}-->
                    <tr>
                        <td><!--{$coupon->code|h}--></td>
                        <td><!--{$coupon->effective_from|date_format:'%Y/%m/%d'|h}--> から <!--{$coupon->effective_to|date_format:'%Y/%m/%d'|h}--> まで</td>
                        <td>
                            <!--{if $coupon->enabled}-->
                                有効
                            <!--{else}-->
                                無効
                            <!--{/if}--></td>
                        <td class="right">
                            <!--{if $coupon->limit_uses}-->
                                <!--{$coupon->uses|number_format|h}-->/<!--{$coupon->max_uses|number_format|h}-->
                            <!--{else}-->
                                <!--{$coupon->uses|number_format|h}-->/∞
                            <!--{/if}-->
                        </td>
                        <td><!--{$coupon->create_date|sfDispDBDate}--></td>
                        <td class="center"><a href="plg_AnCoupon_coupon_edit.php?coupon_id=<!--{$coupon->coupon_id|h}-->">編集</a></td>
                        <td class="center"><a href="plg_AnCoupon_coupon_delete.php?coupon_id=<!--{$coupon->coupon_id|h}-->">削除</a></td>
                    </tr>
                <!--{foreachelse}-->
                    <tr>
                        <td colspan="6">表示できるデータはありません。</td>
                    </tr>
                <!--{/foreach}-->
            </tbody>
        </table>
        
        <!--{* 表示条件 *}-->
        <h2>表示条件</h2>
        
        <table class="form">
            <col width="20%" />
            <col width="80%" />
            <tr>
                <th><!--{$form.code.title|h}--></th>
                <td>
                    <!--{if $form.code.error}--><span class="attention"><!--{$form.code.error}--></span><!--{/if}-->
                    <input type="text" name="code" value="<!--{$form.code.value|h}-->" maxlength="<!--{$form.code.maxlength|h}-->" size="30" class="box30" <!--{if $form.code.error}--><!--{sfSetErrorStyle}--><!--{/if}--> /> を含む
                </td>
            </tr>
            <tr>
                <th rowspan="2">有効期間</span></th>
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
                    </select>日以降に利用可能になるもの
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
                    </select>日までで利用不能になるもの
                </td>
            </tr>
        </table>
    
        <div class="btn">
            <p class="page_rows">検索結果表示件数
            <!--{if $form.search_page_max.error}--><span class="attention"><!--{$form.search_page_max.error}--></span><!--{/if}-->
            <select name="search_page_max" style="<!--{$form.search_page_max.error|sfGetErrorColor}-->">
                <!--{html_options options=$form.search_page_max.options selected=$form.search_page_max.value}-->
            </select> 件</p>
    
            <div class="btn-area">
                <ul>
                    <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', '', '', ''); return false;"><span class="btn-next">この条件で再表示する</span></a></li>
                </ul>
            </div>
        </div>
    </div>
</form>
