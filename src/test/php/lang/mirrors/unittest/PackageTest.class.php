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
}