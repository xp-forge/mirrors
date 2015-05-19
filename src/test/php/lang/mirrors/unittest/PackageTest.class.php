<?php namespace lang\mirrors\unittest;

use lang\mirrors\Package;

class PackageTest extends \unittest\TestCase {

  #[@test]
  public function accepts_namespace_constant() {
    $this->assertEquals('lang.mirrors.unittest', (new Package(__NAMESPACE__))->name());
  }

  #[@test]
  public function accepts_dotted_name() {
    $this->assertEquals('lang.mirrors.unittest', (new Package('lang.mirrors.unittest'))->name());
  }

  #[@test]
  public function declaration() {
    $this->assertEquals('unittest', (new Package('lang.mirrors.unittest'))->declaration());
  }

  #[@test]
  public function global_namespace_has_empty_name() {
    $this->assertEquals('', Package::$GLOBAL->name());
  }

  #[@test]
  public function global_namespace_has_empty_declaration() {
    $this->assertEquals('', Package::$GLOBAL->name());
  }

  #[@test]
  public function global_namespace_is_global() {
    $this->assertTrue(Package::$GLOBAL->isGlobal(), '(global)');
  }

  #[@test]
  public function this_namespace_is_not_global() {
    $this->assertFalse((new Package(__NAMESPACE__))->isGlobal(), __NAMESPACE__);
  }
}