<!--{assign var=coupon_discount value=$coupon_discounts[$arrProduct.product_id]}-->
<!--{if $coupon_discount.available}-->
    <p>
        <span class="pricebox sale_price"><span class="mini">クーポン割引対象商品:</span></span>
        <span class="price">
            <!--{strip}-->
                購入時に販売価格より<!--{if $coupon_discount.amount}-->
                    <!--{$coupon_discount.amount|number_format}-->円
                <!--{/if}-->
                <!--{if $coupon_discount.amount && $coupon_discount.rate}-->
                    と
                <!--{/if}-->
                <!--{if $coupon_discount.rate}-->
                    <!--{$coupon_discount.rate|h}-->％
                <!--{/if}-->
                割引されます。
            <!--{/strip}-->
        </span>
    </p>
<!--{/if}-->
