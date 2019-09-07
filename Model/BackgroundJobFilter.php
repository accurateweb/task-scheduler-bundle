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

namespace Accurateweb\BackgroundJobBundle\Model;

class BackgroundJobFilter
{
  /**
   * @var string
   */
  private $clsid;

  /**
   * @var array
   */
  private $state;

  /**
   * @return string
   */
  public function getClsid ()
  {
    return $this->clsid;
  }

  /**
   * @param string $clsid
   * @return $this
   */
  public function setClsid ($clsid)
  {
    $this->clsid = $clsid;
    return $this;
  }

  /**
   * @return array
   */
  public function getState ()
  {
    return $this->state;
  }

  /**
   * @param string|array $state
   * @return $this
   */
  public function setState ($state)
  {
    if (is_string($state))
    {
      $state = [$state];
    }

    $this->state = $state;

    return $this;
  }
}