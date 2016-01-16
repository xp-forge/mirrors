<?php namespace xp\mirrors;

use lang\mirrors\TypeMirror;

class ClassInformation extends TypeKindInformation {

  /**
   * Display information
   *
   * @param  io.StringWriter $out
   * @return void
   */
  public function display($out) {
    $out->write(self::declarationOf($this->mirror));

    if ($parent= $this->mirror->parent()) {
      $out->write(' extends ', $parent->name());
    }

    $implements= [];
    foreach ($this->mirror->interfaces()->declared() as $type) {
      $implements[]= $type->name();
    }
    if ($implements) {
      $out->write(' implements ', implode(', ', $implements));
    }

    $separator= false;
    $out->writeLine(' {');
    $this->displayMembers($this->mirror->constants(), $out, $separator);
    $this->displayMembers($this->mirror->fields(), $out, $separator);
    $constructor= $this->mirror->constructor();
    if ($constructor->present()) {
      $this->displayMembers([$constructor], $out, $separator);
    }
    $this->displayMembers($this->mirror->methods(), $out, $separator);
    $out->writeLine('}');
  }
}