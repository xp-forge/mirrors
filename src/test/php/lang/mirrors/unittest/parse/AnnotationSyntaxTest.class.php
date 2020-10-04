<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\{ArrayExpr, Closure, Constant, Member, NewInstance, Pairs, PhpSyntax, Value};
use unittest\{Test, Values, TestCase};

/**
 * Tests annotation parsing
 *
 * @see  https://github.com/xp-framework/rfc/issues/16
 */
class AnnotationSyntaxTest extends TestCase {

  /** @return iterable */
  private function pairs() {
    yield ['limit=1.5', ['limit' => new Value(1.5)]];
    yield ['limit=1.5, eta=1.0', ['limit' => new Value(1.5), 'eta' => new Value(1.0)]];
    yield ['limit=[1, 2, 3]', ['limit' => new ArrayExpr([new Value(1), new Value(2), new Value(3)])]];
    yield ['type="Test"', ['type' => new Value('Test')]];
    yield ['class="Test"', ['class' => new Value('Test')]];
    yield ['use="Test"', ['use' => new Value('Test')]];
    yield ['return="Test"', ['return' => new Value('Test')]];
    yield ['self="Test"', ['self' => new Value('Test')]];
    yield ['implements="Test"', ['implements' => new Value('Test')]];
  }

  /**
   * Parses a string
   *
   * @param  string $input
   * @param  string $target
   * @return var
   */
  private function parse($input, $target= null) {
    $unit= (new PhpSyntax())->parse(new StringInput(
      "<?php class Test {\n  #$input\n  function fixture() { } }"
    ));
    return $unit->declaration()['method']['fixture']['annotations'][$target];
  }

  #[Test]
  public function annotation_without_value() {
    $this->assertEquals(
      ['test' => null],
      $this->parse('[@test]')
    );
  }

  #[Test]
  public function two_annotations_without_value() {
    $this->assertEquals(
      ['test' => null, 'ignore' => null],
      $this->parse('[@test, @ignore]')
    );
  }

  #[Test, Values([['1.5', 1.5], ['-1.5', -1.5], ['+1.5', +1.5], ['1', 1], ['0', 0], ['-6100', -6100], ['+6100', +6100], ['""', ''], ["''", ''], ['"Test"', 'Test'], ["'Test'", 'Test']])]
  public function with_primitive_values($literal, $value) {
    $this->assertEquals(
      ['test' => new Value($value)],
      $this->parse('[@test('.$literal.')]')
    );
  }

  #[Test, Values(['true', 'false', 'null', 'M_PI'])]
  public function with_constant_values($literal) {
    $this->assertEquals(
      ['test' => new Constant($literal)],
      $this->parse('[@test('.$literal.')]')
    );
  }

  #[Test, Values(eval: '[["[]", []], ["[1, 2, 3]", [new Value(1), new Value(2), new Value(3)]], ["array()", []], ["array(1, 2, 3)", [new Value(1), new Value(2), new Value(3)]]]')]
  public function with_arrays($literal, $value) {
    $this->assertEquals(
      ['test' => new ArrayExpr($value)],
      $this->parse('[@test('.$literal.')]')
    );
  }

  #[Test, Values(eval: '[["[\"color\" => \"green\"]", ["color" => new Value("green")]], ["[\"a\" => \"b\", \"c\" => \"d\"]", ["a" => new Value("b"), "c" => new Value("d")]], ["array(\"a\" => \"b\", \"c\" => \"d\")", ["a" => new Value("b"), "c" => new Value("d")]]]')]
  public function with_maps($literal, $value) {
    $this->assertEquals(
      ['test' => new ArrayExpr($value)],
      $this->parse('[@test('.$literal.')]')
    );
  }

  #[Test, Values(eval: '[["function() { }", [], ""], ["function(\$a) { }", [["name" => "a", "annotations" => null, "type" => null, "ref" => false, "var" => false, "this" => [], "default" => null]], ""]]')]
  public function annotation_with_closures($literal, $signature, $code) {
    $this->assertEquals(
      ['test' => new Closure($signature, $code)],
      $this->parse('[@test('.$literal.')]')
    );
  }

  #[Test, Values(['self', '\lang\mirrors\unittest\Test', 'Test'])]
  public function with_class_constant($class) {
    $this->assertEquals(
      ['test' => new Member($class, 'class')],
      $this->parse('[@test('.$class.'::class)]')
    );
  }

  #[Test, Values(['self', '\lang\mirrors\unittest\Test', 'Test'])]
  public function with_constant($class) {
    $this->assertEquals(
      ['test' => new Member($class, 'CONSTANT')],
      $this->parse('[@test('.$class.'::CONSTANT)]')
    );
  }

  #[Test, Values(['self', '\lang\mirrors\unittest\Test', 'Test'])]
  public function with_static_member($class) {
    $this->assertEquals(
      ['test' => new Member($class, '$member')],
      $this->parse('[@test('.$class.'::$member)]')
    );
  }

  #[Test, Values(['self', '\lang\mirrors\unittest\Test', 'Test'])]
  public function with_new($class) {
    $this->assertEquals(
      ['test' => new NewInstance($class, [new Value('Test')])],
      $this->parse('[@test(new '.$class.'("Test"))]')
    );
  }

  #[Test, Values('pairs')]
  public function with_key_value_pairs($literal, $value) {
    $this->assertEquals(
      ['test' => new Pairs($value)],
      $this->parse('[@test('.$literal.')]')
    );
  }

  #[Test]
  public function target_annotation_without_value() {
    $this->assertEquals(
      ['test' => null],
      $this->parse('[@$param: test]', '$param')
    );
  }

  #[Test, Values(['$a', '$b'])]
  public function two_target_annotations_without_values($name) {
    $this->assertEquals(
      ['test' => null],
      $this->parse('[@$a: test, @$b: test]', $name)
    );
  }

  #[Test]
  public function target_annotation_with_key_value_pair() {
    $this->assertEquals(
      ['inject' => new Pairs(['name' => new Value('db')])],
      $this->parse('[@$param: inject(name= "db")]', '$param')
    );
  }
}