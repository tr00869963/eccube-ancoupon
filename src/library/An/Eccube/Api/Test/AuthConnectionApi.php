<?php

class An_Eccube_Api_Test_AuthConnectionApi extends An_Eccube_Api
{
    protected function initialize()
    {
        $this->authenticationRequired = true;
    }

    protected function execute(An_Eccube_ApiRequest $request, An_Eccube_ApiResponse $response)
    {
        $response->setBody('OK');
    }
}
