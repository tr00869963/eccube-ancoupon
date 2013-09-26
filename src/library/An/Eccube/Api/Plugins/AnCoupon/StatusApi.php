<?php

class An_Eccube_Api_Plugins_AnCoupon_StatusApi extends An_Eccube_Api {
    protected function initialize() {
        $this->authenticationRequired = true;
    }
    
    protected function get(An_Eccube_ApiRequest $request, An_Eccube_ApiResponse $response) {
        $info = $this->plugin->getPluginInfo();
        $response->setBody($info);
    }
}
