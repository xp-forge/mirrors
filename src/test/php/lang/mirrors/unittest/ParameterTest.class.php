<?php namespace lang\mirrors\unittest;

use lang\mirrors\unittest\fixture\Identity;
use lang\mirrors\{Annotation, Method, Parameter, TypeMirror};
use lang\{ClassLoader, IllegalArgumentException, IllegalStateException, Primitive, Type, XPClass};
use unittest\{Expect, Test, TestCase, Values};

class ParameterTest extends TestCase {
  use TypeDefinition;

  /**
   * Creates a fixture method
   *
   * @param  string $comment
   * @param  string $signature
   * @return lang.mirrors.Method
   */
  private function method($comment, $signature) {
    return new Method(
      $this->mirror('{'.$comment."\npublic function fixture".$signature.' { } }'),
      'fixture'
    );
  }

  #[Test]
  public function can_create_from_method_and_offset() {
    new Parameter($this->method(null, '($arg)'), 0);
  }

  #[Test]
  public function can_create_from_method_and_parameter() {
    $method= $this->method(null, '($arg)');
    new Parameter($method, new \ReflectionParameter([literal($method->declaredIn()->name()), 'fixture'], 0));
  }

  #[Test, Expect(IllegalArgumentException::class), Values([['()', 0], ['()', 1], ['()', -1], ['($arg)', 1], ['($arg)', -1]])]
  public function raises_exception_when_parameter_does_not_exist($signature, $offset) {
    new Parameter($this->method(null, $signature), $offset);
  }

  #[Test]
  public function name() {
    $this->assertEquals('arg', (new Parameter($this->method(null, '($arg)'), 0))->name());
  }

  #[Test]
  public function position() {
    $this->assertEquals(0, (new Parameter($this->method(null, '($arg)'), 0))->position());
  }

  #[Test]
  public function declaringRoutine() {
    $method= $this->method(null, '($arg)');
    $this->assertEquals($method, (new Parameter($method, 0))->declaringRoutine());
  }

  #[Test, Values([['($arg= null)', true], ['($arg)', false]])]
  public function isOptional($signature, $result) {
    $this->assertEquals($result, (new Parameter($this->method(null, $signature), 0))->isOptional());
  }

  #[Test, Values([['($arg)', false]])]
  public function isVariadic($signature, $result) {
    $this->assertEquals($result, (new Parameter($this->method(null, $signature), 0))->isVariadic());
  }

  #[Test]
  public function variadic_via_syntax_with_type() {
    $param= new Parameter($this->method(null, '(string... $args)'), 0);
    $this->assertEquals(
      ['variadic' => true, 'optional' => true, 'type' => Primitive::$STRING],
      ['variadic' => $param->isVariadic(), 'optional' => $param->isOptional(), 'type' => $param->type()]
    );
  }

  #[Test]
  public function variadic_via_syntax() {
    $param= new Parameter($this->method(null, '(... $args)'), 0);
    $this->assertEquals(
      ['variadic' => true, 'optional' => true, 'type' => Type::$VAR],
      ['variadic' => $param->isVariadic(), 'optional' => $param->isOptional(), 'type' => $param->type()]
    );
  }

  #[Test, Values(['/** @param var* $args */', '/** @param var... $args */'])]
  public function variadic_via_apidoc($signature) {
    $param= new Parameter($this->method($signature, '($args= null)'), 0);
    $this->assertEquals(
      ['variadic' => true, 'optional' => true, 'type' => Type::$VAR],
      ['variadic' => $param->isVariadic(), 'optional' => $param->isOptional(), 'type' => $param->type()]
    );
  }

  #[Test]
  public function var_is_default_for_no_type_hint() {
    $this->assertEquals(Type::$VAR, (new Parameter($this->method(null, '($arg)'), 0))->type());
  }

  #[Test]
  public function type_hint() {
    $this->assertEquals(new XPClass(Type::class), (new Parameter($this->method(null, '(\lang\Type $arg)'), 0))->type());
  }

  #[Test]
  public function self_type_hint() {
    $method= $this->method(null, '(self $arg)');
    $this->assertEquals(new XPClass($method->declaredIn()->name()), (new Parameter($method, 0))->type());
  }

  #[Test]
  public function array_type_hint() {
    $this->assertEquals(Type::$ARRAY, (new Parameter($this->method(null, '(array $arg)'), 0))->type());
  }

  #[Test]
  public function callable_type_hint() {
    $this->assertEquals(Type::$CALLABLE, (new Parameter($this->method(null, '(callable $arg)'), 0))->type());
  }

  #[Test, Values(['/** @param lang.Type */', '/** @param \lang\Type */'])]
  public function documented_type_hint_using_short_form($comment) {
    $this->assertEquals(new XPClass(Type::class), (new Parameter($this->method($comment, '($arg)'), 0))->type());
  }

  #[Test, Values(["/**\n * @param lang.Type\n * @param string\n */", "/**\n * @param lang.Type \$one\n * @param string \$two\n */"])]
  public function first_parameter_in_documented_type_hint_using_long_form($comment) {
    $this->assertEquals(new XPClass(Type::class), (new Parameter($this->method($comment, '($one, $two)'), 0))->type());
  }

  #[Test, Values(["/**\n * @param lang.Type\n * @param string\n */", "/**\n * @param lang.Type \$one\n * @param string \$two\n */"])]
  public function second_parameter_in_documented_type_hint_using_long_form($comment) {
    $this->assertEquals(Primitive::$STRING, (new Parameter($this->method($comment, '($one, $two)'), 1))->type());
  }

  #[Test, Expect(IllegalStateException::class)]
  public function cannot_get_default_value_for_non_optional() {
    (new Parameter($this->method(null, '($arg)'), 0))->defaultValue();
  }

  #[Test]
  public function null_default_value_for_optional() {
    $this->assertEquals(null, (new Parameter($this->method(null, '($arg= null)'), 0))->defaultValue());
  }

  #[Test, Values(['($arg= \lang\mirrors\unittest\fixture\Identity::NAME)', '($arg= Identity::NAME)'])]
  public function constant_default_value_for_optional($signature) {
    $this->assertEquals(Identity::NAME, (new Parameter($this->method(null, $signature), 0))->defaultValue());
  }

  #[Test]
  public function array_default_value_for_optional() {
    $this->assertEquals([1, 2, 3], (new Parameter($this->method(null, '($arg= [1, 2, 3])'), 0))->defaultValue());
  }

  #[Test]
  public function no_annotations() {
    $this->assertFalse((new Parameter($this->method(null, '($arg)'), 0))->annotations()->present());
  }

  #[Test]
  public function annotated_parameter() {
    $method= $this->method('#[@$arg: test]', '($arg)');
    $this->assertEquals(
      [new Annotation($method->declaredIn(), 'test', null)],
      iterator_to_array((new Parameter($method, 0))->annotations())
    );
  }

  #[Test, Values([['($arg)', false], ['(\lang\Type $arg)', true], ['(self $arg)', true], ['(array $arg)', true], ['(callable $arg)', true]])]
  public function isVerified($signature, $result) {
    $this->assertEquals($result, (new Parameter($this->method(null, $signature), 0))->isVerified());
  }
}