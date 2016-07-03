<?php namespace xp\mirrors;

use lang\mirrors\TypeMirror;
use lang\ClassLoader;

abstract class TypeKindInformation extends Information {
  protected $mirror;

  /**
   * Creates a new type information instance
   *
   * @param  lang.mirrors.TypeMirror|lang.XPClass $arg
   */
  public function __construct($arg) {
    $this->mirror= $arg instanceof TypeMirror ? $arg : new TypeMirror($arg);
  }

  /** @return php.Generator */
  public function sources() { yield ClassLoader::getDefault()->findClass($this->mirror->name()); }

  /**
   * Display type extensions
   *
   * @param  php.Generator $types
   * @param  io.StringWriter $out
   * @param  string $kinde
   * @return void
   */
  protected function displayExtensions($types, $out, $kind) {
    $extensions= [];
    foreach ($types as $type) {
      $type && $extensions[]= $type->name();
    }
    if ($extensions) {
      $out->write(' '.$kind.' ', implode(', ', $extensions));
    }
  }

  /**
   * Display members
   *
   * @param  php.Generator $members
   * @param  io.StringWriter $out
   * @param  bool $separator
   * @return void
   */
  protected function displayMembers($members, $out, &$separator) {
    if ($separator) {
      $out->writeLine();
      $separator= false;
    }
    foreach ($members as $members) {
      $out->writeLine('  ', (string)$members);
      $separator= true;
    }
  }
}