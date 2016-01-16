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
    $out->write(self::declarationOf($this->mirror), ' {');
    $this->displayConstants($this->mirror, $out, $separator);
    $this->displayMethods($this->mirror, $out, $separator);
    $out->writeLine('}');
  }
}