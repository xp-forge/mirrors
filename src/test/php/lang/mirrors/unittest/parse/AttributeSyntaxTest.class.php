<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\{ArrayExpr, Closure, Constant, Member, NewInstance, Pairs, PhpSyntax, Value};
use unittest\{Test, Values, TestCase};

/**
 * Tests attribute parsing
 *
 * @see  https://wiki.php.net/rfc/shorter_attribute_syntax_change
 */
class AttributeSyntaxTest extends TestCase {

  /** @return iterable */
  private function named() {
    yield ['limit: 1.5', ['limit' => new Value(1.5)]];
    yield ['limit: 1.5, eta: 1.0', ['limit' => new Value(1.5), 'eta' => new Value(1.0)]];
    yield ['limit: [1, 2, 3]', ['limit' => new ArrayExpr([new Value(1), new Value(2), new Value(3)])]];
    yield ['type: "Test"', ['type' => new Value('Test')]];
    yield ['class: "Test"', ['class' => new Value('Test')]];
    yield ['use: "Test"', ['use' => new Value('Test')]];
    yield ['return: "Test"', ['return' => new Value('Test')]];
    yield ['self: "Test"', ['self' => new Value('Test')]];
    yield ['implements: "Test"', ['implements' => new Value('Test')]];
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
  public function attribute_without_value() {
    $this->assertEquals(
      ['test' => null],
      $this->parse('[Test]')
    );
  }

  #[Test]
  public function two_attributes_without_value() {
    $this->assertEquals(
      ['test' => null, 'ignore' => null],
      $this->parse('[Test, Ignore]')
    );
  }

  #[Test, Values([['1.5', 1.5], ['-1.5', -1.5], ['+1.5', +1.5], ['1', 1], ['0', 0], ['-6100', -6100], ['+6100', +6100], ['""', ''], ["''", ''], ['"Test"', 'Test'], ["'Test'", 'Test']])]
  public function with_primitive_values($literal, $value) {
    $this->assertEquals(
      ['test' => new Value($value)],
      $this->parse('[Test('.$literal.')]')
    );
  }

  #[Test, Values(['true', 'false', 'null', 'M_PI'])]
  public function with_constant_values($literal) {
    $this->assertEquals(
      ['test' => new Constant($literal)],
      $this->parse('[Test('.$literal.')]')
    );
  }

  #[Test, Values(eval: '[["[]", []], ["[1, 2, 3]", [new Value(1), new Value(2), new Value(3)]], ["array()", []], ["array(1, 2, 3)", [new Value(1), new Value(2), new Value(3)]]]')]
  public function with_arrays($literal, $value) {
    $this->assertEquals(
      ['test' => new ArrayExpr($value)],
      $this->parse('[Test('.$literal.')]')
    );
  }

  #[Test, Values(eval: '[["[\"color\" => \"green\"]", ["color" => new Value("green")]], ["[\"a\" => \"b\", \"c\" => \"d\"]", ["a" => new Value("b"), "c" => new Value("d")]], ["array(\"a\" => \"b\", \"c\" => \"d\")", ["a" => new Value("b"), "c" => new Value("d")]]]')]
  public function with_maps($literal, $value) {
    $this->assertEquals(
      ['test' => new ArrayExpr($value)],
      $this->parse('[Test('.$literal.')]')
    );
  }

  #[Test, Values(eval: '[["function() { }", [], ""], ["function(\$a) { }", [["name" => "a", "annotations" => null, "type" => null, "ref" => false, "var" => false, "this" => [], "default" => null]], ""]]')]
  public function attribute_with_closures($literal, $signature, $code) {
    $this->assertEquals(
      ['test' => new Closure($signature, $code)],
      $this->parse('[Test('.$literal.')]')
    );
  }

  #[Test, Values(['self', '\lang\mirrors\unittest\Test', 'Test'])]
  public function with_class_constant($class) {
    $this->assertEquals(
      ['test' => new Member($class, 'class')],
      $this->parse('[Test('.$class.'::class)]')
    );
  }

  #[Test, Values(['self', '\lang\mirrors\unittest\Test', 'Test'])]
  public function with_constant($class) {
    $this->assertEquals(
      ['test' => new Member($class, 'CONSTANT')],
      $this->parse('[Test('.$class.'::CONSTANT)]')
    );
  }

  #[Test, Values('named')]
  public function with_key_value_pairs($literal, $value) {
    $this->assertEquals(
      ['test' => new Pairs($value)],
      $this->parse('[Test('.$literal.')]')
    );
  }

  #[Test]
  public function with_eval() {
    $this->assertEquals(
      ['test' => new Constant('true')],
      $this->parse('[Test(eval: "true")]')
    );
  }
}