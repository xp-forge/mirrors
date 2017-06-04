<?php namespace lang\mirrors\parse;

abstract class Resolveable implements \lang\Value {

  /**
   * Resolve this value 
   *
   * @param  lang.mirrors.Source $source
   * @return var
   */
  public abstract function resolve($source);

  /**
   * Compares a given value to this list
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? strcmp($this->__toString(), $value->__toString()) : 1;
  }

  /** @return string */
  public function hashCode() { return '*'.md5($this->__toString()); }

  /** @return string */
  public function toString() { return nameof($this).'<'.$this->__toString().'>'; }

  /** @return string */
  public abstract function __toString();

}