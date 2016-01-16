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
    $out->write('public interface ', $this->mirror->name());

    $out->writeLine(' {');
    $this->displayConstants($this->mirror, $out, $separator);
    $this->displayMethods($this->mirror, $out, $separator);
    $out->writeLine('}');
  }
}