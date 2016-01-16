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
  public function sources() { yield ClassLoader::findClass($this->mirror->name()); }

  /**
   * Display members
   *
   * @param  lang.mirrors.TypeMirror $mirror
   * @param  io.StringWriter $out
   * @param  bool $separator
   * @return void
   */
  protected function displayMembers($members, $out, &$separator) {
    $separator && $out->writeLine();
    foreach ($members as $members) {
      $out->writeLine('  ', (string)$members);
      $separator= true;
    }
  }
}