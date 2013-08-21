<?php

$eccube_html_dir = 'E:/Www/dev/eccube-2.12/html';

require_once "{$eccube_html_dir}/require.php";

$query = SC_Query_Ex::getSingletonInstance();

$tables = array(
    'plg_AnCoupon_coupon' => array(),
    'plg_AnCoupon_coupon_discount_rule' => array(),
    'plg_AnCoupon_discount_rule' => array(),
    'plg_AnCoupon_discount_rule_category' => array(),
    'plg_AnCoupon_discount_rule_product' => array(),
    'plg_AnCoupon_discount_rule_product_class' => array(),
    'plg_AnCoupon_order_coupon' => array(),
);

$sequences = array(
    'plg_AnCoupon_coupon_coupon_id' => 1,
    'plg_AnCoupon_discount_code' => 1,
    'plg_AnCoupon_discount_rule_id' => 1,
);

$schema = AN_Eccube_Utils::buildDatabaseSchema($query, $tables, $sequences);
echo AN_Eccube_Utils::encodeJson($schema);
