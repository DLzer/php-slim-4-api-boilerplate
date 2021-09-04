<?php

namespace App\Factory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Factory.
 */
final class LoggerFactory
{

    /** @var string */
    private $path;

    /** @var int */
    private $level;

    /** @var array */
    private $handler = [];

    /** @var LoggerInterface */
    private $testLogger;

    /**
     * The constructor.
     *
     * @param array $settings The settings
     */
    public function __construct(array $settings)
    {
        $this->path = (string)$settings['path'];
        $this->level = (int)$settings['level'];

        // This can be used for testing to make the Factory testable
        if (isset($settings['test'])) {
            $this->testLogger = $settings['test'];
        }
    }

    /**
     * Build the logger.
     *
     * @param string|null $name The loggin channel
     *
     * @return LoggerInterface The logger
     */
    public function createLogger(string $name = null): LoggerInterface
    {

        if (isset($this->testLogger)) {
            return $this->testLogger;
        }

        $logger = new Logger($name ?: uniqid());

        foreach ($this->handler as $handler) {
            $logger->pushHandler($handler);
        }

        $this->handler = [];

        return $logger;
    }

    /**
     * Add a handler.
     *
     * @param HandlerInterface $handler The handler
     *
     * @return self The logger factory
     */
    public function addHandler(HandlerInterface $handler): self
    {
        $this->handler[] = $handler;

        return $this;
    }

    /**
     * Add rotating file logger handler.
     *
     * @param string $filename The filename
     * @param int $level The level (optional)
     *
     * @return LoggerFactory The logger factory
     */
    public function addFileHandler(string $filename, int $level = null): self
    {
        $filename = sprintf('%s/%s', $this->path, $filename);

        $rotatingFileHandler = new RotatingFileHandler($filename, 0,$level ?? $this->level,true,0777);

        // The last "true" here tells monolog to remove empty []'s
        $rotatingFileHandler->setFormatter(
            new LineFormatter(null, null, false, true)
        );

        $this->addHandler($rotatingFileHandler);

        return $this;
    }

    /**
     * Add a console logger.
     *
     * @param int $level The level (optional)
     *
     * @return self The instance
     */
    public function addConsoleHandler(int $level = null): self
    {
        $streamHandler = new StreamHandler('php://stdout', $level ?? $this->level);
        $streamHandler->setFormatter(new LineFormatter(null, null, false, true));

        $this->addHandler($streamHandler);

        return $this;
    }
}