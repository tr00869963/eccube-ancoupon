<!--{if $coupon_discount.available}-->
    <p class="coupon_discount">
        <span class="mini">クーポン割引：</span>
        <span class="price mini">
        販売価格から更に<!--{strip}-->
            <!--{if $coupon_discount.amount}-->
                <!--{$coupon_discount.amount|number_format|h}-->円
            <!--{/if}-->
            <!--{if $coupon_discount.amount && $coupon_discount.rate}-->
                と
            <!--{/if}-->
            <!--{if $coupon_discount.rate}-->
                <!--{$coupon_discount.rate|h}-->％
            <!--{/if}-->
        <!--{/strip}-->値引きされます
        </span>
    </p>
            
    <!--{if count($coupon_discount.classes) > 1}-->
        <div class="coupon-discount-target">
            <b class="label">クーポン割引の対象になる組み合わせ：</b>
                <!--{foreach from=$coupon_discount.classes item=class}-->
                        <!--{strip}-->
                            ◆
                            <!--{$class.classcategory1_name|h}-->
                            <!--{if $class.classcategory2_name}-->
                                /<!--{$class.classcategory2_name|h}-->
                            <!--{/if}-->
                        <!--{/strip}-->
                <!--{/foreach}-->
        </div>
     <!--{/if}-->
<!--{/if}-->
