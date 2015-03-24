<?php namespace lang\mirrors;

use lang\IllegalArgumentException;

class Constructor extends Routine {

  /**
   * Creates a new constructor
   *
   * @param  lang.mirrors.TypeMirror $mirror
   * @param  php.ReflectionMethod $arg Optionally
   * @throws lang.IllegalArgumentException If there is no constructor
   */
  public function __construct($mirror, $arg= null) {
    if ($arg instanceof \ReflectionMethod) {
      $reflect= $arg;
    } else {
      if (null === ($reflect= $mirror->reflect->getConstructor())) {
        throw new IllegalArgumentException('No constructor in '.$mirror->name());
      }
    }
    parent::__construct($mirror, $reflect);
  }
}