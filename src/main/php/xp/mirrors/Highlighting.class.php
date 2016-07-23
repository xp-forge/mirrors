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

  /**
   * Write
   *
   * @param  var[] $args
   * @return void
   */
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
   * @param  var... $args
   */
  public function write(... $args) {
    $this->write0($args);
  }

  /**
   * Write line
   *
   * @param  var... $args
   */
  public function writeLine(... $args) {
    $this->write0($args);
    $this->out->write("\n");
  }
}