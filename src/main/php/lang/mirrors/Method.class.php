<?php namespace lang\mirrors;

use lang\{IllegalArgumentException, Throwable, Type};
use lang\mirrors\parse\{TagsSource, TagsSyntax};

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
   * @param  php.ReflectionMethod|string|[:var] Map variant returned from Source::methodNamed()
   * @throws lang.IllegalArgumentException If there is no such method
   */
  public function __construct($mirror, $arg) {
    if (is_array($arg)) {
      parent::__construct($mirror, $arg);
    } else if ($arg instanceof \ReflectionMethod) {
      parent::__construct($mirror, $mirror->reflect->methodNamed($arg->name));
    } else {
      parent::__construct($mirror, $mirror->reflect->methodNamed($arg));
    }
  }

  /**
   * Returns the method's return type, or `var` if no return type is declared.
   *
   * @return lang.Type
   */
  public function returns() {
    if (isset($this->reflect['returns'])) {
      return $this->reflect['returns']();
    }

    $return= $this->tags()['return'];
    return isset($return[0]) ? $return[0]->resolve($this->declaredIn()->reflect) : Type::$VAR;
  }

  /**
   * Invokes the method
   *
   * @param  var $instance
   * @param  var[] $args
   * @return var
   * @throws lang.mirrors.TargetInvocationException
   * @throws lang.IllegalArgumentException
   */
  public function invoke($instance= null, $args= []) {
    return $this->reflect['invoke']($instance, $args);
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