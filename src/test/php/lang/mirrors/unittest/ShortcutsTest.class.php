<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;

#[@fixture]
class ShortcutsTest extends \unittest\TestCase {
  use TypeDefinition;

  #[@test]
  public function method_by_name() {
    $mirror= $this->mirror('{ public function fixture() { } }');
    $this->assertEquals($mirror->methods()->named('fixture'), $mirror->method('fixture'));
  }

  #[@test]
  public function field_by_name() {
    $mirror= $this->mirror('{ public $fixture; }');
    $this->assertEquals($mirror->fields()->named('fixture'), $mirror->field('fixture'));
  }

  #[@test]
  public function type_annotation_by_name() {
    $mirror= new TypeMirror(typeof($this));
    $this->assertEquals($mirror->annotations()->named('fixture'), $mirror->annotation('fixture'));
  }

  #[@test]
  public function method_parameter_by_name() {
    $mirror= $this->mirror('{ public function fixture($test) { } }');
    $this->assertEquals(
      $mirror->methods()->named('fixture')->parameters()->named('test'),
      $mirror->method('fixture')->parameter('test')
    );
  }

  #[@test]
  public function method_parameter_by_position() {
    $mirror= $this->mirror('{ public function fixture($test) { } }');
    $this->assertEquals(
      $mirror->methods()->named('fixture')->parameters()->at(0),
      $mirror->method('fixture')->parameter(0)
    );
  }

  #[@test]
  public function method_annotation_by_name() {
    $mirror= $this->mirror("{ #[@test]\npublic function fixture() { } }");
    $this->assertEquals(
      $mirror->methods()->named('fixture')->annotations()->named('test'),
      $mirror->method('fixture')->annotation('test')
    );
  }

  #[@test]
  public function field_annotation_by_name() {
    $mirror= $this->mirror("{ #[@test]\npublic \$fixture; }");
    $this->assertEquals(
      $mirror->fields()->named('fixture')->annotations()->named('test'),
      $mirror->field('fixture')->annotation('test')
    );
  }
}