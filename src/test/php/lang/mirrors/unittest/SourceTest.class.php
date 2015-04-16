<?php namespace lang\mirrors\unittest;

use lang\mirrors\Modifiers;
use lang\mirrors\Kind;

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
  public function has_instance_method() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasMethod('publicInstanceMethod'));
  }

  #[@test]
  public function has_static_method() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasMethod('publicClassMethod'));
  }

  #[@test]
  public function has_instance_field() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasField('publicInstanceField'));
  }

  #[@test]
  public function has_static_field() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasField('publicClassField'));
  }

  #[@test]
  public function has_constant() {
    $this->assertTrue($this->reflect(MemberFixture::class)->hasConstant('CONSTANT'));
  }
}