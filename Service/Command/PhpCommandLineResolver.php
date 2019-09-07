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

namespace Accurateweb\TaskSchedulerBundle\Service\Command;

use Accurateweb\TaskSchedulerBundle\Exception\PhpBinNotFoundException;
use Symfony\Component\Process\PhpExecutableFinder;

class PhpCommandLineResolver
{
  private $kernelDir;
  private $phpLine;

  /**
   * PhpCommandLineResolver constructor.
   * @param string $kernelDir
   * @param string $phpLine /usr/bin/php
   */
  public function __construct ($kernelDir, $phpLine)
  {
    $this->kernelDir = $kernelDir;
    $this->phpLine = $phpLine;
  }

  /**
   * @return string
   */
  public function getCommandLauncher()
  {
    $php = $this->phpLine;
    /*
     * берем из параметров, если не находим, то Ищем текущий php
     */
    if (!$php)
    {
      $phpFinder = new PhpExecutableFinder();
      $php = $phpFinder->find(true);
    }

    if (!$php)
    {
      throw new PhpBinNotFoundException();
    }

    return sprintf('%s %s/../bin/console', $php, $this->kernelDir);
  }
}