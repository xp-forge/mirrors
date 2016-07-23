<?php namespace xp\mirrors;

class Highlighting {
  private $out;
  private $patterns= [];
  private $replacements= [];

  /**
   * Creates a highlighting instance
   *
   * @param  io.streams.OutputStreamWriter $out
   * @param  [:var] $replace
   */
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

  /**
   * Write
   *
   * @param  string... $args
   */
  public function write() {
    $this->write0(func_get_args());
  }

  /**
   * Write line
   *
   * @param  string... $args
   */
  public function writeLine() {
    $this->write0(func_get_args());
    $this->out->write("\n");
  }
}