<?php

namespace SR\Cardcom\Gateway\Logger\Handler;

use Magento\Framework\Logger\Handler\Debug as LoggerHandlerDebug;

class Debug extends LoggerHandlerDebug
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/cardcom.log';

    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
        return $record['level'] >= $this->level;
    }
}
