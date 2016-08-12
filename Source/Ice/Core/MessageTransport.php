<?php

namespace Ice\Core;

abstract class MessageTransport extends Container
{
    use Stored;

    private static $defaultKey = 'default';

    private $fromAddress = null;
    private $fromName = null;
    private $replyToAddress = null;
    private $replyToName = null;

    protected function __construct(array $data)
    {
        parent::__construct($data);

        $config = Config::getInstance(self::getClass());

        $key = $this->getInstanceKey();

        $this->fromAddress = $config->get($key . '/fromAddress');
        $this->fromName = $config->get($key . '/fromName', false);

        $this->replyToAddress = $config->get($key . '/replyToAddress');
        $this->replyToName = $config->get($key . '/replyToName', false);
    }

    /**
     * @param string $instanceKey
     * @param int $ttl
     * @param array $params
     * @return MessageTransport
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.0
     */
    public static function getInstance($instanceKey = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($instanceKey, $ttl, $params);
    }

    protected static function getDefaultKey()
    {
        return MessageTransport::$defaultKey;
    }

    abstract public function send(Message $message);

    /**
     * @return string
     */
    public function getFromAddress()
    {
        return $this->fromAddress;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @return string
     */
    public function getReplyToAddress()
    {
        return $this->replyToAddress;
    }

    /**
     * @return string
     */
    public function getReplyToName()
    {
        return $this->replyToName;
    }
}