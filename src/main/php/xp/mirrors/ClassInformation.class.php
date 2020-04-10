<?php namespace xp\mirrors;

use lang\mirrors\{Fields, Methods, TypeMirror};

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
    $this->displayMembers($this->mirror->fields()->declared(Fields::with($this->visibility)), $out, $separator);
    $constructor= $this->mirror->constructor();
    if ($constructor->present()) {
      $this->displayMembers([$constructor], $out, $separator);
    } else {
      $separator= false;
    }
    $this->displayMembers($this->mirror->methods()->all(Methods::ofClass()->with($this->visibility)), $out, $separator);
    $this->displayMembers($this->mirror->methods()->all(Methods::ofInstance()->with($this->visibility)), $out, $separator);
    $out->writeLine('}');
  }
}