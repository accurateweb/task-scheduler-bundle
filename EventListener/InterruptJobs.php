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

namespace Accurateweb\TaskSchedulerBundle\EventListener;

use Accurateweb\TaskSchedulerBundle\Event\BackgroundJobDispatcherEvent;
use Accurateweb\TaskSchedulerBundle\Model\BackgroundJob;
use Accurateweb\TaskSchedulerBundle\Model\BackgroundJobFilter;
use Accurateweb\TaskSchedulerBundle\Service\BackgroundJob\BackgroundJobManager;
use Doctrine\ORM\EntityManager;

class InterruptJobs
{
  private $entityManager;
  private $backgroundJobManager;

  public function __construct (EntityManager $entityManager, BackgroundJobManager $backgroundJobManager)
  {
    $this->entityManager = $entityManager;
    $this->backgroundJobManager = $backgroundJobManager;
  }

  public function onStart(BackgroundJobDispatcherEvent $event)
  {
    $filter = new BackgroundJobFilter();
    $filter->setState(BackgroundJob::STATE_RUNNING);
    $jobs = $this->backgroundJobManager->getJobsByFilter($filter);

    foreach ($jobs as $job)
    {
      $job->setState(BackgroundJob::STATE_INTERRUPTED);
      $this->entityManager->persist($job);
    }

    $this->entityManager->flush();
  }
}