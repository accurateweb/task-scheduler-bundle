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

namespace Accurateweb\TaskSchedulerBundle\Model;

class MetaData implements \Serializable
{
  /**
   * @var array
   */
  protected $cmdArguments;

  /**
   * @var array
   */
  protected $cmdOptions;

  /**
   * @var array
   */
  protected $options;

  /**
   * MetaData constructor.
   * @param null|array $cmdArguments
   * @param null|array $cmdOptions
   * @param null|array $options
   */
  public function __construct ($cmdArguments=null, $cmdOptions=null, $options=null)
  {
    $this->cmdArguments = is_array($cmdArguments)?$cmdArguments:[];
    $this->cmdOptions = is_array($cmdOptions)?$cmdOptions:[];
    $this->options = is_array($options)?$options:[];
  }

  /**
   * @return array
   */
  public function getCmdArguments ()
  {
    return $this->cmdArguments;
  }

  /**
   * @param array $cmdArguments
   * @return $this
   */
  public function setCmdArguments ($cmdArguments)
  {
    $this->cmdArguments = $cmdArguments;
    return $this;
  }

  /**
   * @return array
   */
  public function getCmdOptions ()
  {
    return $this->cmdOptions;
  }

  /**
   * @param array $cmdOptions
   * @return $this
   */
  public function setCmdOptions ($cmdOptions)
  {
    $this->cmdOptions = $cmdOptions;
    return $this;
  }

  /**
   * @return array
   */
  public function getOptions ()
  {
    return $this->options;
  }

  /**
   * @param array $options
   * @return $this
   */
  public function setOptions ($options)
  {
    $this->options = $options;
    return $this;
  }

  public function serialize()
  {
    return json_encode([
      'cmdArguments' => $this->cmdArguments,
      'cmdOptions' => $this->cmdOptions,
      'options' => $this->options
    ]);
  }

  public function unserialize($serialized)
  {
    $data = json_decode($serialized, true);

    $this->cmdArguments = (isset($data['cmdArguments']) && is_array($data['cmdArguments'])) ?
      $data['cmdArguments'] : [];
    $this->cmdOptions = (isset($data['cmdOptions']) && is_array($data['cmdOptions'])) ?
      $data['cmdOptions'] : [];;
    $this->options = (isset($data['options']) && is_array($data['options'])) ?
      $data['options'] : [];;
  }
}