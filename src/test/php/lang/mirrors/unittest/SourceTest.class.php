<?php namespace lang\mirrors\unittest;

use lang\mirrors\unittest\fixture\{AbstractMemberFixture, FixtureAbstract, FixtureBase, FixtureCloseable, FixtureEnum, FixtureFinal, FixtureImpl, FixtureInterface, FixtureParams, FixtureTrait, FixtureUsed, FixtureUses, MemberFixture};
use lang\mirrors\{Kind, Modifiers};
use lang\{Closeable, ElementNotFoundException, Type, XPClass};
use unittest\{Expect, Fixture, Test};

/**
 * Base class for source implementation testing
 */
#[Fixture]
abstract class SourceTest extends \unittest\TestCase {

  #[Fixture]
  private $field;

  #[Fixture]
  private function method() { }

  /**
   * Returns the keys of an iterator on a map sorted alphabetically
   *
   * @param  php.Traversable $iterator
   * @return lang.mirrors.Field[]
   */
  private function sorted($iterator) {
    $keys= array_keys(iterator_to_array($iterator));
    sort($keys);
    return $keys;
  }

  /**
   * Creates a new reflection source
   *
   * @param  string $name
   * @return lang.mirrors.Source
   */
  protected abstract function reflect($name);

  #[Test]
  public function typeName() {
    $this->assertEquals('lang.mirrors.unittest.SourceTest', $this->reflect(self::class)->typeName());
  }

  #[Test]
  public function typeDeclaration() {
    $this->assertEquals('SourceTest', $this->reflect(self::class)->typeDeclaration());
  }

  #[Test]
  public function typeInstance() {
    $this->assertEquals(new XPClass(self::class), $this->reflect(self::class)->typeInstance());
  }

  #[Test]
  public function typeComment() {
    $this->assertEquals(
      "/**\n * Base class for source implementation testing\n */",
      $this->reflect(self::class)->typeComment()
    );
  }

  #[Test]
  public function typeComment_for_undocumented_class() {
    $this->assertNull($this->reflect(FixtureTrait::class)->typeComment());
  }

  #[Test]
  public function packageName() {
    $this->assertEquals('lang.mirrors.unittest', $this->reflect(self::class)->packageName());
  }

  #[Test]
  public function typeParent_of_this_class() {
    $this->assertEquals($this->reflect(parent::class), $this->reflect(self::class)->typeParent());
  }

  #[Test]
  public function typeParent_of_parentless_class() {
    $this->assertNull($this->reflect(AbstractMemberFixture::class)->typeParent());
  }

  #[Test]
  public function typeAnnotations_of_this_class() {
    $this->assertEquals(['fixture' => null], $this->reflect(self::class)->typeAnnotations());
  }

  #[Test]
  public function typeAnnotations_of_annotationless_class() {
    $this->assertNull($this->reflect(AbstractMemberFixture::class)->typeAnnotations());
  }

  #[Test]
  public function typeModifiers() {
    $this->assertEquals(new Modifiers('public abstract'), $this->reflect(self::class)->typeModifiers());
  }

  #[Test]
  public function typeModifiers_of_trait() {
    $this->assertEquals(new Modifiers('public abstract'), $this->reflect(FixtureTrait::class)->typeModifiers());
  }

  #[Test]
  public function typeModifiers_of_abstract() {
    $this->assertEquals(new Modifiers('public abstract'), $this->reflect(FixtureAbstract::class)->typeModifiers());
  }

  #[Test]
  public function typeModifiers_of_final() {
    $this->assertEquals(new Modifiers('public final'), $this->reflect(FixtureFinal::class)->typeModifiers());
  }

  #[Test]
  public function typeModifiers_of_enum() {
    $this->assertEquals(new Modifiers('public'), $this->reflect(FixtureEnum::class)->typeModifiers());
  }

  #[Test]
  public function typeModifiers_of_interface() {
    $this->assertEquals(new Modifiers('public'), $this->reflect(FixtureInterface::class)->typeModifiers());
  }

  #[Test]
  public function typeKind() {
    $this->assertEquals(Kind::$CLASS, $this->reflect(self::class)->typeKind());
  }

  #[Test]
  public function typeKind_of_trait() {
    $this->assertEquals(Kind::$TRAIT, $this->reflect(FixtureTrait::class)->typeKind());
  }

  #[Test]
  public function typeKind_of_enum() {
    $this->assertEquals(Kind::$ENUM, $this->reflect(FixtureEnum::class)->typeKind());
  }

  #[Test]
  public function typeKind_of_interface() {
    $this->assertEquals(Kind::$INTERFACE, $this->reflect(FixtureInterface::class)->typeKind());
  }

  #[Test]
  public function typeImplements_declared_interface() {
    $this->assertTrue($this->reflect(FixtureImpl::class)->typeImplements(\IteratorAggregate::class));
  }

  #[Test]
  public function typeImplements_inherited_interface() {
    $this->assertTrue($this->reflect(FixtureImpl::class)->typeImplements(\Traversable::class));
  }

  #[Test]
  public function all_interfaces() {
    $this->assertEquals(
      [\IteratorAggregate::class, \Traversable::class, Closeable::class, FixtureInterface::class],
      $this->sorted($this->reflect(FixtureImpl::class)->allInterfaces())
    );
  }

  #[Test]
  public function declared_interfaces() {
    $this->assertEquals(
      [\IteratorAggregate::class, Closeable::class],
      $this->sorted($this->reflect(FixtureImpl::class)->declaredInterfaces())
    );
  }

  #[Test]
  public function parent_interfaces() {
    $this->assertEquals(
      [Closeable::class, FixtureInterface::class],
      $this->sorted($this->reflect(FixtureCloseable::class)->allInterfaces())
    );
  }

  #[Test]
  public function typeUses() {
    $this->assertTrue($this->reflect(FixtureImpl::class)->typeUses(FixtureTrait::class));
  }

  #[Test]
  public function all_traits() {
    $this->assertEquals(
      [FixtureTrait::class, FixtureUsed::class],
      $this->sorted($this->reflect(FixtureUses::class)->allTraits())
    );
  }

  #[Test]
  public function declared_traits() {
    $this->assertEquals(
      [FixtureUsed::class],
      $this->sorted($this->reflect(FixtureUses::class)->declaredTraits())
    );
  }

  #[Test]
  public function with_constructor() {
    $this->assertEquals('__construct', $this->reflect(self::class)->constructor()['name']);
  }

  #[Test]
  public function default_constructor() {
    $this->assertEquals('__default', $this->reflect(MemberFixture::class)->constructor()['name']);
  }

  #[Test]
  public function has_instance_field() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasField('publicInstanceField'));
  }

  #[Test]
  public function has_inherited_field() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasField('inheritedField'));
  }

  #[Test]
  public function has_trait_field() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasField('traitField'));
  }

  #[Test]
  public function has_static_field() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasField('publicClassField'));
  }

  #[Test]
  public function all_fields() {
    $this->assertEquals(
      [
        'annotatedClassField',
        'annotatedInstanceField',
        'annotatedTraitField',
        'inheritedField',
        'privateClassField',
        'privateInstanceField',
        'protectedClassField',
        'protectedInstanceField',
        'publicClassField',
        'publicInstanceField',
        'traitField'
      ],
      $this->sorted($this->reflect(MemberFixture::class)->allFields())
    );
  }

  #[Test]
  public function declared_fields() {
    $this->assertEquals(
      [
        'annotatedClassField',
        'annotatedInstanceField',
        'annotatedTraitField',
        'privateClassField',
        'privateInstanceField',
        'protectedClassField',
        'protectedInstanceField',
        'publicClassField',
        'publicInstanceField',
        'traitField'
      ],
      $this->sorted($this->reflect(MemberFixture::class)->declaredFields())
    );
  }

  #[Test]
  public function trait_fields() {
    $this->assertEquals(
      ['annotatedTraitField', 'traitField'],
      $this->sorted($this->reflect(FixtureTrait::class)->allFields())
    );
  }

  #[Test]
  public function instance_field() {
    $this->assertEquals(
      'publicInstanceField',
      $this->reflect(MemberFixture::class)->fieldNamed('publicInstanceField')['name']
    );
  }

  #[Test]
  public function static_field() {
    $this->assertEquals(
      'publicClassField',
      $this->reflect(MemberFixture::class)->fieldNamed('publicClassField')['name']
    );
  }

  #[Test]
  public function inherited_field() {
    $this->assertEquals(
      'inheritedField',
      $this->reflect(MemberFixture::class)->fieldNamed('inheritedField')['name']
    );
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function non_existant_field() {
    $this->reflect(MemberFixture::class)->fieldNamed('does.not.exist');
  }

  #[Test]
  public function fieldAnnotations_of_field_in_this_class() {
    $field= $this->reflect(self::class)->fieldNamed('field');
    $this->assertEquals(['fixture' => null], $field['annotations']());
  }

  #[Test]
  public function has_instance_method() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasMethod('publicInstanceMethod'));
  }

  #[Test]
  public function has_static_method() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasMethod('publicClassMethod'));
  }

  #[Test]
  public function all_methods() {
    $this->assertEquals(
      [
        'annotatedClassMethod',
        'annotatedInstanceMethod',
        'annotatedTraitMethod',
        'inheritedMethod',
        'privateClassMethod',
        'privateInstanceMethod',
        'protectedClassMethod',
        'protectedInstanceMethod',
        'publicClassMethod',
        'publicInstanceMethod',
        'traitMethod'
      ],
      $this->sorted($this->reflect(MemberFixture::class)->allMethods())
    );
  }

  #[Test]
  public function declared_methods() {
    $this->assertEquals(
      [
        'annotatedClassMethod',
        'annotatedInstanceMethod',
        'annotatedTraitMethod',
        'privateClassMethod',
        'privateInstanceMethod',
        'protectedClassMethod',
        'protectedInstanceMethod',
        'publicClassMethod',
        'publicInstanceMethod',
        'traitMethod'
      ],
      $this->sorted($this->reflect(MemberFixture::class)->declaredMethods())
    );
  }

  #[Test]
  public function trait_methods() {
    $this->assertEquals(
      ['annotatedTraitMethod', 'traitMethod'],
      $this->sorted($this->reflect(FixtureTrait::class)->allMethods())
    );
  }

  #[Test]
  public function instance_method() {
    $this->assertEquals(
      'publicInstanceMethod',
      $this->reflect(MemberFixture::class)->methodNamed('publicInstanceMethod')['name']
    );
  }

  #[Test]
  public function static_method() {
    $this->assertEquals(
      'publicClassMethod',
      $this->reflect(MemberFixture::class)->methodNamed('publicClassMethod')['name']
    );
  }

  #[Test]
  public function inherited_method() {
    $this->assertEquals(
      'inheritedMethod',
      $this->reflect(MemberFixture::class)->methodNamed('inheritedMethod')['name']
    );
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function non_existant_method() {
    $this->reflect(MemberFixture::class)->methodNamed('does.not.exist');
  }

  #[Test]
  public function methodAnnotations_of_field_in_this_class() {
    $method= $this->reflect(self::class)->methodNamed('method');
    $this->assertEquals(['fixture' => null], $method['annotations']());
  }

  #[Test]
  public function no_params() {
    $method= $this->reflect(FixtureParams::class)->methodNamed('noParam');
    $this->assertEquals([], $method['params']());
  }

  #[Test]
  public function one_param() {
    $method= $this->reflect(FixtureParams::class)->methodNamed('oneParam');
    $param= $method['params']()[0];
    $this->assertEquals(
      [0, 'arg', null, false, false, null],
      [$param['pos'], $param['name'], $param['type'], $param['ref'], $param['var'], $param['default']]
    );
  }

  #[Test]
  public function one_optional_param() {
    $method= $this->reflect(FixtureParams::class)->methodNamed('oneOptionalParam');
    $param= $method['params']()[0];
    $this->assertEquals(
      [0, 'arg', null, false, null, null],
      [$param['pos'], $param['name'], $param['type'], $param['ref'], $param['var'], $param['default']()]
    );
  }

  #[Test]
  public function typed_param() {
    $method= $this->reflect(FixtureParams::class)->methodNamed('oneTypeHintedParam');
    $param= $method['params']()[0];
    $this->assertEquals(new XPClass(Type::class), $param['type']());
  }

  #[Test]
  public function self_typehinted_param() {
    $method= $this->reflect(FixtureParams::class)->methodNamed('oneSelfTypeHintedParam');
    $param= $method['params']()[0];
    $this->assertEquals(new XPClass(FixtureParams::class), $param['type']());
  }

  #[Test]
  public function array_typehinted_param() {
    $method= $this->reflect(FixtureParams::class)->methodNamed('oneArrayTypeHintedParam');
    $param= $method['params']()[0];
    $this->assertEquals(Type::$ARRAY, $param['type']());
  }

  #[Test]
  public function callable_typehinted_param() {
    $method= $this->reflect(FixtureParams::class)->methodNamed('oneCallableTypeHintedParam');
    $param= $method['params']()[0];
    $this->assertEquals(Type::$CALLABLE, $param['type']());
  }

  #[Test]
  public function has_constant() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasConstant('CONSTANT'));
  }

  #[Test]
  public function all_constants() {
    $this->assertEquals(
      ['CONSTANT' => MemberFixture::CONSTANT, 'INHERITED' => MemberFixture::INHERITED],
      iterator_to_array($this->reflect(MemberFixture::class)->allConstants())
    );
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function non_existant_constant() {
    $this->reflect(MemberFixture::class)->constantNamed('does.not.exist');
  }

  #[Test]
  public function constant_named() {
    $this->assertEquals(
      MemberFixture::CONSTANT,
      $this->reflect(MemberFixture::class)->constantNamed('CONSTANT')
    );
  }

  #[Test]
  public function isSubtypeOf_returns_false_for_self() {
    $this->assertFalse($this->reflect(FixtureImpl::class)->isSubtypeOf(FixtureImpl::class));
  }

  #[Test]
  public function isSubtypeOf_parent() {
    $this->assertTrue($this->reflect(FixtureImpl::class)->isSubtypeOf(FixtureBase::class));
  }

  #[Test]
  public function isSubtypeOf_implemented_interface() {
    $this->assertTrue($this->reflect(FixtureImpl::class)->isSubtypeOf(FixtureInterface::class));
  }

  #[Test]
  public function trait_field_comment() {
    $field= $this->reflect(MemberFixture::class)->fieldNamed('traitField');
    $this->assertEquals('/** @type int */', $field['comment']());
  }

  #[Test]
  public function trait_field_annotations() {
    $field= $this->reflect(MemberFixture::class)->fieldNamed('annotatedTraitField');
    $this->assertEquals(['fixture' => null], $field['annotations']());
  }

  #[Test]
  public function trait_method_comment() {
    $field= $this->reflect(MemberFixture::class)->methodNamed('traitMethod');
    $this->assertEquals('/** @return void */', $field['comment']());
  }

  #[Test]
  public function trait_method_annotations() {
    $field= $this->reflect(MemberFixture::class)->methodNamed('annotatedTraitMethod');
    $this->assertEquals(['fixture' => null], $field['annotations']());
  }
}