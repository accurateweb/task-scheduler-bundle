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

namespace Accurateweb\TaskSchedulerBundle\Service\Logger;

use Accurateweb\TaskSchedulerBundle\Model\BackgroundJob;
use Accurateweb\TaskSchedulerBundle\Model\BackgroundJobRepositoryInterface;

class BackgroundJobLoggerFactory
{
  private $jobLogFileResolver;

  /**
   * BackgroundJobLoggerFactory constructor.
   * @param BackgroundJobLogFileResolver $jobLogFileResolver
   */
  public function __construct (BackgroundJobLogFileResolver $jobLogFileResolver)
  {
    $this->jobLogFileResolver = $jobLogFileResolver;
  }

  /**
   * @param BackgroundJob $backgroundJob
   * @return \Psr\Log\LoggerInterface
   */
  public function getBackgroundJobLogger(BackgroundJob $backgroundJob)
  {
    return new BackgroundJobLogger($backgroundJob, $this->jobLogFileResolver->resolveFile($backgroundJob));
  }
}