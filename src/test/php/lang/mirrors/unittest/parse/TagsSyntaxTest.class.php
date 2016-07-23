<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\TagsSyntax;
use lang\mirrors\parse\TagsSource;
use lang\mirrors\parse\TypeRef;
use lang\mirrors\parse\ArrayTypeRef;
use lang\mirrors\parse\MapTypeRef;
use lang\mirrors\parse\FunctionTypeRef;
use lang\mirrors\parse\GenericTypeRef;
use lang\mirrors\parse\ReferenceTypeRef;
use lang\mirrors\parse\TypeUnionRef;
use lang\Type;
use lang\Primitive;

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

  #[@test, @values([
  #  '@param string',
  #  '@param string $input',
  #  '@param string $input The input parameter'
  #])]
  public function single_parameter($declaration) {
    $this->assertEquals(
      ['param' => [new TypeRef(Primitive::$STRING)]],
      $this->parse($declaration)
    );
  }

  #[@test]
  public function two_parameters() {
    $this->assertEquals(
      ['param' => [new TypeRef(Primitive::$STRING), new TypeRef(Primitive::$INT)]],
      $this->parse("@param string\n@param int")
    );
  }

  #[@test, @values([
  #  ['@param callable', new TypeRef(Type::$CALLABLE)],
  #  ['@param array', new TypeRef(Type::$ARRAY)],
  #  ['@param void', new TypeRef(Type::$VOID)],
  #  ['@param var', new TypeRef(Type::$VAR)]
  #])]
  public function special_types_param($declaration, $type) {
    $this->assertEquals(['param' => [$type]], $this->parse($declaration));
  }

  #[@test, @values([
  #  ['@param self', new ReferenceTypeRef('self')],
  #  ['@param parent', new ReferenceTypeRef('parent')],
  #  ['@param static', new ReferenceTypeRef('static')]
  #])]
  public function special_class_parameter($declaration, $type) {
    $this->assertEquals(['param' => [$type]], $this->parse($declaration));
  }

  #[@test, @values([
  #  ['@param var[]', new ArrayTypeRef(new TypeRef(Type::$VAR))],
  #  ['@param string[][]', new ArrayTypeRef(new ArrayTypeRef(new TypeRef(Primitive::$STRING)))]
  #])]
  public function array_parameter($declaration, $type) {
    $this->assertEquals(['param' => [$type]], $this->parse($declaration));
  }

  #[@test, @values([
  #  ['@param [:var]', new MapTypeRef(new TypeRef(Type::$VAR))],
  #  ['@param [:[:string]]', new MapTypeRef(new MapTypeRef(new TypeRef(Primitive::$STRING)))]
  #])]
  public function map_parameter($declaration, $type) {
    $this->assertEquals(['param' => [$type]], $this->parse($declaration));
  }

  #[@test, @values([
  #  ['@param function(): var', new FunctionTypeRef([], new TypeRef(Type::$VAR))],
  #  ['@param function(string): var', new FunctionTypeRef([new TypeRef(Primitive::$STRING)], new TypeRef(Type::$VAR))],
  #  ['@param function(string, int): void', new FunctionTypeRef([new TypeRef(Primitive::$STRING), new TypeRef(Primitive::$INT)], new TypeRef(Type::$VOID))],
  #])]
  public function function_type($declaration, $type) {
    $this->assertEquals(['param' => [$type]], $this->parse($declaration));
  }

  #[@test]
  public function function_in_braces() {
    $this->assertEquals(
      ['param' => [new ArrayTypeRef(new FunctionTypeRef([], new TypeRef(Primitive::$INT)))]],
      $this->parse('@param (function(): int)[]')
    );
  }

  #[@test, @values([
  #  ['@param util.collections.List<int>', new GenericTypeRef(new ReferenceTypeRef('util.collections.List'), [new TypeRef(Primitive::$INT)])],
  #  ['@param util.collections.Map<string, int>', new GenericTypeRef(new ReferenceTypeRef('util.collections.Map'), [new TypeRef(Primitive::$STRING), new TypeRef(Primitive::$INT)])]
  #])]
  public function generic_type($declaration, $type) {
    $this->assertEquals(['param' => [$type]], $this->parse($declaration));
  }

  #[@test, @values([
  #  ['@param [:int[]]', new MapTypeRef(new ArrayTypeRef(new TypeRef(Primitive::$INT)))],
  #  ['@param [:[:string]]', new MapTypeRef(new MapTypeRef(new TypeRef(Primitive::$STRING)))],
  #  ['@param util.collections.Map<string, int>[]', new ArrayTypeRef(new GenericTypeRef(new ReferenceTypeRef('util.collections.Map'), [new TypeRef(Primitive::$STRING), new TypeRef(Primitive::$INT)]))],
  #  ['@param [:util.collections.Map<string, int>]', new MapTypeRef(new GenericTypeRef(new ReferenceTypeRef('util.collections.Map'), [new TypeRef(Primitive::$STRING), new TypeRef(Primitive::$INT)]))],
  #  ['@param function(string[]): var', new FunctionTypeRef([new ArrayTypeRef(new TypeRef(Primitive::$STRING))], new TypeRef(Type::$VAR))],
  #  ['@param function(string[]): var[]', new FunctionTypeRef([new ArrayTypeRef(new TypeRef(Primitive::$STRING))], new ArrayTypeRef(new TypeRef(Type::$VAR)))],
  #  ['@param function([:string]): void', new FunctionTypeRef([new MapTypeRef(new TypeRef(Primitive::$STRING))], new TypeRef(Type::$VOID))]
  #])]
  public function nested_type_parameters($declaration, $type) {
    $this->assertEquals(['param' => [$type]], $this->parse($declaration));
  }

  #[@test, @values([
  #  '@param string|int',
  #  '@param string|int The union',
  #  '@param (string|int)',
  #  '@param (string|int) The union',
  #  '@param string | int',
  #  '@param string | int The union'
  #])]
  public function union_type($declaration) {
    $this->assertEquals(
      ['param' => [new TypeUnionRef([new TypeRef(Primitive::$STRING), new TypeRef(Primitive::$INT)])]],
      $this->parse($declaration)
    );
  }

  #[@test, @values([
  #  '@return string',
  #  '@return string The name'
  #])]
  public function returns($declaration) {
    $this->assertEquals(
      ['return' => [new TypeRef(Primitive::$STRING)]],
      $this->parse($declaration)
    );
  }

  #[@test, @values([
  #  '@throws lang.IllegalArgumentException',
  #  '@throws lang.IllegalArgumentException When the name is incorrect'
  #])]
  public function single_throws($declaration) {
    $this->assertEquals(
      ['throws' => [new ReferenceTypeRef('lang.IllegalArgumentException')]],
      $this->parse($declaration)
    );
  }

  #[@test]
  public function two_throws() {
    $this->assertEquals(
      ['throws' => [new ReferenceTypeRef('lang.IllegalArgumentException'), new ReferenceTypeRef('lang.IllegalAccessException')]],
      $this->parse("@throws lang.IllegalArgumentException\n@throws lang.IllegalAccessException")
    );
  }

  #[@test, @values([
  #  '@see http://example.com'
  #])]
  public function single_see_tag($declaration) {
    $this->assertEquals(
      ['see' => [substr($declaration, strlen('@see '))]],
      $this->parse($declaration)
    );
  }

  #[@test, @values([
  #  '@type int',
  #  '@type int $field'
  #])]
  public function type_tag_is_parsed($declaration) {
    $this->assertEquals(['type' => [new TypeRef(Primitive::$INT)]], $this->parse($declaration));
  }

  #[@test, @values([
  #  '@var int',
  #  '@var int $field'
  #])]
  public function var_tag_is_parsed($declaration) {
    $this->assertEquals(['var' => [new TypeRef(Primitive::$INT)]], $this->parse($declaration));
  }

  #[@test, @values([
  #  '@var int$fixture',
  #  '@type int$fixture',
  #  '@param int$fixture'
  #])]
  public function missing_whitespace_between_variable_and_type_ok($declaration) {
    $this->assertEquals([new TypeRef(Primitive::$INT)], current($this->parse($declaration)));
  }

  #[@test, @values([
  #  ['@param resource', new TypeRef(Type::$VAR)],
  #  ['@param object', new TypeRef(Type::$VAR)],
  #  ['@param mixed', new TypeRef(Type::$VAR)],
  #  ['@param null', new TypeRef(Type::$VOID)],
  #  ['@param false', new TypeRef(Primitive::$BOOL)],
  #  ['@param true', new TypeRef(Primitive::$BOOL)],
  #  ['@param $this', new ReferenceTypeRef('self')]
  #])]
  public function foreign_types_param($declaration, $type) {
    $this->assertEquals(['param' => [$type]], $this->parse($declaration));
  }

  #[@test, @values([
  #  ['@param boolean', new TypeRef(Primitive::$BOOL)],
  #  ['@param integer', new TypeRef(Primitive::$INT)],
  #  ['@param float', new TypeRef(Primitive::$DOUBLE)]
  #])]
  public function foreign_type_aliases($declaration, $type) {
    $this->assertEquals(['param' => [$type]], $this->parse($declaration));
  }

  #[@test, @values([
  #  ['@param \Iterator', new ReferenceTypeRef('\Iterator')],
  #  ['@param \stubbles\lang\Sequence', new ReferenceTypeRef('\stubbles\lang\Sequence')],
  #  ['@param \DateTime[]', new ArrayTypeRef(new ReferenceTypeRef('\DateTime'))],
  #  ['@param \ArrayObject|\DateTime[]', new TypeUnionRef([
  #    new ReferenceTypeRef('\ArrayObject'),
  #    new ArrayTypeRef(new ReferenceTypeRef('\DateTime'))
  #  ])]
  #])]
  public function foreign_fully_qualified($declaration, $type) {
    $this->assertEquals(['param' => [$type]], $this->parse($declaration));
  }
}