<?php namespace lang\mirrors\parse;

use lang\FunctionType;
use util\Objects;

/**
 * Function type reference
 *
 * @test  xp://lang.mirrors.unittest.FunctionTypeRefTest
 */
class FunctionTypeRef extends \lang\Object {
  private $parameters, $return;

  /**
   * Creates a new function type reference
   *
   * @param  lang.mirrors.parse.TypeRef[] $parameters
   * @param  lang.mirrors.parse.TypeRef $return
   */
  public function __construct($parameters, $return) {
    $this->parameters= $parameters;
    $this->return= $return;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.reflection.TypeMirror $type
   * @return var
   */
  public function resolve($type) {
    $parameters= [];
    foreach ($this->parameters as $param) {
      $parameters[]= $param->resolve($type);
    }
    return new FunctionType($parameters, $this->return->resolve($type));
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && (
      Objects::equal($this->parameters, $cmp->parameters) &&
      $this->return->equals($cmp->return)
    );
  }

  /**
   * Returns a string represenation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'<('.Objects::stringOf($this->parameters).'): '.$this->return->toString().'>';
  }
}