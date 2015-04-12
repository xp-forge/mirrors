<?php namespace lang\mirrors;

use lang\IllegalArgumentException;
use lang\Throwable;

/**
 * Represents a constructor. If no constructor is present, a default 
 * no-arg constructor is used.
 *
 * @test   xp://lang.mirrors.unittest.ConstructorTest
 */
class Constructor extends Routine {
  private static $DEFAULT;

  static function __static() {
    self::$DEFAULT= new \ReflectionMethod(self::class, '__default');
  }

  /** @return lang.Generic */
  public function __default() {
    return $this->reflect->newInstance();
  }

  /**
   * Creates a new constructor
   *
   * @param  lang.mirrors.TypeMirror $mirror
   */
  public function __construct($mirror) {
    parent::__construct($mirror, $mirror->reflect->getConstructor() ?: self::$DEFAULT);
  }

  /** @return bool */
  public function present() { return self::$DEFAULT !== $this->reflect; }

  /**
   * Creates a new instance using this constructor
   *
   * @param  var* $args
   * @return lang.Generic
   */
  public function newInstance(... $args) {
    if (!$this->mirror->reflect->isInstantiable()) {
      throw new IllegalArgumentException('Verifying '.$this->mirror->name().': Cannot instantiate');
    }

    try {
      return $this->mirror->reflect->newInstanceArgs($args);
    } catch (Throwable $e) {
      throw new TargetInvocationException('Creating a new instance of '.$this->mirror->name().' raised '.$e->getClassName(), $e);
    } catch (\Exception $e) {
      throw new IllegalArgumentException('Instantiating '.$this->mirror->name().': '.$e->getMessage());
    }
  }

  /** @return string */
  public function __toString() {
    $params= '';
    foreach ($this->parameters() as $param) {
      $params.= ', '.$param;
    }
    return $this->modifiers()->names().' __construct('.substr($params, 2).')';
  }
}