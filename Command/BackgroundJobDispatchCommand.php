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

namespace Accurateweb\TaskSchedulerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class BackgroundJobDispatchCommand extends ContainerAwareCommand
{
  protected function configure ()
  {
    $this
      ->setName('background-job:dispatch')
      ->setDescription('...')
      ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description');
  }

  protected function execute (InputInterface $input, OutputInterface $output)
  {
    if (!$this->lock($this->getContainer()->get('aw.task_scheduler.background_job_dispatcher')->getUid()))
    {
      $output->writeln('Another instance of background job dispatcher appears to be running already');
      return 0;
    }

    $output->writeln('Start background job dispatcher...');
    $this->getContainer()->get('aw.task_scheduler.background_job_dispatcher')->dispatch();
  }

  /**
   * Locks a command.
   *
   * @param null $name
   * @return bool
   */
  private function lock ($name = null)
  {
    $logDir = realpath($this->getContainer()->getParameter('kernel.logs_dir'));
    $filename = sprintf('%s/%s', $logDir, $name);
    $fs = new Filesystem();

    if (!$fs->exists($filename))
    {
      $fs->touch($filename);
    }

    $fp = fopen($filename, 'c+');

    if (!is_resource($fp))
    {
      throw new LogicException('Unable to acquire lock');
    }

    if (!flock($fp, LOCK_EX | LOCK_NB))
    {
      return false;
    }

    return true;
  }
}
