<?php

class An_Eccube_ApiResponse
{
    const CODE_INFORMATION = 100;
    const CODE_SUCCESS = 200;
    const CODE_REDIRECTION = 300;
    const CODE_CLIENT_ERORR = 400;
    const CODE_SERVER_ERROR = 500;

    /**
     * @var array
     */
    public $headers = array();

    public $body = array();

    /**
     * @var int
     */
    public $status_code;

    /**
     * @var string
     */
    public $status_message;

    public function __construct()
    {
        $this->headers['Content-Type'] = 'application/json';
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    public function removeHeader($key)
    {
        unset($this->headers[$key]);
    }


    public function setBody($body, $status_code = 200, $status_message = 'OK')
    {
        $this->body = $body;

        $this->status_code = $status_code;
        $this->status_message = $status_message;
    }

    public function setError($error_message, $status_code = 500, $status_message = 'Internal Server Error')
    {
        $this->body = array(
            'error' => array(
                'message' => $error_message,
                'code' => $status_code,
            ),
        );

        $this->status_code = $status_code;
        $this->status_message = $status_message;
    }

    public function send()
    {
        $content = $this->buildContent();
        $this->addHeader('Content-Length', strlen($content));

        header('HTTP/1.1 ' . $this->status_code . ' ' . $this->status_message, $this->status_code);

        foreach ($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }

        echo $content;
    }

    protected function buildContent()
    {
        $content = $this->body;
        return An_Eccube_Utils::encodeJson($content);
    }
}
