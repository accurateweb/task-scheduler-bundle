<?php


namespace Accurateweb\TaskSchedulerBundle\Command;

use Symfony\Component\Console\Input\InputInterface;

class BackgroundJobForkCommand extends AbstractForkCommand
{
  protected function configure ()
  {
    parent::configure();
    $this->setName('background-job:daemon');
  }

  protected function getPidFile ()
  {
    return parent::getPidFile();
  }

  public function startLoop (InputInterface $input)
  {
    $this->logger->block('Start job dispatcher', 'daemon', 'fg=green');
    $this->getContainer()->get('aw.task_scheduler.background_job_dispatcher')->dispatch();
  }

  public function stopLoop ()
  {
    $this->logger->block('Stop loop', 'daemon', 'fg=green');
    $this->getContainer()->get('aw.task_scheduler.background_job_dispatcher')->stop();
  }
}