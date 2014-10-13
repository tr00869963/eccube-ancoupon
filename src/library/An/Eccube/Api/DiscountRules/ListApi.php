<?php

class An_Eccube_Api_DiscountRules_ListApi extends An_Eccube_Api
{
    protected function initialize()
    {
        $this->authenticationRequired = true;
    }

    protected function get(An_Eccube_ApiRequest $request, An_Eccube_ApiResponse $response)
    {
        $where = 'enabled = ?';
        $where_params = array(1);
        $limit = null;
        $offset = 0;
        $discount_rules = An_Eccube_DiscountRule::findByWhere('*', $where, $where_params, $limit, $offset, 'name', 'ASC');

        $body = array();
        foreach ($discount_rules as $discount_rule) {
            $item = array(
                'discount_rule_id' => $discount_rule->discount_rule_id,
                'code'             => $discount_rule->code,
                'name'             => $discount_rule->name,
                'memo'             => $discount_rule->memo,
                'effective_from'   => An_Eccube_Utils::toDateTimeWithTimezone($discount_rule->effective_from),
                'effective_to'     => An_Eccube_Utils::toDateTimeWithTimezone($discount_rule->effective_to),
                'create_date'      => An_Eccube_Utils::toDateTimeWithTimezone($discount_rule->create_date),
                'update_date'      => An_Eccube_Utils::toDateTimeWithTimezone($discount_rule->update_date),
            );
            $body[] = $item;
        }

        $response->setBody($body);
    }
}
