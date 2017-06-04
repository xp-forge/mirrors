<?php namespace xp\mirrors;

use lang\mirrors\TypeMirror;
use lang\ClassLoader;

abstract class TypeKindInformation extends Information {
  protected $mirror, $visibility;

  /**
   * Creates a new type information instance
   *
   * @param  lang.mirrors.TypeMirror|lang.XPClass $arg
   * @param  bool all Whether to incude all members - defaults: No
   */
  public function __construct($arg, $all= false) {
    $this->mirror= $arg instanceof TypeMirror ? $arg : new TypeMirror($arg);
    $this->visibility= $all
      ? function($member) { return true; }
      : function($member) { return $member->modifiers()->isPublic(); }
    ;
  }

  /** @return iterable */
  public function sources() { yield ClassLoader::getDefault()->findClass($this->mirror->name()); }

  /**
   * Display type extensions
   *
   * @param  iterable $types
   * @param  io.StringWriter $out
   * @param  string $kind
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
   * @param  iterable $members
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