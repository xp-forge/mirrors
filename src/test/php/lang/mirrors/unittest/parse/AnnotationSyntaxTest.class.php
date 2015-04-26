<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\Value;
use lang\mirrors\parse\Constant;
use lang\mirrors\parse\ArrayExpr;
use lang\mirrors\parse\Member;
use lang\mirrors\parse\Closure;
use lang\mirrors\parse\NewInstance;
use lang\mirrors\parse\Pairs;

/**
 * Tests annotation parsing
 *
 * @see  https://github.com/xp-framework/rfc/issues/16
 */
class AnnotationSyntaxTest extends \unittest\TestCase {

  /**
   * Parses a string
   *
   * @param  string $input
   * @param  string $target
   * @return var
   */
  private function parse($input, $target= null) {
    $unit= (new ClassSyntax())->parse(new StringInput(
      "<?php class Test {\n  $input\n  function fixture() { } }"
    ));
    return $unit->declaration()['method']['fixture']['annotations'][$target];
  }

  #[@test]
  public function annotation_without_value() {
    $this->assertEquals(
      ['test' => null],
      $this->parse('[@test]')
    );
  }

  #[@test]
  public function two_annotations_without_value() {
    $this->assertEquals(
      ['test' => null, 'ignore' => null],
      $this->parse('[@test, @ignore]')
    );
  }

  #[@test, @values([
  #  ['1.5', 1.5], ['-1.5', -1.5], ['+1.5', +1.5],
  #  ['1', 1], ['0', 0], ['-6100', -6100], ['+6100', +6100],
  #  ['""', ''], ["''", ''], ['"Test"', 'Test'], ["'Test'", 'Test']
  #])]
  public function with_primitive_values($literal, $value) {
    $this->assertEquals(
      ['test' => new Value($value)],
      $this->parse('[@test('.$literal.')]')
    );
  }

  #[@test, @values(['true', 'false', 'null', 'M_PI'])]
  public function with_constant_values($literal) {
    $this->assertEquals(
      ['test' => new Constant($literal)],
      $this->parse('[@test('.$literal.')]')
    );
  }

  #[@test, @values([
  #  ['[]', []], ['[1, 2, 3]', [new Value(1), new Value(2), new Value(3)]],
  #  ['array()', []], ['array(1, 2, 3)', [new Value(1), new Value(2), new Value(3)]]
  #])]
  public function with_arrays($literal, $value) {
    $this->assertEquals(
      ['test' => new ArrayExpr($value)],
      $this->parse('[@test('.$literal.')]')
    );
  }

  #[@test, @values([
  #  ['["color" => "green"]', ['color' => new Value('green')]],
  #  ['["a" => "b", "c" => "d"]', ['a' => new Value('b'), 'c' => new Value('d')]],
  #  ['array("a" => "b", "c" => "d")', ['a' => new Value('b'), 'c' => new Value('d')]]
  #])]
  public function with_maps($literal, $value) {
    $this->assertEquals(
      ['test' => new ArrayExpr($value)],
      $this->parse('[@test('.$literal.')]')
    );
  }

  #[@test, @values([
  #  ['function() { }', [], ''],
  #  ['function($a) { }', [['name' => 'a', 'type' => null, 'ref' => false, 'var' => false, 'default' => null]], '']
  #])]
  public function annotation_with_closures($literal, $signature, $code) {
    $this->assertEquals(
      ['test' => new Closure($signature, $code)],
      $this->parse('[@test('.$literal.')]')
    );
  }

  #[@test, @values(['self', '\lang\mirrors\unittest\Test', 'Test'])]
  public function with_class_constant($class) {
    $this->assertEquals(
      ['test' => new Member($class, 'class')],
      $this->parse('[@test('.$class.'::class)]')
    );
  }

  #[@test, @values(['self', '\lang\mirrors\unittest\Test', 'Test'])]
  public function with_constant($class) {
    $this->assertEquals(
      ['test' => new Member($class, 'CONSTANT')],
      $this->parse('[@test('.$class.'::CONSTANT)]')
    );
  }

  #[@test, @values(['self', '\lang\mirrors\unittest\Test', 'Test'])]
  public function with_static_member($class) {
    $this->assertEquals(
      ['test' => new Member($class, '$member')],
      $this->parse('[@test('.$class.'::$member)]')
    );
  }

  #[@test, @values(['self', '\lang\mirrors\unittest\Test', 'Test'])]
  public function with_new($class) {
    $this->assertEquals(
      ['test' => new NewInstance($class, [new Value('Test')])],
      $this->parse('[@test(new '.$class.'("Test"))]')
    );
  }

  #[@test, @values([
  #  ['limit=1.5', ['limit' => new Value(1.5)]],
  #  ['limit=1.5, eta=1.0', ['limit' => new Value(1.5), 'eta' => new Value(1.0)]],
  #  ['limit=[1, 2, 3]', ['limit' => new ArrayExpr([new Value(1), new Value(2), new Value(3)])]],
  #  ['class="Test"', ['class' => new Value('Test')]],
  #  ['return="Test"', ['return' => new Value('Test')]],
  #  ['self="Test"', ['self' => new Value('Test')]],
  #  ['implements="Test"', ['implements' => new Value('Test')]]
  #])]
  public function with_key_value_pairs($literal, $value) {
    $this->assertEquals(
      ['test' => new Pairs($value)],
      $this->parse('[@test('.$literal.')]')
    );
  }

  #[@test]
  public function target_annotation_without_value() {
    $this->assertEquals(
      ['test' => null],
      $this->parse('[@$param: test]', '$param')
    );
  }

  #[@test, @values(['$a', '$b'])]
  public function two_target_annotations_without_values($name) {
    $this->assertEquals(
      ['test' => null],
      $this->parse('[@$a: test, @$b: test]', $name)
    );
  }

  #[@test]
  public function target_annotation_with_key_value_pair() {
    $this->assertEquals(
      ['inject' => new Pairs(['name' => new Value('db')])],
      $this->parse('[@$param: inject(name= "db")]', '$param')
    );
  }
}