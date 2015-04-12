<?php namespace lang\mirrors;

use lang\Type;
use lang\XPClass;
use lang\IllegalArgumentException;
use lang\IllegalStateException;

/**
 * A method or constructor parameter
 *
 * @test  xp://lang.mirrors.unittest.ParameterTest
 */
class Parameter extends \lang\Object {
  private $mirror, $reflect;

  /**
   * Creates a new parameter
   *
   * @param  lang.mirrors.Method $mirror
   * @param  var $arg Either a ReflectionParameter or an offset
   * @throws lang.IllegalArgumentException If there is no such parameter
   */
  public function __construct($mirror, $arg) {
    if ($arg instanceof \ReflectionParameter) {
      $this->reflect= $arg;
    } else {
      try {
        $this->reflect= new \ReflectionParameter([$mirror->reflect->class, $mirror->reflect->name], $arg);
      } catch (\Exception $e) {
        throw new IllegalArgumentException('No parameter '.$arg.' in '.$mirror->name());
      }
    }
    $this->mirror= $mirror;
  }

  /** @return string */
  public function name() { return $this->reflect->name; }

  /** @return int */
  public function position() { return $this->reflect->getPosition(); }

  /** @return bool */
  public function isOptional() { return $this->reflect->isOptional(); }

  /** @return bool */
  public function isVariadic() { return $this->reflect->isVariadic(); }

  /** @return lang.Type */
  public function type() {
    if ($this->reflect->isArray()) {
      return Type::$ARRAY;
    } else if ($this->reflect->isCallable()) {
      return Type::$CALLABLE;
    } else if (null === ($class= $this->reflect->getClass())) {
      $params= $this->mirror->tags()['param'];
      $n= $this->reflect->getPosition();
      if (isset($params[$n])) {
        return $params[$n]->resolve($this->mirror->declaredIn());
      } else {
        return Type::$VAR;
      }
    } else {
      return new XPClass($class);
    }
  }

  /**
   * Returns the default value for an optional parameter
   *
   * @return var
   * @throws lang.IllegalStateException
   */
  public function defaultValue() {
    if ($this->reflect->isOptional() && !$this->reflect->isVariadic()) {
      return $this->reflect->getDefaultValue();
    }
    throw new IllegalStateException('Parameter is not optional');
  }
}