<?php namespace lang\mirrors;

use lang\Generic;
use lang\Throwable;
use lang\Type;
use lang\IllegalArgumentException;
use lang\mirrors\parse\TagsSyntax;
use lang\mirrors\parse\TagsSource;

/**
 * A class method
 *
 * @test   xp://lang.mirrors.unittest.MethodTest
 */
class Method extends Routine {

  /**
   * Creates a new method
   *
   * @param  lang.mirrors.TypeMirror $mirror
   * @param  [:var] $reflect
   * @throws lang.IllegalArgumentException If there is no such method
   */
  public function __construct($mirror, $arg) {
    if (is_array($arg)) {
      $reflect= $arg;
    } else if ($arg instanceof \ReflectionMethod) {
      $reflect= $mirror->reflect->methodNamed($arg->name);
    } else {
      try {
        $reflect= $mirror->reflect->methodNamed($arg);
      } catch (\Exception $e) {
        throw new IllegalArgumentException('No method named '.$arg.'() in '.$mirror->name());
      }
    }
    parent::__construct($mirror, $reflect);
  }

  /**
   * Returns the method's return type, or `var` if no return type is declared.
   *
   * @return lang.Type
   */
  public function returns() {
    $return= $this->tags()['return'];
    return empty($return) ? Type::$VAR : $return[0]->resolve($this->declaredIn());
  }

  /**
   * Invokes the method
   *
   * @param  lang.Generic $instance
   * @param  var[] $args
   * @return var
   * @throws lang.mirrors.TargetInvocationException
   * @throws lang.IllegalArgumentException
   */
  public function invoke(Generic $instance= null, $args= []) {
    return $this->mirror->reflect->invokeMethod($this->reflect['value'], $instance, $args);
  }

  /** @return string */
  public function __toString() {
    $params= '';
    foreach ($this->parameters() as $param) {
      $params.= ', '.$param;
    }
    return $this->modifiers()->names().' '.$this->returns().' '.$this->name().'('.substr($params, 2).')';
  }
}