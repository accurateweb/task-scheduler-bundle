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

namespace Accurateweb\TaskSchedulerBundle\Service\Command;

use Accurateweb\TaskSchedulerBundle\Service\Logger\BackgroundJobLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

class CommandLineLauncher implements CommandLineLauncherInterface
{
  private $commandLineResolver;
  /**
   * @var BackgroundJobLogger
   */
  private $logger;
  private $env;

  public function __construct (PhpCommandLineResolver $commandLineResolver, LoggerInterface $logger, $env)
  {
    $this->commandLineResolver = $commandLineResolver;
    $this->logger = $logger;
    $this->env = $env;
  }

  /**
   * @param $command
   * @return int status Code
   * @throws \Exception
   */
  public function startCommand($command)
  {
    $phpConsoleLine = $this->commandLineResolver->getCommandLauncher();
    $commandLine = sprintf('%s %s --env=%s', $phpConsoleLine, $command, $this->env);
    $process = new Process($commandLine, null, null, null, null);
    $statusCode = $process->run(function($type, $buffer){
      if (Process::ERR === $type)
      {
        $this->logger->error($buffer);
      }
      else
      {
        $this->logger->info($buffer);
      }
    });

    return $statusCode;
  }
}