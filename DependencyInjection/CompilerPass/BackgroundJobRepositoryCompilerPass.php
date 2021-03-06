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

namespace Accurateweb\TaskSchedulerBundle\DependencyInjection\CompilerPass;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class BackgroundJobRepositoryCompilerPass implements CompilerPassInterface
{
  public function process (ContainerBuilder $container)
  {
    $param = $container->getParameter('aw.task_scheduler.background_job_repository');

    try
    {
      $repository = $container->getDefinition($param);
    }
    catch (ServiceNotFoundException $e)
    {
      /**
       * @FIXME: Handle exception
       */
    }

    $manager = $container->getDefinition('aw.task_scheduler.background_job_manager');
    $manager->replaceArgument(0, $repository);
  }

}