<?php
/**
 *  (c) 2019 ИП Рагозин Денис Николаевич. Все права защищены.
 *
 *  Настоящий файл является частью программного продукта, разработанного ИП Рагозиным Денисом Николаевичем
 *  (ОГРНИП 315668300000095, ИНН 660902635476).
 *
 *  Алгоритм и исходные коды программного кода программного продукта являются коммерческой тайной
 *  ИП Рагозина Денис Николаевича. Любое их использование без согласия ИП Рагозина Денис Николаевича рассматривается,
 *  как нарушение его авторских прав.
 *   Ответственность за нарушение авторских прав наступает в соответствии с действующим законодательством РФ.
 */

namespace Accurateweb\BackgroundJobBundle\Service\Command;

use Cocur\BackgroundProcess\BackgroundProcess;
use Psr\Log\LoggerInterface;

class BackgroundCommandLineLauncher implements CommandLineLauncherInterface
{
  private $commandLineResolver;
  private $logger;
  private $processOutput;

  public function __construct (PhpCommandLineResolver $commandLineResolver, LoggerInterface $logger, $processOutput = '/dev/null')
  {
    $this->commandLineResolver = $commandLineResolver;
    $this->logger = $logger;
    $this->processOutput = $processOutput;
  }

  /**
   * @param $command
   * @return int PID
   */
  public function startCommand ($command)
  {
    $phpConsoleLine = $this->commandLineResolver->getCommandLauncher();
    $startCommandLine = sprintf('%s %s', $phpConsoleLine, $command);
    $this->logger->info(sprintf('Starting command "%s"...', $startCommandLine));

    $process = new BackgroundProcess($startCommandLine);
    $process->run($this->processOutput);
    $pid = $process->getPid();
    $this->logger->info(sprintf('Daemon PID %s', $pid));
    $this->logger->info(sprintf('Daemon log %s', $this->processOutput));

    return $pid;
  }
}