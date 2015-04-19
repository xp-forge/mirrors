<?php namespace lang\mirrors;

use lang\ElementNotFoundException;

class Parameters extends \lang\Object implements \IteratorAggregate {
  const BY_ID = 0, BY_NAME = 1;

  private $mirror, $reflect;
  private $lookup= null;

  /**
   * Creates a new parameters instance
   *
   * @param  lang.mirrors.Method $mirror
   * @param  [:var] $reflect
   */
  public function __construct($mirror, $reflect) {
    $this->mirror= $mirror;
    $this->reflect= $reflect;
  }

  /**
   * Returns whether parameters are present
   *
   * @return bool
   */
  public function present() { return !empty($this->lookup()[self::BY_ID]); } 

  /**
   * Populates lookup maps BY_ID and BY_NAME lazily, then returns it.
   *
   * @return [:var]
   */
  private function lookup() {
    if (null === $this->lookup) {
      $params= $this->reflect['params']();
      $this->lookup= [self::BY_ID => $params, self::BY_NAME => []];
      foreach ($params as $pos => $param) {
        $this->lookup[self::BY_NAME][$param['name']]= $pos;
      }
    }
    return $this->lookup;
  }

  /**
   * Checks whether a given method is provided
   *
   * @param  string $name
   * @return bool
   */
  public function provides($name) {
    return isset($this->lookup()[self::BY_NAME][$name]);
  }

  /**
   * Returns how many parameters exist.
   *
   * @return int
   */
  public function length() { return sizeof($this->lookup()[self::BY_ID]); }

  /**
   * Returns a given method if provided or raises an exception
   *
   * @param  string $name
   * @return lang.mirrors.Parameter
   * @throws lang.ElementNotFoundException
   */
  public function named($name) {
    $lookup= $this->lookup();
    if (isset($lookup[self::BY_NAME][$name])) {
      $pos= $lookup[self::BY_NAME][$name];
      return new Parameter($this, $lookup[self::BY_ID][$pos]);
    }
    throw new ElementNotFoundException('No parameter '.$name.' in '.$this->mirror->name());
  }

  /**
   * Returns parameter at a given offset
   *
   * @param  int $position
   * @return lang.mirrors.Parameter
   * @throws lang.ElementNotFoundException
   */
  public function at($position) {
    $params= $this->lookup()[self::BY_ID];
    if (isset($params[$position])) {
      return new Parameter($this->mirror, $params[$position]);
    }
    throw new ElementNotFoundException('No parameter #'.$position.' in '.$this->mirror->name());
  }

  /**
   * Returns first parameter
   *
   * @param  string $name
   * @return lang.mirrors.Parameter
   * @throws lang.ElementNotFoundException
   */
  public function first() {
    $params= $this->lookup()[self::BY_ID];
    if (isset($params[0])) {
      return new Parameter($this->mirror, $params[0]);
    }
    throw new ElementNotFoundException('No parameters in '.$this->mirror->name());
  }

  /**
   * Iterates over all parameters
   *
   * @return php.Generator
   */
  public function getIterator() {
    foreach ($this->lookup()[self::BY_ID] as $param) {
      yield new Parameter($this->mirror, $param);
    }
  }
}