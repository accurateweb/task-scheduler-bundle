<?php


namespace Accurateweb\TaskSchedulerBundle\Service\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Filesystem\Filesystem;

class BackgroundJobDispatcherLoggerFactory
{
  /**
   * @param $logFile
   * @return LoggerInterface
   */
  public function createLogger ($logFile)
  {
    $fs = new Filesystem();
    $dirname = dirname($logFile);

    if (!$fs->exists($dirname))
    {
      $fs->mkdir($dirname);
    }

    if (!$fs->exists($logFile))
    {
      $fs->touch($logFile);
    }

    $logger = new ConsoleLogger(
      new StreamOutput(fopen($logFile, 'a'), [
        LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::INFO => OutputInterface::VERBOSITY_NORMAL,
      ])
    );

    return $logger;
  }
}