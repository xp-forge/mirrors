<?php namespace lang\mirrors;

/**
 * Represents a constructor. If no constructor is present, a default 
 * no-arg constructor is used.
 *
 * @test   xp://lang.mirrors.unittest.ConstructorTest
 */
class Constructor extends Routine {

  /**
   * Creates a new constructor
   *
   * @param  lang.mirrors.TypeMirror $mirror
   */
  public function __construct($mirror) {
    parent::__construct($mirror, $mirror->reflect->constructor());
  }

  /** @return bool */
  public function present() { return '__default' !== $this->reflect['name']; }

  /**
   * Creates a new instance using this constructor
   *
   * @param  var... $args
   * @return var
   */
  public function newInstance(... $args) { return $this->mirror->reflect->newInstance($args); }

  /** @return string */
  public function __toString() {
    $params= '';
    foreach ($this->parameters() as $param) {
      $params.= ', '.$param;
    }
    return $this->modifiers()->names().' __construct('.substr($params, 2).')';
  }
}