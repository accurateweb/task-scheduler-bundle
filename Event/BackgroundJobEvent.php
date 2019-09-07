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

namespace Accurateweb\BackgroundJobBundle\Event;


use Accurateweb\BackgroundJobBundle\Model\BackgroundJob;
use Accurateweb\BackgroundJobBundle\Service\BackgroundJob\BackgroundJobInterface;
use Symfony\Component\EventDispatcher\Event;

class BackgroundJobEvent extends Event
{
  private $backgroundJobService;
  private $backgroundJob;

  public function __construct (BackgroundJobInterface $backgroundJobService, BackgroundJob $backgroundJob)
  {
    $this->backgroundJobService = $backgroundJobService;
    $this->backgroundJob = $backgroundJob;
  }

  /**
   * @return BackgroundJobInterface
   */
  public function getBackgroundJobService ()
  {
    return $this->backgroundJobService;
  }

  /**
   * @param BackgroundJobInterface $backgroundJobService
   * @return $this
   */
  public function setBackgroundJobService ($backgroundJobService)
  {
    $this->backgroundJobService = $backgroundJobService;
    return $this;
  }

  /**
   * @return BackgroundJob
   */
  public function getBackgroundJob ()
  {
    return $this->backgroundJob;
  }

  /**
   * @param BackgroundJob $backgroundJob
   * @return $this
   */
  public function setBackgroundJob ($backgroundJob)
  {
    $this->backgroundJob = $backgroundJob;
    return $this;
  }
}