<?php namespace lang\mirrors;

use lang\Type;
use lang\XPClass;

class Parameter extends \lang\Object {
  private $mirror, $reflect;

  /**
   * Creates a new method
   *
   * @param  lang.mirrors.Method $mirror
   * @param  php.ReflectionParameter $reflect
   */
  public function __construct($mirror, $reflect) {
    $this->mirror= $mirror;
    $this->reflect= $reflect;
  }

  /** @return string */
  public function name() { return $this->reflect->name; }

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
}