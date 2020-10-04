<?php namespace lang\mirrors\unittest;

use lang\mirrors\Package;
use unittest\Test;

class PackageTest extends \unittest\TestCase {

  #[Test]
  public function accepts_namespace_constant() {
    $this->assertEquals('lang.mirrors.unittest', (new Package(__NAMESPACE__))->name());
  }

  #[Test]
  public function accepts_dotted_name() {
    $this->assertEquals('lang.mirrors.unittest', (new Package('lang.mirrors.unittest'))->name());
  }

  #[Test]
  public function declaration() {
    $this->assertEquals('unittest', (new Package('lang.mirrors.unittest'))->declaration());
  }

  #[Test]
  public function global_namespace_has_empty_name() {
    $this->assertEquals('', Package::$GLOBAL->name());
  }

  #[Test]
  public function global_namespace_has_empty_declaration() {
    $this->assertEquals('', Package::$GLOBAL->name());
  }

  #[Test]
  public function global_namespace_is_global() {
    $this->assertTrue(Package::$GLOBAL->isGlobal(), '(global)');
  }

  #[Test]
  public function this_namespace_is_not_global() {
    $this->assertFalse((new Package(__NAMESPACE__))->isGlobal(), __NAMESPACE__);
  }
}