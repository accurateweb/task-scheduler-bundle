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

namespace Accurateweb\TaskSchedulerBundle\Admin;

use Accurateweb\TaskSchedulerBundle\Model\BackgroundJob;
use Accurateweb\TaskSchedulerBundle\Service\Logger\BackgroundJobLogFileResolver;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class BackgroundJobAdmin extends AbstractAdmin
{
  private $jobLogFileResolver;

  public function __construct ($code, $class, $baseControllerName, BackgroundJobLogFileResolver $jobLogFileResolver)
  {
    $this->jobLogFileResolver = $jobLogFileResolver;
    parent::__construct($code, $class, $baseControllerName);
  }

  protected $datagridValues = [
    '_page' => 1,
    '_sort_order' => 'DESC',
    '_sort_by' => 'createdAt',
  ];

  protected function configureShowFields (ShowMapper $show)
  {
    $show
      ->add('name')
      ->add('createdAt')
      ->add('command')
      ->add('state')
      ->add('startedAt')
      ->add('finishedAt')
      ->add('statusCode')
      ->add('output')
    ;
  }

  protected function configureListFields (ListMapper $list)
  {
    $list
      ->add('name')
      ->add('createdAt', null, [
        'format' => 'd.m.Y H:i',
      ])
      ->add('state')
      ->add('finishedAt', null, [
        'format' => 'd.m.Y H:i',
      ])
      ->add('_action', null, array(
          'actions' => array(
            'show' => null,
          )
        )
      )
    ;
  }

  protected function configureDatagridFilters (DatagridMapper $filter)
  {
    $allTasks = $this->getConfigurationPool()->getContainer()->get('aw.task_scheduler.background_job_pool')->getBackgroundJobs();
    $taskChoices = [];

    foreach ($allTasks as $allTask)
    {
      $taskChoices[$allTask->getName()] = $allTask->getClsid();
    }

    $filter
      ->add('name')
      ->add('clsid', 'doctrine_orm_string', [
        'label' => 'Задача',
      ], 'choice', [
          'choices' => $taskChoices,
        ]
      )
      ->add('state', 'doctrine_orm_string', [], 'choice', [
          'choices' => array_combine(BackgroundJob::getAvailableStates(), BackgroundJob::getAvailableStates()),
        ]
      )
    ;
  }

  protected function configureRoutes (RouteCollection $collection)
  {
    $collection->remove('create');
    $collection->remove('edit');
  }

  public function getTemplate ($name)
  {
    if ($name == 'show')
    {
      return 'AccuratewebBackgroundJobBundle::show.html.twig';
    }

    return parent::getTemplate($name);
  }

  public function getBackgroundJobOutput(BackgroundJob $job)
  {
    $logFile = $this->jobLogFileResolver->resolveFile($job);

    if (!is_readable($logFile))
    {
      return 'log file not exists';
    }

    return file_get_contents($logFile);
  }

  public function getSafeCommand(BackgroundJob $job)
  {
    $command = $job->getCommand();
    $rootDir = realpath(sprintf('%s/../',
      $this->getConfigurationPool()->getContainer()->getParameter('kernel.root_dir')
    ));

    if ($rootDir)
    {
      //Скроем вхождения пути к сайту из админки, если они присутствуют в команде
      $command = preg_replace(sprintf('#%s#', preg_quote($rootDir)), '*site root*', $command);
    }

    return $command;
  }
}