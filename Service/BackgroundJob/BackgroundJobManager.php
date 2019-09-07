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

namespace Accurateweb\BackgroundJobBundle\Service\BackgroundJob;

use Accurateweb\BackgroundJobBundle\Event\BackgroundJobEvent;
use Accurateweb\BackgroundJobBundle\Model\BackgroundJob;
use Accurateweb\BackgroundJobBundle\Model\BackgroundJobFilter;
use Accurateweb\BackgroundJobBundle\Model\BackgroundJobRepositoryInterface;
use Accurateweb\BackgroundJobBundle\Model\MetaData;
use Doctrine\ORM\EntityManager;
use Sonata\CoreBundle\Exception\InvalidParameterException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BackgroundJobManager
{
  private $backgroundJobRepository;
  private $pool;
  private $eventDispatcher;

  public function __construct (
    BackgroundJobRepositoryInterface $backgroundJobRepository,
    BackgroundJobPool $pool,
    EventDispatcherInterface $eventDispatcher,
    EntityManager $entityManager
  )
  {
    $this->backgroundJobRepository = $backgroundJobRepository;
    $this->pool = $pool;
    $this->eventDispatcher = $eventDispatcher;
    $this->entityManager = $entityManager;
  }

  /**
   * @param $clsid
   * @throws \Accurateweb\BackgroundJobBundle\Exception\BackgroundJobNotExistsException
   */
  public function addToQueueByClsid($clsid)
  {
    $jobService = $this->pool->getBackgroundJob($clsid);
    $this->addToQueue($jobService);
  }

  /**
   * @param BackgroundJobInterface $jobService
   * @param MetaData|null $metaData
   */
  public function addToQueue(BackgroundJobInterface $jobService, MetaData $metaData=null)
  {
    $className = $this->backgroundJobRepository->getClassName();
    /** @var BackgroundJob $job */
    $job = new $className();

    if (!$job instanceof BackgroundJob)
    {
      throw new InvalidParameterException(sprintf('BackgroundJobRepository entities should implement Accurateweb\BackgroundJobBundle\Model\BackgroundJob'));
    }

    if ($metaData instanceof MetaData)
    {
      $jobService->initialize($metaData);
    }

    $job->setClsid($jobService->getClsid());
    $job->setName($jobService->getName());
    $job->setState(BackgroundJob::STATE_QUEUED);
    $job->setCommand($jobService->getCommand());
    $job->setMetaData($metaData);
    $this->eventDispatcher->dispatch('queue.add', new BackgroundJobEvent($jobService, $job));
    $this->entityManager->persist($job);
    $this->entityManager->flush();
  }

  /**
   * @param BackgroundJobInterface $jobService
   */
  public function removeFromQueue(BackgroundJobInterface $jobService)
  {
    /** @var BackgroundJob $job */
    $job = $this->backgroundJobRepository->findOneBy(['state' => BackgroundJob::STATE_QUEUED, 'clsid' => $jobService->getClsid()]);

    if ($job)
    {
      $job->setState(BackgroundJob::STATE_INTERRUPTED);
      $this->entityManager->persist($job);
      $this->entityManager->flush();
    }
  }

  /**
   * @param BackgroundJobInterface $job
   * @return bool
   */
  public function isQueued(BackgroundJobInterface $job)
  {
    return $this->isQueuedByClsid($job->getClsid());
  }

  /**
   * @param string $clsid
   * @return bool
   */
  public function isQueuedByClsid($clsid)
  {
    $filter = new BackgroundJobFilter();
    $filter->setState([BackgroundJob::STATE_QUEUED]);
    $filter->setClsid($clsid);

    return count($this->backgroundJobRepository->findByBackgroundJobFilter($filter)) > 0;
  }

  /**
   * @return BackgroundJob[]
   */
  public function getQueuedJobs()
  {
    $filter = new BackgroundJobFilter();
    $filter->setState(BackgroundJob::STATE_QUEUED);

    return $this->backgroundJobRepository->findByBackgroundJobFilter($filter);
  }

  /**
   * @param BackgroundJobFilter $filter
   * @return BackgroundJob[]
   */
  public function getJobsByFilter(BackgroundJobFilter $filter)
  {
    return $this->backgroundJobRepository->findByBackgroundJobFilter($filter);
  }
}