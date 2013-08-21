<!--{if !$tpl_coupon_using}-->
<tr class="coupon">
    <th colspan="5" class="alignR">クーポンコードをお持ちの方は割引を受けられます(割引対象商品に限ります)</th>
    <td class="alignR"><a href="<!--{$smarty.const.ROOT_URLPATH|h}-->cart/plg_AnCoupon_coupon_use.php?destination=<!--{$smarty.const.CART_URLPATH|escape:'urlencode'|h}-->" class="coupon_use_link">クーポンコードを入力</a></td>
</tr>
<!--{/if}-->
