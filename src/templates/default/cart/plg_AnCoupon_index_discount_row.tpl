<!--{if $tpl_coupon_using}-->
<tr>
    <th colspan="5" class="alignR">
        クーポン割引
        <!--{if $tpl_coupon_restricts.minimum_subtotal}-->
            <br /><span class="note">※購入金額の小計が<!--{$tpl_coupon_restricts.minimum_subtotal|number_format}-->円以上になる場合のみ適用</span>
        <!--{/if}-->
    </th>
    <td class="alignR"><span class="price"><!--{$tpl_coupon_discount[$key]|number_format}-->円</span></td>
</tr>
<!--{/if}-->