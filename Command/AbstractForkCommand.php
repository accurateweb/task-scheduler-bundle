<?php


namespace Accurateweb\BackgroundJobBundle\Command;

use Cocur\BackgroundProcess\BackgroundProcess;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractForkCommand extends ContainerAwareCommand
{
  protected $pidFile;
  /**
   * @var SymfonyStyle
   */
  protected $logger;

  protected function configure ()
  {
    $this->addArgument('action', InputArgument::OPTIONAL, 'start|stop|restart|status', 'start');
  }

  /**
   * @return string
   */
  protected function getPidFile ()
  {
    $uid = $this->getContainer()->get('aw.bg_job.background_job_dispatcher')->getUid();
    return sprintf('%s/background_job/%s.pid', $this->getContainer()->getParameter('kernel.logs_dir'), $uid);
  }

  /**
   * @return string
   * @throws \Exception
   */
  protected function getCommandOutputFile ()
  {
    return sprintf('%s/daemon.log', $this->getLogDir());
  }

  /**
   * @return string
   * @throws \Exception
   */
  protected function getLogDir ()
  {
    $logDir = realpath(sprintf('%s/../var/logs', $this->getContainer()->getParameter('kernel.root_dir')));

    if (!$logDir)
    {
      $logDir = sprintf('%s/../var/logs', $this->getContainer()->getParameter('kernel.root_dir'));
    }

    $logDir = sprintf('%s/background_job', $logDir);

    if (!is_dir($logDir))
    {
      if (!@mkdir($logDir, 0777, true))
      {
        throw new \Exception(sprintf('Failed to create %s', $logDir));
      }
    }

    return $logDir;
  }

  protected function execute (InputInterface $input, OutputInterface $output)
  {
    $this->logger = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);
    $this->pidFile = $this->getPidFile();
    $action = $input->getArgument('action');

    switch ($action)
    {
      case 'stop':
        $this->stop();
        break;
      case 'start':
        $this->startProcess($input);
        break;
      case 'restart':
        if ($this->isRunning())
        {
          $this->stop();
        }
        $this->startProcess($input);
        break;
      case 'status':
        $this->printStatus();
        break;
    }

    return 0;
  }

  abstract public function startLoop (InputInterface $input);

  abstract public function stopLoop ();

  protected function isRunning ()
  {
    if (!file_exists($this->pidFile))
    {
      return false;
    }

    $pid = file_get_contents($this->pidFile);
    $process = BackgroundProcess::createFromPID($pid);

    if ($process->isRunning())
    {
      return true;
    }

    unlink($this->pidFile);

    return false;
  }

  protected function printStatus ()
  {
    if ($this->isRunning())
    {
      $pid = file_get_contents($this->pidFile);
      $this->logger->block(sprintf('Process working. PID: %s', $pid), 'daemon', 'fg=green');
    }
  }

  protected function startProcess (InputInterface $input)
  {
    if ($this->isRunning())
    {
      throw new \RuntimeException(sprintf('A process is started'));
    }

    if (function_exists('pcntl_signal'))
    {
      pcntl_signal(SIGHUP, [$this, 'stopLoop']);
    }

    $this->preFork();
    $pid = pcntl_fork();

    if ($pid < 0)
    {
      throw new \RuntimeException('Unable to start the server process.');
    }

    if ($pid > 0)
    {
      return;
    }

    if (posix_setsid() < 0)
    {
      throw new \RuntimeException('Unable to set the child process as session leader.');
    }

    $this->postFork();
    $pid = (string)getmypid();
    file_put_contents($this->pidFile, $pid);
    $this->logger->block(sprintf('Starting process PID: %s', $pid), 'daemon', 'fg=green');
    $this->startLoop($input);
  }

  protected function stop ()
  {
    if (!file_exists($this->pidFile))
    {
      throw new \RuntimeException('No server is listening.');
    }

    $this->logger->block('Stop process', 'daemon', 'fg=green');
    $pid = file_get_contents($this->pidFile);
    $process = BackgroundProcess::createFromPID($pid);
    posix_kill($pid, SIGHUP);

    for ($i = 60; $i > 0; $i--)
    {
      if ($process->isRunning())
      {
        if ($i <= 0)
        {
          $process->stop();
          throw new \Error(sprintf('Process %s still running after 60 seconds', $pid));
          break;
        }
        else
        {
          sleep(1);
        }
      }
    }

    unlink($this->pidFile);
  }

  protected function preFork ()
  {
    $this->getContainer()->get('doctrine')->getConnection()->close();
  }

  protected function postFork ()
  {
    $this->getContainer()->get('doctrine')->getConnection()->connect();
  }
}