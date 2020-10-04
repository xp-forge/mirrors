<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use unittest\Test;

#[@fixture]
class ShortcutsTest extends \unittest\TestCase {
  use TypeDefinition;

  #[Test]
  public function method_by_name() {
    $mirror= $this->mirror('{ public function fixture() { } }');
    $this->assertEquals($mirror->methods()->named('fixture'), $mirror->method('fixture'));
  }

  #[Test]
  public function field_by_name() {
    $mirror= $this->mirror('{ public $fixture; }');
    $this->assertEquals($mirror->fields()->named('fixture'), $mirror->field('fixture'));
  }

  #[Test]
  public function type_annotation_by_name() {
    $mirror= new TypeMirror(typeof($this));
    $this->assertEquals($mirror->annotations()->named('fixture'), $mirror->annotation('fixture'));
  }

  #[Test]
  public function method_parameter_by_name() {
    $mirror= $this->mirror('{ public function fixture($test) { } }');
    $this->assertEquals(
      $mirror->methods()->named('fixture')->parameters()->named('test'),
      $mirror->method('fixture')->parameter('test')
    );
  }

  #[Test]
  public function method_parameter_by_position() {
    $mirror= $this->mirror('{ public function fixture($test) { } }');
    $this->assertEquals(
      $mirror->methods()->named('fixture')->parameters()->at(0),
      $mirror->method('fixture')->parameter(0)
    );
  }

  #[Test]
  public function method_annotation_by_name() {
    $mirror= $this->mirror("{ #[@test]\npublic function fixture() { } }");
    $this->assertEquals(
      $mirror->methods()->named('fixture')->annotations()->named('test'),
      $mirror->method('fixture')->annotation('test')
    );
  }

  #[Test]
  public function field_annotation_by_name() {
    $mirror= $this->mirror("{ #[@test]\npublic \$fixture; }");
    $this->assertEquals(
      $mirror->fields()->named('fixture')->annotations()->named('test'),
      $mirror->field('fixture')->annotation('test')
    );
  }
}