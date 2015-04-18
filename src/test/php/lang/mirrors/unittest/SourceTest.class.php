<?php namespace lang\mirrors\unittest;

use lang\mirrors\Modifiers;
use lang\mirrors\Kind;
use lang\Runnable;

/**
 * Base class for source implementation testing
 */
#[@fixture]
abstract class SourceTest extends \unittest\TestCase {

  /**
   * Creates a new reflection source
   *
   * @param  string $name
   * @return lang.mirrors.Source
   */
  protected abstract function reflect($name);

  #[@test]
  public function typeName() {
    $this->assertEquals('lang.mirrors.unittest.SourceTest', $this->reflect(self::class)->typeName());
  }

  #[@test]
  public function typeDeclaration() {
    $this->assertEquals('SourceTest', $this->reflect(self::class)->typeDeclaration());
  }

  #[@test]
  public function typeComment() {
    $this->assertEquals(
      "/**\n * Base class for source implementation testing\n */",
      $this->reflect(self::class)->typeComment()
    );
  }

  #[@test]
  public function typeComment_for_undocumented_class() {
    $this->assertNull($this->reflect(FixtureTrait::class)->typeComment());
  }

  #[@test]
  public function packageName() {
    $this->assertEquals('lang.mirrors.unittest', $this->reflect(self::class)->packageName());
  }

  #[@test]
  public function typeParent_of_this_class() {
    $this->assertEquals($this->reflect(parent::class), $this->reflect(self::class)->typeParent());
  }

  #[@test]
  public function typeParent_of_parentless_class() {
    $this->assertNull($this->reflect(AbstractMemberFixture::class)->typeParent());
  }

  #[@test]
  public function typeAnnotations_of_this_class() {
    $this->assertEquals([null => ['fixture' => null]], $this->reflect(self::class)->typeAnnotations());
  }

  #[@test]
  public function typeAnnotations_of_annotationless_class() {
    $this->assertNull($this->reflect(AbstractMemberFixture::class)->typeAnnotations());
  }

  #[@test]
  public function typeModifiers() {
    $this->assertEquals(new Modifiers('public abstract'), $this->reflect(self::class)->typeModifiers());
  }

  #[@test]
  public function typeModifiers_of_trait() {
    $this->assertEquals(new Modifiers('public abstract'), $this->reflect(FixtureTrait::class)->typeModifiers());
  }

  #[@test]
  public function typeModifiers_of_abstract() {
    $this->assertEquals(new Modifiers('public abstract'), $this->reflect(FixtureAbstract::class)->typeModifiers());
  }

  #[@test]
  public function typeModifiers_of_final() {
    $this->assertEquals(new Modifiers('public final'), $this->reflect(FixtureFinal::class)->typeModifiers());
  }

  #[@test]
  public function typeModifiers_of_enum() {
    $this->assertEquals(new Modifiers('public'), $this->reflect(FixtureEnum::class)->typeModifiers());
  }

  #[@test]
  public function typeModifiers_of_interface() {
    $this->assertEquals(new Modifiers('public'), $this->reflect(FixtureInterface::class)->typeModifiers());
  }

  #[@test]
  public function typeKind() {
    $this->assertEquals(Kind::$CLASS, $this->reflect(self::class)->typeKind());
  }

  #[@test]
  public function typeKind_of_trait() {
    $this->assertEquals(Kind::$TRAIT, $this->reflect(FixtureTrait::class)->typeKind());
  }

  #[@test]
  public function typeKind_of_enum() {
    $this->assertEquals(Kind::$ENUM, $this->reflect(FixtureEnum::class)->typeKind());
  }

  #[@test]
  public function typeKind_of_interface() {
    $this->assertEquals(Kind::$INTERFACE, $this->reflect(FixtureInterface::class)->typeKind());
  }

  #[@test]
  public function typeImplements() {
    $this->assertTrue($this->reflect(FixtureImpl::class)->typeImplements(FixtureInterface::class));
  }

  #[@test]
  public function all_interfaces() {
    $names= array_keys(iterator_to_array($this->reflect(FixtureImpl::class)->allInterfaces()));
    sort($names);
    $this->assertEquals([Runnable::class, FixtureInterface::class], $names);
  }

  #[@test]
  public function declared_interfaces() {
    $names= array_keys(iterator_to_array($this->reflect(FixtureImpl::class)->declaredInterfaces()));
    sort($names);
    $this->assertEquals([Runnable::class], $names);
  }

  #[@test]
  public function typeUses() {
    $this->assertTrue($this->reflect(FixtureImpl::class)->typeUses(FixtureTrait::class));
  }

  #[@test]
  public function all_traits() {
    $names= array_keys(iterator_to_array($this->reflect(FixtureUses::class)->allTraits()));
    sort($names);
    $this->assertEquals([FixtureTrait::class, FixtureUsed::class], $names);
  }

  #[@test]
  public function declared_traits() {
    $names= array_keys(iterator_to_array($this->reflect(FixtureUses::class)->declaredTraits()));
    sort($names);
    $this->assertEquals([FixtureUsed::class], $names);
  }

  #[@test]
  public function with_constructor() {
    $this->assertEquals('__construct', $this->reflect(self::class)->constructor()['name']);
  }

  #[@test]
  public function default_constructor() {
    $this->assertEquals('__default', $this->reflect(MemberFixture::class)->constructor()['name']);
  }

  #[@test]
  public function has_instance_field() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasField('publicInstanceField'));
  }

  #[@test]
  public function has_inherited_field() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasField('inheritedField'));
  }

  #[@test]
  public function has_static_field() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasField('publicClassField'));
  }

  #[@test]
  public function has_constant() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasConstant('CONSTANT'));
  }

  #[@test]
  public function all_fields() {
    $this->assertEquals(
      [
        'publicInstanceField',
        'protectedInstanceField',
        'privateInstanceField',
        'publicClassField',
        'protectedClassField',
        'privateClassField',
        'inheritedField'
      ],
      array_keys(iterator_to_array($this->reflect(MemberFixture::class)->allFields()))
    );
  }

  #[@test]
  public function declared_fields() {
    $this->assertEquals(
      [
        'publicInstanceField',
        'protectedInstanceField',
        'privateInstanceField',
        'publicClassField',
        'protectedClassField',
        'privateClassField'
      ],
      array_keys(iterator_to_array($this->reflect(MemberFixture::class)->declaredFields()))
    );
  }

  #[@test]
  public function instance_field() {
    $this->assertEquals(
      'publicInstanceField',
      $this->reflect(MemberFixture::class)->fieldNamed('publicInstanceField')['name']
    );
  }

  #[@test]
  public function static_field() {
    $this->assertEquals(
      'publicClassField',
      $this->reflect(MemberFixture::class)->fieldNamed('publicClassField')['name']
    );
  }

  #[@test]
  public function inherited_field() {
    $this->assertEquals(
      'inheritedField',
      $this->reflect(MemberFixture::class)->fieldNamed('inheritedField')['name']
    );
  }

  #[@test]
  public function has_instance_method() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasMethod('publicInstanceMethod'));
  }

  #[@test]
  public function has_static_method() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasMethod('publicClassMethod'));
  }

  #[@test]
  public function all_methods() {
    $this->assertEquals(
      [
        'publicInstanceMethod',
        'protectedInstanceMethod',
        'privateInstanceMethod',
        'publicClassMethod',
        'protectedClassMethod',
        'privateClassMethod',
        'inheritedMethod'
      ],
      array_keys(iterator_to_array($this->reflect(MemberFixture::class)->allMethods()))
    );
  }

  #[@test]
  public function declared_methods() {
    $this->assertEquals(
      [
        'publicInstanceMethod',
        'protectedInstanceMethod',
        'privateInstanceMethod',
        'publicClassMethod',
        'protectedClassMethod',
        'privateClassMethod'
      ],
      array_keys(iterator_to_array($this->reflect(MemberFixture::class)->declaredMethods()))
    );
  }

  #[@test]
  public function instance_method() {
    $this->assertEquals(
      'publicInstanceMethod',
      $this->reflect(MemberFixture::class)->methodNamed('publicInstanceMethod')['name']
    );
  }

  #[@test]
  public function static_method() {
    $this->assertEquals(
      'publicClassMethod',
      $this->reflect(MemberFixture::class)->methodNamed('publicClassMethod')['name']
    );
  }

  #[@test]
  public function inherited_method() {
    $this->assertEquals(
      'inheritedMethod',
      $this->reflect(MemberFixture::class)->methodNamed('inheritedMethod')['name']
    );
  }
}