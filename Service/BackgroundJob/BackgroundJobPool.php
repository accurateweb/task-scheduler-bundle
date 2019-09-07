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

namespace Accurateweb\BackgroundJobBundle\Service\BackgroundJob;

use Accurateweb\BackgroundJobBundle\Exception\BackgroundJobNotExistsException;

class BackgroundJobPool
{
  private $jobs;

  public function __construct ()
  {
    $this->jobs = [];
  }

  /**
   * @param BackgroundJobInterface $backgroundJob
   */
  public function addBackgroundJob(BackgroundJobInterface $backgroundJob)
  {
    $this->jobs[$backgroundJob->getClsid()] = $backgroundJob;
  }

  /**
   * @param $clsid
   * @return BackgroundJobInterface
   * @throws BackgroundJobNotExistsException
   */
  public function getBackgroundJob($clsid)
  {
    if (!isset($this->jobs[$clsid]))
    {
      throw new BackgroundJobNotExistsException(sprintf('Background job %s not exists', $clsid));
    }

    return $this->jobs[$clsid];
  }

  /**
   * @param $clsid
   * @return bool
   */
  public function hasBackgroundJob($clsid)
  {
    return isset($this->jobs[$clsid]);
  }

  /**
   * @return array|BackgroundJobInterface[]
   */
  public function getBackgroundJobs()
  {
    return $this->jobs;
  }
}