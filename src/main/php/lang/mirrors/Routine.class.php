<?php namespace lang\mirrors;

/**
 * Base class for constructors and methods
 */
abstract class Routine extends Member {
  protected static $kind= 'method';
  protected static $tags= ['param' => [], 'return' => [], 'throws' => []];
  private $parameters;

  /**
   * Creates a new method
   *
   * @param  lang.mirrors.TypeMirror $mirror
   * @param  php.ReflectionMethod $reflect
   */
  public function __construct($mirror, $reflect) {
    parent::__construct($mirror, $reflect);
    $reflect->setAccessible(true);
    $this->parameters= new Parameters($this, $reflect);
  }

  /** @return lang.mirrors.Parameters */
  public function parameters() { return $this->parameters; }

  /** @return lang.mirrors.Throws */
  public function throws() { return new Throws($this); }
}