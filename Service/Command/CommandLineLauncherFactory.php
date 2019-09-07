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

use Accurateweb\BackgroundJobBundle\Model\BackgroundJob;
use Accurateweb\BackgroundJobBundle\Service\Logger\BackgroundJobLoggerFactory;

class CommandLineLauncherFactory
{
  private $commandLineResolver;
  private $loggerFactory;
  private $env;

  public function __construct (PhpCommandLineResolver $commandLineResolver, BackgroundJobLoggerFactory $loggerFactory, $env)
  {
    $this->commandLineResolver = $commandLineResolver;
    $this->loggerFactory = $loggerFactory;
    $this->env = $env;
  }

  public function getLauncher(BackgroundJob $job)
  {
    $logger = $this->loggerFactory->getBackgroundJobLogger($job);
    return new CommandLineLauncher($this->commandLineResolver, $logger, $this->env);
  }
}