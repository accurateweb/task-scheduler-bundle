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

namespace Accurateweb\BackgroundJobBundle\Command;

use Accurateweb\BackgroundJobBundle\Service\Command\BackgroundCommandLineLauncher;
use Cocur\BackgroundProcess\BackgroundProcess;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class BackgroundJobDaemonCommand extends ContainerAwareCommand
{
  use LockableTrait;

  protected function configure ()
  {
    $this
      ->setName('background-job:old-daemon')
      ->addArgument('argument', InputArgument::OPTIONAL, 'start|stop|restart', 'start');
  }

  protected function execute (InputInterface $input, OutputInterface $output)
  {
    $argument = $input->getArgument('argument');

    if (!in_array($argument, ['stop', 'restart', 'start']))
    {
      throw new InvalidArgumentException(sprintf('Available commands: start, stop, restart'));
    }

    $isLocked = !$this->lock($this->getContainer()->get('aw.bg_job.background_job_dispatcher')->getUid());

    if ($isLocked && $argument == 'start')
    {
      $output->writeln('Another instance of background job dispatcher appears to be running already');
      return 1;
    }

    $this->release();
    $logger = new ConsoleLogger($output, [
      LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
      LogLevel::INFO   => OutputInterface::VERBOSITY_NORMAL,
    ]);

    /*
     * Ресолвим путь к shared dir
     */
    $logDir = realpath(sprintf('%s/../var/logs', $this->getContainer()->getParameter('kernel.root_dir')));

    if (!$logDir)
    {
      $logDir = sprintf('%s/../var/logs', $this->getContainer()->getParameter('kernel.root_dir'));
    }

    $logDir = sprintf('%s/background_job', $logDir);

    if (!is_dir($this->getContainer()->getParameter('kernel.root_dir')))
    {
      throw new \Error(sprintf('kernel.root_dir (%s) not exists???', $this->getContainer()->getParameter('kernel.root_dir')));
    }

    if (!is_dir($logDir))
    {
      $output->writeln('Log dir not exists. Creating...');

      if (!@mkdir($logDir, 0777, true))
      {
        $output->writeln(sprintf('Failed to create %s', $logDir));
        return 1;
      }
    }

    $launcher = new BackgroundCommandLineLauncher(
      $this->getContainer()->get('aw.bg_job.php_command_line'),
      $logger,
      sprintf('%s/daemon.log', $logDir)
    );

    $pidStorage = $this->getContainer()->get('aw.bg_job.daemon_pid_storage');

    if (in_array($argument, ['stop', 'restart']))
    {
      $output->writeln(sprintf('Stopping process...'));
      $pid = $pidStorage->restore();

      if (!$pid && in_array($argument, ['stop']))
      {
        $output->writeln(sprintf('File var/logs/background_job/daemon.pid not found or empty'));

        return 1;
      }
      elseif($pid)
      {
        $process = BackgroundProcess::createFromPID($pid);

        if (!$process->stop() && in_array($argument, ['stop']))
        {
          $output->writeln(sprintf('Process with pid %s not exists or failed to stop', $pid));

          return 1;
        }

        $output->writeln(sprintf('Process %s was stopped', $pid));
        $pidStorage->store(null);

        if (in_array($argument, ['stop']))
        {
          return 0;
        }
      }
      else
      {
        $output->writeln(sprintf('Process not found'));
      }
    }

    /*
     * Запускаем процесс и пишем его pid
     */
    $pid = $launcher->startCommand(sprintf('background-job:dispatch --env=%s', $this->getContainer()->getParameter('kernel.environment')));
    $pidStorage->store($pid);

    /*
     * Проверим не упал ли процесс
     */
    sleep(1);

    if (!BackgroundProcess::createFromPID($pid)->isRunning())
    {
      throw new \Exception(sprintf('Process with pid %s not found. More info %s', $pid, realpath(sprintf('%s/daemon.log', $logDir))));
    }

    $output->writeln('Startup complete');
    
    return 0;
  }
}
