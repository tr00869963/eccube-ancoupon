<?php

// スクリプト名はポイ
array_shift($argv);

/// EC-CUBEのHTMLディレクトリ
$eccube_html_dir = array_shift($argv);

if (!file_exists($eccube_html_dir) || !is_dir($eccube_html_dir)) {
    fwrite(STDERR, 'Not found EC-CUBE html directory. ' . PHP_EOL . 'given path: ' . $eccube_html_dir . PHP_EOL);
    exit(1);
}

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

$schema = AN_Eccube_DbUtils::buildDatabaseSchema($query, $tables, $sequences);
echo AN_Eccube_Utils::encodeJson($schema);
