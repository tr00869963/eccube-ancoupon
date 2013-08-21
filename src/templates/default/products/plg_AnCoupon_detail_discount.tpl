<!--{if $coupon_discount.available}-->
    <div class="coupon-discount">
        <b>この商品はクーポン割引対象です。</b><br />
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
            
        <!--{if count($coupon_discount.classes) > 1}-->
            <div class="coupon-discount-target">
                <b class="label">割引対象になる組み合わせ</b>
                <ul class="coupon-product-class-list>
                    <!--{foreach from=$coupon_discount.classes item=class}-->
                        <li style="display: block; float: none;">
                            <!--{strip}-->
                                <!--{$class.classcategory1_name|h}-->
                                <!--{if $class.classcategory2_name}-->
                                    /<!--{$class.classcategory2_name|h}-->
                                <!--{/if}-->
                            <!--{/strip}-->
                        </li>
                    <!--{/foreach}-->
                </ul>
            </div>
         <!--{/if}-->
    </div>
<!--{/if}-->
