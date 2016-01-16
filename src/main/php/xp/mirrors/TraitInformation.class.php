<?php namespace xp\mirrors;

class TraitInformation extends TypeKindInformation {

  /**
   * Display information
   *
   * @param  io.StringWriter $out
   * @return void
   */
  public function display($out) {
    $separator= false;
    $out->write('public trait ', $this->mirror->name());

    $out->writeLine(' {');
    $this->displayConstants($this->mirror, $out, $separator);
    $this->displayFields($this->mirror, $out, $separator);
    $this->displayMethods($this->mirror, $out, $separator);
    $out->writeLine('}');
  }
}