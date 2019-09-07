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

namespace Accurateweb\TaskSchedulerBundle\Service\BackgroundJob;


use Accurateweb\TaskSchedulerBundle\Model\MetaData;

class SimpleBackgroundJob implements BackgroundJobInterface
{
  private $command;
  private $name;
  private $clsid;
  /**
   * @var MetaData
   */
  private $metaData;

  public function __construct ($command, $name, $clsid)
  {
    $this->command = $command;
    $this->name = $name;
    $this->clsid = $clsid;
    $this->metaData = new MetaData();
  }

  public function initialize (MetaData $data)
  {
    $this->metaData = $data;
  }

  public function getCommand ()
  {
    $arguments = $this->metaData->getCmdArguments();
    $options = $this->metaData->getCmdOptions();
    $optionString = '';

    foreach ($options as $option => $value)
    {
      $optionString .= sprintf('--%s=%s ', $option, $value);
    }

    return sprintf('%s %s %s', $this->command, implode(' ', $arguments), $optionString);
  }

  public function getName ()
  {
    return $this->name;
  }

  public function getClsid ()
  {
    return $this->clsid;
  }

}