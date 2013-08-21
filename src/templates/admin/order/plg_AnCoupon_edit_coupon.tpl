<!--{if $tpl_order_coupon}-->
    <h2 id="order_coupon">
        クーポン情報
    </h2>
    <table class="form">
        <tr>
            <th>使用されたクーポン</th>
            <td>
                <a href="../products/plg_AnCoupon_coupon_edit.php?coupon_id=<!--{$tpl_order_coupon.coupon_id|h}-->"><!--{$tpl_order_coupon.coupon_code|h}--></a>
            </td>
        </tr>
        <tr>
            <th>割引額</th>
            <td>
                <!--{$tpl_order_coupon.discount|number_format|h}-->円
            </td>
        </tr>
    </table>
<!--{/if}-->
