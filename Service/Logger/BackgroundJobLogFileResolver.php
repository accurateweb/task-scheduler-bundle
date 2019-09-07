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
use Symfony\Component\Filesystem\Filesystem;

class BackgroundJobLogFileResolver
{
  private $logDir;

  public function __construct ($logDir)
  {
    /*
     * Путь должен разресолвиться в shared директорию, а не на текущий релиз
     */
    $this->logDir = realpath($logDir);

    if (!$this->logDir)
    {
      $this->logDir = $logDir;
    }

    $this->logDir = sprintf('%s/background_job/jobs', $this->logDir);
  }

  /**
   * @param BackgroundJob $job
   * @return string
   */
  public function resolveFile(BackgroundJob $job)
  {
    if (!$job->getId())
    {
      throw new \LogicException(sprintf('Job without id can not be resolved'));
    }

    $this->checkDir();

    $filename = $this->getFileName($job);
//    $fileSystem = new Filesystem();

//    if (!$fileSystem->exists($filename))
//    {
//      $fileSystem->touch($filename);
//    }

    return $filename;
  }

  private function checkDir()
  {
    $fileSystem = new Filesystem();

    if (!$fileSystem->exists($this->logDir))
    {
      $fileSystem->mkdir($this->logDir);
    }
  }

  private function getFileName(BackgroundJob $job)
  {
    $oldFileName = sprintf('%s/%s_%s.log', $this->logDir, $job->getId(), $job->getClsid());

    if (file_exists($oldFileName))
    {
      return $oldFileName;
    }

    $filename = sprintf('%s/%s/%s.log', $this->logDir, $job->getClsid(), $job->getId());
    $dir = dirname($filename);

    if (!is_dir($dir))
    {
      mkdir($dir, 0777, true);
    }

    return $filename;
  }
}