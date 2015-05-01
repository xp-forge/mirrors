<?php namespace lang\mirrors\parse;

abstract class Resolveable extends \lang\Object {

  /**
   * Resolve this value 
   *
   * @param  lang.mirrors.Source $source
   * @return var
   */
  public abstract function resolve($source);

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