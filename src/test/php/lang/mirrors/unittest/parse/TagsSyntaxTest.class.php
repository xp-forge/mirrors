<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\{ArrayTypeRef, FunctionTypeRef, GenericTypeRef, MapTypeRef, ReferenceTypeRef, TagsSource, TagsSyntax, TypeRef, TypeUnionRef};
use lang\{Primitive, Type};
use unittest\{Test, Values};

class TagsSyntaxTest extends \unittest\TestCase {

  /**
   * Parses a string
   *
   * @param  string $input
   * @return lang.mirrors.parse.CodeUnit
   */
  private function parse($input) {
    return (new TagsSyntax())->parse(new TagsSource($input));
  }

  /** @return iterable */
  private function types() {
    yield ['@param callable', new TypeRef(Type::$CALLABLE)];
    yield ['@param array', new TypeRef(Type::$ARRAY)];
    yield ['@param void', new TypeRef(Type::$VOID)];
    yield ['@param var', new TypeRef(Type::$VAR)];
    yield ['@param self', new ReferenceTypeRef('self')];
    yield ['@param parent', new ReferenceTypeRef('parent')];
    yield ['@param static', new ReferenceTypeRef('static')];
    yield ['@param var[]', new ArrayTypeRef(new TypeRef(Type::$VAR))];
    yield ['@param string[][]', new ArrayTypeRef(new ArrayTypeRef(new TypeRef(Primitive::$STRING)))];
    yield ['@param [:var]', new MapTypeRef(new TypeRef(Type::$VAR))];
    yield ['@param [:[:string]]', new MapTypeRef(new MapTypeRef(new TypeRef(Primitive::$STRING)))];
    yield ['@param [:int[]]', new MapTypeRef(new ArrayTypeRef(new TypeRef(Primitive::$INT)))];
    yield ['@param [:[:string]]', new MapTypeRef(new MapTypeRef(new TypeRef(Primitive::$STRING)))];
    yield ['@param resource', new TypeRef(Type::$VAR)];
    yield ['@param mixed', new TypeRef(Type::$VAR)];
    yield ['@param null', new TypeRef(Type::$VOID)];
    yield ['@param false', new TypeRef(Primitive::$BOOL)];
    yield ['@param true', new TypeRef(Primitive::$BOOL)];
    yield ['@param $this', new ReferenceTypeRef('self')];
    yield ['@param boolean', new TypeRef(Primitive::$BOOL)];
    yield ['@param integer', new TypeRef(Primitive::$INT)];
    yield ['@param float', new TypeRef(Primitive::$DOUBLE)];
    yield ['@param \Iterator', new ReferenceTypeRef('\Iterator')];
    yield ['@param \stubbles\lang\Sequence', new ReferenceTypeRef('\stubbles\lang\Sequence')];
    yield ['@param \DateTime[]', new ArrayTypeRef(new ReferenceTypeRef('\DateTime'))];
    yield ['@param \ArrayObject|\DateTime[]', new TypeUnionRef([
      new ReferenceTypeRef('\ArrayObject'),
      new ArrayTypeRef(new ReferenceTypeRef('\DateTime'))
    ])];
    yield ['@param function(): var', new FunctionTypeRef(
      [],
      new TypeRef(Type::$VAR)
    )];
    yield ['@param function(string): var', new FunctionTypeRef(
      [new TypeRef(Primitive::$STRING)],
      new TypeRef(Type::$VAR)
    )];
    yield ['@param function(string, int): void', new FunctionTypeRef(
      [new TypeRef(Primitive::$STRING), new TypeRef(Primitive::$INT)],
      new TypeRef(Type::$VOID)
    )];
    yield ['@param function(string[]): var', new FunctionTypeRef(
      [new ArrayTypeRef(new TypeRef(Primitive::$STRING))],
      new TypeRef(Type::$VAR)
    )];
    yield ['@param function(string[]): var[]', new FunctionTypeRef(
      [new ArrayTypeRef(new TypeRef(Primitive::$STRING))],
      new ArrayTypeRef(new TypeRef(Type::$VAR))
    )];
    yield ['@param function([:string]): void', new FunctionTypeRef(
      [new MapTypeRef(new TypeRef(Primitive::$STRING))],
      new TypeRef(Type::$VOID)
    )];
    yield ['@param util.collections.List<int>', new GenericTypeRef(
      new ReferenceTypeRef('util.collections.List'),
      [new TypeRef(Primitive::$INT)]
    )];
    yield ['@param util.collections.Map<string, int>', new GenericTypeRef(
      new ReferenceTypeRef('util.collections.Map'),
      [new TypeRef(Primitive::$STRING), new TypeRef(Primitive::$INT)]
    )];
    yield ['@param util.collections.Map<string, int>[]', new ArrayTypeRef(new GenericTypeRef(
      new ReferenceTypeRef('util.collections.Map'),
      [new TypeRef(Primitive::$STRING), new TypeRef(Primitive::$INT)]
    ))];
    yield ['@param [:util.collections.Map<string, int>]', new MapTypeRef(new GenericTypeRef(
      new ReferenceTypeRef('util.collections.Map'),
      [new TypeRef(Primitive::$STRING), new TypeRef(Primitive::$INT)]
    ))];
  }

  #[Test, Values(['@param string', '@param string $input', '@param string $input The input parameter'])]
  public function single_parameter($declaration) {
    $this->assertEquals(
      ['param' => [new TypeRef(Primitive::$STRING)]],
      $this->parse($declaration)
    );
  }

  #[Test]
  public function two_parameters() {
    $this->assertEquals(
      ['param' => [new TypeRef(Primitive::$STRING), new TypeRef(Primitive::$INT)]],
      $this->parse("@param string\n@param int")
    );
  }

  #[Test, Values('types')]
  public function special_types_param($declaration, $type) {
    $this->assertEquals(['param' => [$type]], $this->parse($declaration));
  }

  #[Test]
  public function object_type() {
    $this->assertEquals(
      ['param' => [new TypeRef(property_exists(Type::class, 'OBJECT') ? Type::$OBJECT : Type::$VAR)]],
      $this->parse('@param object')
    );
  }

  #[Test]
  public function iterable_type() {
    $this->assertEquals(
      ['param' => [new TypeRef(property_exists(Type::class, 'ITERABLE') ? Type::$ITERABLE : Type::$VAR)]],
      $this->parse('@param iterable')
    );
  }

  #[Test]
  public function function_in_braces() {
    $this->assertEquals(
      ['param' => [new ArrayTypeRef(new FunctionTypeRef([], new TypeRef(Primitive::$INT)))]],
      $this->parse('@param (function(): int)[]')
    );
  }

  #[Test, Values(['@param string|int', '@param string|int The union', '@param (string|int)', '@param (string|int) The union', '@param string | int', '@param string | int The union'])]
  public function union_type($declaration) {
    $this->assertEquals(
      ['param' => [new TypeUnionRef([new TypeRef(Primitive::$STRING), new TypeRef(Primitive::$INT)])]],
      $this->parse($declaration)
    );
  }

  #[Test, Values(['@return string', '@return string The name'])]
  public function returns($declaration) {
    $this->assertEquals(
      ['return' => [new TypeRef(Primitive::$STRING)]],
      $this->parse($declaration)
    );
  }

  #[Test]
  public function returns_typeparameter() {
    $this->assertEquals(
      ['return' => [new ReferenceTypeRef('T')]],
      $this->parse('@return T')
    );
  }

  #[Test, Values(['@throws lang.IllegalArgumentException', '@throws lang.IllegalArgumentException When the name is incorrect'])]
  public function single_throws($declaration) {
    $this->assertEquals(
      ['throws' => [new ReferenceTypeRef('lang.IllegalArgumentException')]],
      $this->parse($declaration)
    );
  }

  #[Test]
  public function two_throws() {
    $this->assertEquals(
      ['throws' => [new ReferenceTypeRef('lang.IllegalArgumentException'), new ReferenceTypeRef('lang.IllegalAccessException')]],
      $this->parse("@throws lang.IllegalArgumentException\n@throws lang.IllegalAccessException")
    );
  }

  #[Test, Values(['@see http://example.com'])]
  public function single_see_tag($declaration) {
    $this->assertEquals(
      ['see' => [substr($declaration, strlen('@see '))]],
      $this->parse($declaration)
    );
  }

  #[Test, Values(['@type int', '@type int $field'])]
  public function type_tag_is_parsed($declaration) {
    $this->assertEquals(['type' => [new TypeRef(Primitive::$INT)]], $this->parse($declaration));
  }

  #[Test, Values(['@var int', '@var int $field'])]
  public function var_tag_is_parsed($declaration) {
    $this->assertEquals(['var' => [new TypeRef(Primitive::$INT)]], $this->parse($declaration));
  }

  #[Test, Values(['@var int$fixture', '@type int$fixture', '@param int$fixture'])]
  public function missing_whitespace_between_variable_and_type_ok($declaration) {
    $this->assertEquals([new TypeRef(Primitive::$INT)], current($this->parse($declaration)));
  }
}