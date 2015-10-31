<?php
/**
 * Ice core response class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */
namespace Ice\Core;

use Ice\Helper\Http;
use Ice\Helper\Json;

/**
 * Class Response
 *
 * Core response class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class Response
{
    /**
     * Content type
     *
     * @var string
     */
    private $contentType = null;

    /**
     * Http status code
     *
     * @var string
     */
    private $statusCode = null;

    private $content = null;

    private $error = null;

    private $success = null;

    /**
     * Private constructor of Request object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    private function __construct()
    {
    }

    /**
     * Create instance of Response
     *
     * @return Response
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public static function create()
    {
        return new Response();
    }

    /**
     * @param null $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param null $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @param null $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * Send data to standard output stream
     *
     * @param array $result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function send(array $result)
    {
        $redirectUrl = isset($result['redirectUrl']) ? $result['redirectUrl'] : null;

        if ($redirectUrl) {
            if (headers_sent()) {
                echo '<script type="text/javascript">location.href="' . $redirectUrl . '"</script>';
                return;
            }

            Http::setHeader('Location: ' . $redirectUrl, false, $this->statusCode);
            return;
        }

        if ($this->content === null) {
            $this->content = Request::isAjax()
                ? str_replace(dirname(MODULE_DIR), '', Json::encode($result))
                : str_replace(dirname(MODULE_DIR), '', $result['content']);
        }

        Http::setHeader(Http::getContentLength(strlen($this->content)));

        if ($this->contentType) {
            Http::setHeader(Http::getContentTypeHeader($this->contentType));
        }

        if ($this->statusCode) {
            Http::setHeader(Http::getStatusCodeHeader($this->statusCode), true, $this->statusCode);
        }

        echo $this->content;
    }

    /**
     * Set content type for response
     *
     * @param string $contentType
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Set redirect url
     *
     * @param null $redirectUrl
     * @param int $statusCode
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function setRedirectUrl($redirectUrl, $statusCode = 301)
    {
        $this->redirectUrl = $redirectUrl;
        $this->setStatusCode($statusCode);
    }

    /**
     * Set status code for response
     *
     * @param string $statusCode
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }
}
