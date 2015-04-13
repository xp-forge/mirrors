<?php namespace lang\mirrors\unittest;

abstract class SourceTest extends \unittest\TestCase {

  /**
   * Creates a new reflection source
   *
   * @param  string $name
   * @return lang.mirrors.Source
   */
  protected abstract function reflect($name);

  #[@test]
  public function typeName() {
    $this->assertEquals('lang.mirrors.unittest.SourceTest', $this->reflect(self::class)->typeName());
  }
}