<?php namespace lang\mirrors;

use lang\Type;
use lang\IllegalArgumentException;
use lang\IllegalStateException;
use lang\mirrors\parse\VariadicTypeRef;
use util\Objects;

/**
 * A method or constructor parameter
 *
 * @test  xp://lang.mirrors.unittest.ParameterTest
 */
class Parameter implements \lang\Value {
  private $mirror, $reflect;

  /**
   * Creates a new parameter
   *
   * @param  lang.mirrors.Routine $mirror
   * @param  php.ReflectionParameter|int|[:var]
   * @throws lang.IllegalArgumentException If there is no such parameter
   */
  public function __construct($mirror, $arg) {
    if (is_array($arg)) {
      $this->reflect= $arg;
    } else if ($arg instanceof \ReflectionParameter) {
      $params= $mirror->reflect['params']();
      $this->reflect= $params[$arg->getPosition()];
    } else {
      $params= $mirror->reflect['params']();
      if (!isset($params[$arg])) {
        throw new IllegalArgumentException('No parameter '.$arg.' in '.$mirror->name());
      }
      $this->reflect= $params[$arg];
    }
    $this->mirror= $mirror;
  }

  /** @return string */
  public function name() { return $this->reflect['name']; }

  /** @return int */
  public function position() { return $this->reflect['pos']; }

  /** @return lang.mirrors.Routine */
  public function declaringRoutine() { return $this->mirror; }

  /** @return bool */
  public function isVariadic() {
    if (null === $this->reflect['var']) {
      $params= $this->mirror->tags()['param'];
      $n= $this->reflect['pos'];
      $this->reflect['var']= isset($params[$n]) && $params[$n] instanceof VariadicTypeRef;
    }
    return $this->reflect['var'];
  }

  /** @return bool */
  public function isOptional() { return isset($this->reflect['default']) || $this->isVariadic(); }

  /** @return bool */
  public function isVerified() { return isset($this->reflect['type']); }

  /** @return lang.Type */
  public function type() {
    if (null === $this->reflect['type']) {
      $params= $this->mirror->tags()['param'];
      $n= $this->reflect['pos'];
      if (isset($params[$n])) {
        return $params[$n]->resolve($this->mirror->declaredIn()->reflect);
      } else {
        return Type::$VAR;
      }
    } else {
      return $this->reflect['type']();
    }
  }

  /**
   * Returns the default value for an optional parameter
   *
   * @return var
   * @throws lang.IllegalStateException
   */
  public function defaultValue() {
    if (isset($this->reflect['default'])) {
      return $this->reflect['default']();
    }
    throw new IllegalStateException('Parameter is not optional');
  }

  /** @return lang.mirrors.Annotations */
  public function annotations() {
    $annotations= $this->reflect['annotations']();
    return new Annotations($this->mirror->declaredIn(), (array)$annotations);
  }

  /**
   * Compares a given value to this parameter
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self
      ? Objects::compare(
        [$this->mirror, $this->reflect['pos']],
        [$value->mirror, $value->reflect['pos']]
      )
      : 1
    ;
  }

  /** @return string */
  public function hashCode() { return 'R'.md5($this->__toString()); }

  /** @return string */
  public function toString() { return nameof($this).'('.$this->__toString().')'; }

  /** @return string */
  public function __toString() {
    return $this->type().($this->reflect['var'] ? '...' : '').' $'.$this->name();
  }
}