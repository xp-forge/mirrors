<?php namespace xp\mirrors;

use lang\mirrors\{Fields, Methods};

class TraitInformation extends TypeKindInformation {

  /**
   * Display information
   *
   * @param  io.StringWriter $out
   * @return void
   */
  public function display($out) {
    $separator= false;
    $out->write(self::declarationOf($this->mirror), ' {');
    $this->displayMembers($this->mirror->constants(), $out, $separator);
    $this->displayMembers($this->mirror->fields()->all(Fields::with($this->visibility)), $out, $separator);
    $constructor= $this->mirror->constructor();
    if ($constructor->present()) {
      $this->displayMembers([$constructor], $out, $separator);
    }
    $this->displayMembers($this->mirror->methods()->all(Methods::with($this->visibility)), $out, $separator);
    $out->writeLine('}');
  }
}