<?php namespace xp\mirrors;

use lang\mirrors\TypeMirror;
use lang\mirrors\Methods;

class ClassInformation extends TypeKindInformation {

  /**
   * Display information
   *
   * @param  io.StringWriter $out
   * @return void
   */
  public function display($out) {
    $out->write(self::declarationOf($this->mirror));
    $this->displayExtensions([$this->mirror->parent()], $out, 'extends');
    $this->displayExtensions($this->mirror->interfaces()->declared(), $out, 'implements');

    $separator= false;
    $out->writeLine(' {');
    $this->displayMembers($this->mirror->constants(), $out, $separator);
    $this->displayMembers($this->mirror->fields()->declared(), $out, $separator);
    $constructor= $this->mirror->constructor();
    if ($constructor->present()) {
      $this->displayMembers([$constructor], $out, $separator);
    }
    $this->displayMembers($this->mirror->methods()->all(Methods::ofClass()), $out, $separator);
    $this->displayMembers($this->mirror->methods()->all(Methods::ofInstance()), $out, $separator);
    $out->writeLine('}');
  }
}