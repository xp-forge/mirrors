<?php namespace xp\mirrors;

use lang\mirrors\TypeMirror;
use lang\ClassLoader;

abstract class TypeKindInformation {
  protected $mirror;

  /**
   * Creates a new type information instance
   *
   * @param  lang.mirrors.TypeMirror|lang.XPClass $arg
   */
  public function __construct($arg) {
    $this->mirror= $arg instanceof TypeMirror ? $arg : new TypeMirror($arg);
  }

  public function source() { return ClassLoader::findClass($this->mirror->name()); }

  /**
   * Display information
   *
   * @param  io.StringWriter $out
   * @return void
   */
  public abstract function display($out);

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