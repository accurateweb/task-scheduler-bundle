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

namespace Accurateweb\TaskSchedulerBundle\Service\Logger;

use Accurateweb\TaskSchedulerBundle\Model\BackgroundJob;
use Psr\Log\LoggerInterface;

class BackgroundJobLogger implements LoggerInterface
{
  private $job;
  private $logFile;

  public function __construct (BackgroundJob $job, $logFile)
  {
    $this->job = $job;
    $this->logFile = $logFile;
  }

  /**
   * @param string $message
   * @param array $context
   * @throws \Exception
   */
  public function emergency ($message, array $context = array())
  {
    $this->log('emergency', $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws \Exception
   */
  public function alert ($message, array $context = array())
  {
    $this->log('alert', $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws \Exception
   */
  public function critical ($message, array $context = array())
  {
    $this->log('critical', $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws \Exception
   */
  public function error ($message, array $context = array())
  {
    $this->log('error', $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws \Exception
   */
  public function warning ($message, array $context = array())
  {
    $this->log('warning', $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws \Exception
   */
  public function notice ($message, array $context = array())
  {
    $this->log('notice', $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws \Exception
   */
  public function info ($message, array $context = array())
  {
    $this->log('info', $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws \Exception
   */
  public function debug ($message, array $context = array())
  {
    $this->log('debug', $message, $context);
  }

  /**
   * @param mixed $level
   * @param string $message
   * @param array $context
   * @throws \Exception
   */
  public function log ($level, $message, array $context = array())
  {
    $message = sprintf("[%s] %s: %s%s", date('Y-m-d H:i:s'), $level, $message, PHP_EOL);
    $this->appendToFile($this->logFile, $message);
  }

  /**
   * @param $filename
   * @param $content
   * @throws \Exception
   */
  private function appendToFile($filename, $content)
  {
    $dir = dirname($filename);

    if (!is_dir($dir))
    {
      $this->mkdir($dir);
    }

    if (!is_writable($dir))
    {
      throw new \Exception(sprintf('Unable to write to the "%s" directory.', $dir), 0, null);
    }

    if (false === @file_put_contents($filename, $content, FILE_APPEND))
    {
      throw new \Exception(sprintf('Failed to write file "%s".', $filename), 0, null);
    }
  }

  /**
   * @param $dir
   * @param int $mode
   * @throws \Exception
   */
  private function mkdir ($dir, $mode = 0777)
  {

    if (true !== @mkdir($dir, $mode, true))
    {
      $error = error_get_last();
      
      if (!is_dir($dir))
      {
        if ($error)
        {
          throw new \Exception(sprintf('Failed to create "%s": %s.', $dir, $error['message']), 0, null);
        }
        throw new \Exception(sprintf('Failed to create "%s"', $dir), 0, null);
      }
    }
  }
}