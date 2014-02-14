<form name="form1" id="form1" method="post" action="?">
    <div id="coupon" class="contents-main">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="delete" />
        <input type="hidden" name="page_context_id" value="<!--{$context->id|h}-->" />

        <h2>削除するクーポンコード</h2>

        <table class="list center">
            <thead>
                <tr>
                    <th>クーポンコード</th>
                    <th>更新日時</th>
                </tr>
            </thead>
            <tbody>
                <!--{foreach from=$coupons|smarty:nodefaults item=coupon key=index}-->
                    <tr>
                        <td><!--{$coupon->code|h}--></td>
                        <td><!--{$coupon->update_date|sfDispDBDate}--></td>
                    </tr>
                <!--{foreachelse}-->
                    <tr>
                        <td colspan="2">削除できる項目がありません。</td>
                    </tr>
                <!--{/foreach}-->
            </tbody>
        </table>

        <div class="btn">
            <div class="btn-area">
                <ul>
                    <!--{if $coupons|smarty:nodefaults}-->
                        <li><a class="btn-action" href="javascript:;" onclick="fnSetFormSubmit('form1', 'mode', 'delete'); return false;"><span class="btn-next">削除する</span></a></li>
                    <!--{/if}-->
                    <li><a class="btn-action" href="plg_AnCoupon_coupon_list.php"><span class="btn-next">一覧に戻る</span></a></li>
                </ul>
            </div>
        </div>
    </div>
</form>
