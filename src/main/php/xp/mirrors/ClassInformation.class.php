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
    $out->write($this->mirror->modifiers()->names(), ' class ', $this->mirror->name());
    if ($parent= $this->mirror->parent()) {
      $out->write(' extends ', $parent->name());
    }

    $separator= false;
    $out->writeLine(' {');
    $this->displayConstants($this->mirror, $out, $separator);
    $this->displayFields($this->mirror, $out, $separator);

    $constructor= $this->mirror->constructor();
    if ($constructor->present()) {
      $separator && $out->writeLine();
      $out->writeLine('  ', (string)$constructor);
      $separator= true;
    }

    $this->displayMethods($this->mirror, $out, $separator);
    $out->writeLine('}');
  }
}