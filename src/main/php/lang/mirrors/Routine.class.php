<?php namespace lang\mirrors;

/**
 * Base class for constructors and methods
 *
 * @see    xp://lang.mirrors.Constructor
 * @see    xp://lang.mirrors.Method
 */
abstract class Routine extends Member {
  protected static $kind= 'method';
  protected static $tags= ['param' => [], 'return' => [], 'throws' => []];
  private $parameters;

  /**
   * Creates a new routine
   *
   * @param  lang.mirrors.TypeMirror $mirror
   * @param  php.ReflectionMethod $reflect
   */
  public function __construct($mirror, $reflect) {
    parent::__construct($mirror, $reflect);
    $this->parameters= new Parameters($this, $reflect);
  }

  /** @return lang.mirrors.Parameters */
  public function parameters() { return $this->parameters; }

  /**
   * Returns a parameter by a given name
   *
   * @param  string|int $arg
   * @return lang.mirrors.Parameter
   * @throws lang.ElementNotFoundException
   */
  public function parameter($arg) {
    return is_int($arg) ? $this->parameters->at($arg) : $this->parameters->named($arg);
  }

  /** @return lang.mirrors.Throws */
  public function throws() { return new Throws($this); }
}