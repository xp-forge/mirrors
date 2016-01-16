<?php namespace xp\mirrors;

class InterfaceInformation extends TypeKindInformation {

  /**
   * Display information
   *
   * @param  io.StringWriter $out
   * @return void
   */
  public function display($out) {
    $separator= false;
    $out->write(self::declarationOf($this->mirror));

    $extends= [];
    foreach ($this->mirror->interfaces()->declared() as $parent) {
      $extends[]= $parent->name();
    }
    if ($extends) {
      $out->write(' extends ', implode(', ', $extends));
    }

    $out->writeLine(' {');
    $this->displayMembers($this->mirror->constants(), $out, $separator);
    $this->displayMembers($this->mirror->methods(), $out, $separator);
    $out->writeLine('}');
  }
}