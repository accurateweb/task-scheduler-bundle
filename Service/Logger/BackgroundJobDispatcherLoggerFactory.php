<?php


namespace Accurateweb\TaskSchedulerBundle\Service\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

class BackgroundJobDispatcherLoggerFactory
{
  /**
   * @param $logFile
   * @return LoggerInterface
   */
  public function createLogger ($logFile)
  {
    $logger = new ConsoleLogger(
      new StreamOutput(fopen($logFile, 'a'), [
        LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::INFO => OutputInterface::VERBOSITY_NORMAL,
      ])
    );

    return $logger;
  }
}