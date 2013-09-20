<!--{assign var=coupon_discount value=$coupon_discounts[$arrProduct.product_id]}-->
<!--{if $coupon_discount.available}-->
    <!--{strip}-->
        <b>この商品はクーポン割引対象です。</b>
        購入時に販売価格より
        <!--{if $coupon_discount.amount}-->
            <!--{$coupon_discount.amount|number_format}-->円
        <!--{/if}-->
        <!--{if $coupon_discount.amount && $coupon_discount.rate}-->
            と
        <!--{/if}-->
        <!--{if $coupon_discount.rate}-->
            <!--{$coupon_discount.rate|h}-->％
        <!--{/if}-->
        割引されます。<br><br>
            
        <!--{if count($coupon_discount.classes) > 1}-->
            <b>割引対象になる組み合わせ:</b><br>
            <!--{foreach from=$coupon_discount.classes item=class}-->
                ◆<!--{$class.classcategory1_name|h}-->
                <!--{if $class.classcategory2_name}-->
                    /<!--{$class.classcategory2_name|h}-->
                <!--{/if}-->&nbsp;
            <!--{/foreach}--><br><br>
        <!--{/if}-->
    <!--{/strip}-->
 <!--{/if}-->
    