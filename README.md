# TaskSchedulerBundle


Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require accurateweb/task-scheduler-bundle
```
Step 2: Enable the Bundle
-------------------------
config.yml
```
accurateweb_task_scheduler:
    configuration:
        uuid: my-uniq-background-job-uuid
        repository_service: app.repository.backgorund_job
```
services.yml
```
app.repository.backgorund_job:
    class: AppBundle\Repository\Common\BackgroundJobRepository
    factory: ['@doctrine.orm.entity_manager', 'getRepository']
    arguments: ['AppBundle\Entity\Common\BackgroundJob']
```
BackgroundJobRepository.php
```
use Accurateweb\TaskSchedulerBundle\Model\BackgroundJobFilter;
use Accurateweb\TaskSchedulerBundle\Model\BackgroundJobRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class BackgroundJobRepository extends EntityRepository implements BackgroundJobRepositoryInterface
{
  public function findByBackgroundJobFilter (BackgroundJobFilter $filter)
  {
    $qb = $this->createQueryBuilder('b');
    
    if ($filter->getClsid())
    {
      $qb->where('b.clsid = :clsid')
        ->setParameter('clsid', $filter->getClsid());
    }
    
    if ($filter->getState())
    {
      $qb
        ->andWhere('b.state IN (:states)')
        ->setParameter('states', $filter->getState());
    }
    
    return $qb->getQuery()->getResult();
  }
}
```
Usage sample
-------------------------
Service class:
```
class CacheClearBackgroundJob implements BackgroundJobInterface
{
  const CLSID = '9829ea19-a2e8-4c09-8aaa-adadb064af31';
  
  private $env;
  
  public function initialize(MetaData $data)
  {
    $this->resolveCmdOptions($data->getCmdOptions());
  }
  
  public function getCommand()
  {
    return sprintf('cache:clear --env=%s', $this->env);
  }
  
  public function getName()
  {
    return 'Cache clear';
  }
  
  public function getClsid()
  {
    return static::CLSID;
  }
  
  private function resolveCmdOptions($options)
  {
    $cmdOptions = new OptionsResolver();
    $cmdOptions->setRequired(['env']);
    
    $cmdOptions = $cmdOptions->resolve($options);
    $this->env = $cmdOptions['env'];
  }
}
```
In services.yml:
```
app.cache_clear.bg_job:
    class: AppBundle\Model\BackgroundJob\CacheClearBackgroundJob
    tags:
        - { name: aw.bg_job }
```
Service usage:
```
    $job = $this->container->get('app.cache_clear.bg_job');
    $metaData = new MetaData(null, ['env' => 'test']);
    
    $this->container->get('aw.bg_job.background_job_manager')->addToQueue($job, $metaData);
```