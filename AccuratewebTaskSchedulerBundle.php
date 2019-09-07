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

namespace Accurateweb\TaskSchedulerBundle;

use Accurateweb\TaskSchedulerBundle\DependencyInjection\CompilerPass\BackgroundJobAdminCompilerPass;
use Accurateweb\TaskSchedulerBundle\DependencyInjection\CompilerPass\BackgroundJobPoolCompilerPass;
use Accurateweb\TaskSchedulerBundle\DependencyInjection\CompilerPass\BackgroundJobRepositoryCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AccuratewebTaskSchedulerBundle extends Bundle
{
  public function build (ContainerBuilder $container)
  {
    $container->addCompilerPass(new BackgroundJobRepositoryCompilerPass());
    $container->addCompilerPass(new BackgroundJobPoolCompilerPass());
    $container->addCompilerPass(new BackgroundJobAdminCompilerPass());
  }
}
