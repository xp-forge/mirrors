<?php namespace xp\mirrors;

use lang\mirrors\TypeMirror;
use lang\IllegalStateException;

class TypeInformation {
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

  public function source() { return $this->delegate->source(); }

  /**
   * Display information
   *
   * @param  io.StringWriter $out
   * @return void
   */
  public function display($out) {
    $this->delegate->display($out);
  }

  protected function displayConstants($mirror, $out, &$separator) {
    $separator && $out->writeLine();
    foreach ($mirror->constants() as $constant) {
      $out->writeLine('  ', (string)$constant);
      $separator= true;
    }
  }

  protected function displayFields($mirror, $out, &$separator) {
    $separator && $out->writeLine();
    foreach ($mirror->fields()->declared() as $fields) {
      $out->writeLine('  ', (string)$fields);
      $separator= true;
    }
  }

  protected function displayMethods($mirror, $out, &$separator) {
    $separator && $out->writeLine();
    foreach ($mirror->methods()->declared() as $method) {
      $out->writeLine('  ', (string)$method);
      $separator= true;
    }
  }
}