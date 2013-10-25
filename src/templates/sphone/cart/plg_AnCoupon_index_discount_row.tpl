<!--{if $tpl_coupon_using}-->
    <div>
        <!--{strip}-->
        <span class="mini">クーポン割引
        <!--{if $tpl_coupon_restricts.minimum_subtotal}-->
            <span class="mini">(<!--{$tpl_coupon_restricts.minimum_subtotal|number_format}-->円以上ご購入時にのみ)</span>
        <!--{/if}-->
        ：</span><span class="coupon-discount"><!--{$tpl_coupon_discount[$key]|number_format}-->円</span>
        <!--{/strip}-->
    </div>
<!--{/if}-->
