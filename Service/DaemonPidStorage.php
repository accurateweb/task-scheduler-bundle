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

namespace Accurateweb\BackgroundJobBundle\Service;

class DaemonPidStorage
{
  private $logsDir;
  private $file;

  public function __construct ($logsDir)
  {
    /*
     * Путь должен разресолвиться в shared директорию, а не на текущий релиз
     */
    $this->logsDir = realpath($logsDir);

    if (!$this->logsDir)
    {
      $this->logsDir = $logsDir;
    }

    $this->file = sprintf('%s/background_job/daemon.pid', $logsDir);
  }

  public function store($pid)
  {
    file_put_contents($this->file, $pid);
  }

  public function restore()
  {
    if (file_exists($this->file))
    {
      return file_get_contents($this->file);
    }

    return null;
  }
}