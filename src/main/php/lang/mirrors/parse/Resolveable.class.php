<?php namespace lang\mirrors\parse;

abstract class Resolveable extends \lang\Object {

  /**
   * Resolve this value 
   *
   * @param  lang.reflection.TypeMirror $type
   * @return var
   */
  public abstract function resolve($type);

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'<'.$this.'>';
  }

  /** @return string */
  public abstract function __toString();

}