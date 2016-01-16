<?php namespace xp\mirrors;

use lang\mirrors\TypeMirror;
use lang\IllegalStateException;

class TypeInformation extends Information {
  private $delegate;

  /**
   * Creates a new type information instance
   *
   * @param  lang.mirrors.TypeMirror|lang.XPClass $arg
   */
  public function __construct($arg) {
    $mirror= $arg instanceof TypeMirror ? $arg : new TypeMirror($arg);
    $kind= $mirror->kind();
    if ($kind->isEnum()) {
      $this->delegate= new EnumInformation($mirror);
    } else if ($kind->isTrait()) {
      $this->delegate= new TraitInformation($mirror);
    } else if ($kind->isInterface()) {
      $this->delegate= new InterfaceInformation($mirror);
    } else if ($kind->isClass()) {
      $this->delegate= new ClassInformation($mirror);
    } else {
      throw new IllegalStateException('Unknown type kind '.$kind->toString());
    }
  }

  /** @return php.Generator */
  public function sources() {
    foreach ($this->delegate->sources() as $source) {
      yield $source;
    }
  }

  /**
   * Display information
   *
   * @param  io.StringWriter $out
   * @return void
   */
  public function display($out) {
    $this->delegate->display($out);
  }
}