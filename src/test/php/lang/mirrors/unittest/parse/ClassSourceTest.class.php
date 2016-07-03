<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\ClassSource;
use lang\DynamicClassLoader;
use lang\ClassFormatException;

class ClassSourceTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new ClassSource(nameof($this));
  }

  #[@test]
  public function source_of_this_class_is_present() {
    $this->assertTrue((new ClassSource(nameof($this)))->present());
  }

  #[@test]
  public function source_of_nonexistant_class_is_present() {
    $this->assertFalse((new ClassSource('@non-existant@'))->present());
  }

  #[@test, @values(['<?php', '<?php ', "<?php\n", '<?php namespace test;'])]
  public function parse_php($variant) {
    $class= nameof($this).$this->name.md5($variant);
    DynamicClassLoader::instanceFor(__CLASS__)->setClassBytes($class, 'class Test { }', $variant);
    $this->assertEquals('php', (new ClassSource($class))->usedSyntax());
  }

  #[@test, @values(['<?hh', '<?hh ', "<?hh\n", '<?hh namespace test;'])]
  public function parse_hh($variant) {
    $class= nameof($this).$this->name.md5($variant);
    DynamicClassLoader::instanceFor(__CLASS__)->setClassBytes($class, 'class Test { }', $variant);
    $this->assertEquals('hh', (new ClassSource($class))->usedSyntax());
  }

  #[@test, @expect(ClassFormatException::class)]
  public function cannot_parse_xml() {
    $class= nameof($this).$this->name;
    DynamicClassLoader::instanceFor(__CLASS__)->setClassBytes($class, '', '<?xml version="1.0">');
    new ClassSource($class);
  }
}