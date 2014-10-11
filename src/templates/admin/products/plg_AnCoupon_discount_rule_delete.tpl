<form name="form1" id="form1" method="post" action="?">
    <div id="discount_rule" class="contents-main">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="delete" />
        <input type="hidden" name="context" value="<!--{$context|h}-->" />

        <h2>削除する割引条件</h2>

        <table class="list center">
            <thead>
                <tr>
                    <th>割引条件名</th>
                    <th>更新日時</th>
                </tr>
            </thead>
            <tbody>
                <!--{foreach from=$discount_rules|smarty:nodefaults item=discount_rule key=index}-->
                    <tr>
                        <td><!--{$discount_rule->name|h}--></td>
                        <td><!--{$discount_rule->update_date|sfDispDBDate}--></td>
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
                    <!--{if $discount_rules|smarty:nodefaults}-->
                        <li><a class="btn-action" href="javascript:;" onclick="fnSetFormSubmit('form1', 'mode', 'delete'); return false;"><span class="btn-next">削除する</span></a></li>
                    <!--{/if}-->
                    <li><a class="btn-action" href="plg_AnCoupon_discount_rule_list.php"><span class="btn-next">一覧に戻る</span></a></li>
                </ul>
            </div>
        </div>
    </div>
</form>
