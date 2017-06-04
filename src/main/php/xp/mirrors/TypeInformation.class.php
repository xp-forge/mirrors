<?php namespace xp\mirrors;

use lang\mirrors\TypeMirror;
use lang\IllegalStateException;

class TypeInformation extends Information {
  private $delegate;

  /**
   * Creates a new type information instance
   *
   * @param  lang.mirrors.TypeMirror|lang.XPClass $arg
   * @param  bool $all Whether to display all members, defaults to false
   */
  public function __construct($arg, $all= false) {
    $mirror= $arg instanceof TypeMirror ? $arg : new TypeMirror($arg);
    $kind= $mirror->kind();
    if ($kind->isEnum()) {
      $this->delegate= new EnumInformation($mirror, $all);
    } else if ($kind->isTrait()) {
      $this->delegate= new TraitInformation($mirror, $all);
    } else if ($kind->isInterface()) {
      $this->delegate= new InterfaceInformation($mirror, $all);
    } else if ($kind->isClass()) {
      $this->delegate= new ClassInformation($mirror, $all);
    } else {
      throw new IllegalStateException('Unknown type kind '.$kind->toString());
    }
  }

  /** @return iterable */
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