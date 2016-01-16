<?php namespace xp\mirrors;

class Highlighting implements \io\streams\OutputStreamWriter {
  private $out;
  private $patterns= [];
  private $replacements= [];

  public function __construct($out, $replace= []) {
    $this->out= $out;
    foreach ($replace as $pattern => $replacement) {
      $this->patterns[]= $pattern;
      $this->replacements[]= $replacement;
    }
  }

  private function write0($args) {
    $line= '';
    foreach ($args as $arg) {
      $line.= is_string($arg) ? $arg : \xp::stringOf($arg);
    }
    $this->out->write(preg_replace($this->patterns, $this->replacements, $line));
  }

  public function write() {
    $this->write0(func_get_args());
  }

  public function writeLine() {
    $this->write0(func_get_args());
    $this->out->write("\n");
  }

  public function writef() {
    $a= func_get_args();
    $this->write0(vsprintf(array_shift($a), $a));
  }

  public function writeLinef() {
    $a= func_get_args();
    $this->write0(vsprintf(array_shift($a), $a));
    $this->out->write("\n");
  }

  /**
   * Flush output buffer
   *
   * @return void
   */
  public function flush() {
    $this->out->flush();
  }
}