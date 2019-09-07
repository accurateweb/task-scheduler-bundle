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

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class BackgroundJob
{
  const STATE_QUEUED = 'queued';
  const STATE_RUNNING = 'running';
  const STATE_FINISHED = 'finished';
  const STATE_INTERRUPTED = 'interrupted';
  /**
   * @var int
   * @ORM\Column(type="bigint")
   * @ORM\Id()
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @var string
   * @ORM\Column(type="string", nullable=false)
   */
  protected $clsid;

  /**
   * @var string
   * @ORM\Column(type="string")
   */
  protected $name;

  /**
   * @var string
   * @ORM\Column(type="string", nullable=true)
   */
  protected $command;

  /**
   * @var \DateTime
   * @ORM\Column(type="datetime")
   */
  protected $createdAt;

  /**
   * @var \DateTime
   * @ORM\Column(type="datetime", nullable=true)
   */
  protected $startedAt;

  /**
   * @var \DateTime
   * @ORM\Column(type="datetime", nullable=true)
   */
  protected $finishedAt;

  /**
   * @var string
   * @ORM\Column(type="string", length=50, nullable=true)
   */
  protected $state;

  /**
   * @var integer
   * @ORM\Column(type="integer", nullable=true)
   */
  protected $statusCode;

  /**
   * @var MetaData
   * @ORM\Column(type="object", nullable=true)
   */
  protected $metaData;

  /**
   * @var integer
   * @ORM\Column(type="integer")
   */
  protected $progress;

  /**
   * @return int
   */
  public function getId ()
  {
    return $this->id;
  }

  /**
   * @param int $id
   * @return $this
   */
  public function setId ($id)
  {
    $this->id = $id;
    return $this;
  }

  /**
   * @return string
   */
  public function getName ()
  {
    return $this->name;
  }

  /**
   * @param string $name
   * @return $this
   */
  public function setName ($name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getCreatedAt ()
  {
    return $this->createdAt;
  }

  /**
   * @param \DateTime $createdAt
   * @return $this
   */
  public function setCreatedAt ($createdAt)
  {
    $this->createdAt = $createdAt;
    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getStartedAt ()
  {
    return $this->startedAt;
  }

  /**
   * @param \DateTime $startedAt
   * @return $this
   */
  public function setStartedAt ($startedAt)
  {
    $this->startedAt = $startedAt;
    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getFinishedAt ()
  {
    return $this->finishedAt;
  }

  /**
   * @param \DateTime $finishedAt
   * @return $this
   */
  public function setFinishedAt ($finishedAt)
  {
    $this->finishedAt = $finishedAt;
    return $this;
  }

  /**
   * @return string
   */
  public function getCommand ()
  {
    return $this->command;
  }

  /**
   * @param string $command
   * @return $this
   */
  public function setCommand ($command)
  {
    $this->command = $command;
    return $this;
  }

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
   * @return string
   */
  public function getState ()
  {
    return $this->state;
  }

  /**
   * @param string $state
   * @return $this
   */
  public function setState ($state)
  {
    if (!in_array($state, static::getAvailableStates()))
    {
      throw new \InvalidArgumentException(sprintf('Available states: [%s]', implode(', ', static::getAvailableStates())));
    }

    $this->state = $state;
    return $this;
  }

  /**
   * @return int
   */
  public function getStatusCode ()
  {
    return $this->statusCode;
  }

  /**
   * @param int $statusCode
   * @return $this
   */
  public function setStatusCode ($statusCode)
  {
    $this->statusCode = $statusCode;
    return $this;
  }

  /**
   * @return MetaData
   */
  public function getMetaData ()
  {
    if (!$this->metaData)
    {
      $this->metaData = new MetaData();
    }

    return $this->metaData;
  }

  /**
   * @param MetaData $metaData
   * @return $this
   */
  public function setMetaData ($metaData)
  {
    $this->metaData = $metaData;
    return $this;
  }

  public static function getAvailableStates()
  {
    return [
      self::STATE_FINISHED,
      self::STATE_QUEUED,
      self::STATE_RUNNING,
      self::STATE_INTERRUPTED,
    ];
  }

  public function __toString ()
  {
    return $this->getName()?$this->getName():'Новая задача';
  }
}