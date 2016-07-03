<?php namespace lang\mirrors\unittest;

use lang\mirrors\Modifiers;
use lang\mirrors\Kind;
use lang\Closeable;
use lang\XPClass;
use lang\Type;
use lang\ElementNotFoundException;
use lang\mirrors\unittest\fixture\AbstractMemberFixture;
use lang\mirrors\unittest\fixture\MemberFixture;
use lang\mirrors\unittest\fixture\FixtureAbstract;
use lang\mirrors\unittest\fixture\FixtureBase;
use lang\mirrors\unittest\fixture\FixtureCloseable;
use lang\mirrors\unittest\fixture\FixtureEnum;
use lang\mirrors\unittest\fixture\FixtureFinal;
use lang\mirrors\unittest\fixture\FixtureImpl;
use lang\mirrors\unittest\fixture\FixtureInterface;
use lang\mirrors\unittest\fixture\FixtureParams;
use lang\mirrors\unittest\fixture\FixtureTrait;
use lang\mirrors\unittest\fixture\FixtureUsed;
use lang\mirrors\unittest\fixture\FixtureUses;

/**
 * Base class for source implementation testing
 */
#[@fixture]
abstract class SourceTest extends \unittest\TestCase {

  #[@fixture]
  private $field;

  #[@fixture]
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

  #[@test]
  public function typeName() {
    $this->assertEquals('lang.mirrors.unittest.SourceTest', $this->reflect(__CLASS__)->typeName());
  }

  #[@test]
  public function typeDeclaration() {
    $this->assertEquals('SourceTest', $this->reflect(__CLASS__)->typeDeclaration());
  }

  #[@test]
  public function typeInstance() {
    $this->assertEquals(new XPClass(__CLASS__), $this->reflect(__CLASS__)->typeInstance());
  }

  #[@test]
  public function typeComment() {
    $this->assertEquals(
      "/**\n * Base class for source implementation testing\n */",
      $this->reflect(__CLASS__)->typeComment()
    );
  }

  #[@test]
  public function typeComment_for_undocumented_class() {
    $this->assertNull($this->reflect('lang.mirrors.unittest.fixture.FixtureTrait')->typeComment());
  }

  #[@test]
  public function packageName() {
    $this->assertEquals('lang.mirrors.unittest', $this->reflect(__CLASS__)->packageName());
  }

  #[@test]
  public function typeParent_of_this_class() {
    $this->assertEquals($this->reflect('unittest.TestCase'), $this->reflect(__CLASS__)->typeParent());
  }

  #[@test]
  public function typeParent_of_parentless_class() {
    $this->assertNull($this->reflect('lang.mirrors.unittest.fixture.AbstractMemberFixture')->typeParent());
  }

  #[@test]
  public function typeAnnotations_of_this_class() {
    $this->assertEquals(['fixture' => null], $this->reflect(__CLASS__)->typeAnnotations());
  }

  #[@test]
  public function typeAnnotations_of_annotationless_class() {
    $this->assertNull($this->reflect('lang.mirrors.unittest.fixture.AbstractMemberFixture')->typeAnnotations());
  }

  #[@test]
  public function typeModifiers() {
    $this->assertEquals(new Modifiers('public abstract'), $this->reflect(__CLASS__)->typeModifiers());
  }

  #[@test]
  public function typeModifiers_of_trait() {
    $this->assertEquals(new Modifiers('public abstract'), $this->reflect('lang.mirrors.unittest.fixture.FixtureTrait')->typeModifiers());
  }

  #[@test]
  public function typeModifiers_of_abstract() {
    $this->assertEquals(new Modifiers('public abstract'), $this->reflect('lang.mirrors.unittest.fixture.FixtureAbstract')->typeModifiers());
  }

  #[@test]
  public function typeModifiers_of_final() {
    $this->assertEquals(new Modifiers('public final'), $this->reflect('lang.mirrors.unittest.fixture.FixtureFinal')->typeModifiers());
  }

  #[@test]
  public function typeModifiers_of_enum() {
    $this->assertEquals(new Modifiers('public'), $this->reflect('lang.mirrors.unittest.fixture.FixtureEnum')->typeModifiers());
  }

  #[@test]
  public function typeModifiers_of_interface() {
    $this->assertEquals(new Modifiers('public'), $this->reflect('lang.mirrors.unittest.fixture.FixtureInterface')->typeModifiers());
  }

  #[@test]
  public function typeKind() {
    $this->assertEquals(Kind::$CLASS, $this->reflect(__CLASS__)->typeKind());
  }

  #[@test]
  public function typeKind_of_trait() {
    $this->assertEquals(Kind::$TRAIT, $this->reflect('lang.mirrors.unittest.fixture.FixtureTrait')->typeKind());
  }

  #[@test]
  public function typeKind_of_enum() {
    $this->assertEquals(Kind::$ENUM, $this->reflect('lang.mirrors.unittest.fixture.FixtureEnum')->typeKind());
  }

  #[@test]
  public function typeKind_of_interface() {
    $this->assertEquals(Kind::$INTERFACE, $this->reflect('lang.mirrors.unittest.fixture.FixtureInterface')->typeKind());
  }

  #[@test]
  public function typeImplements_declared_interface() {
    $this->assertTrue($this->reflect('lang.mirrors.unittest.fixture.FixtureImpl')->typeImplements('IteratorAggregate'));
  }

  #[@test]
  public function typeImplements_inherited_interface() {
    $this->assertTrue($this->reflect('lang.mirrors.unittest.fixture.FixtureImpl')->typeImplements('Traversable'));
  }

  #[@test]
  public function all_interfaces() {
    $this->assertEquals(
      ['IteratorAggregate', 'Traversable', 'lang\Closeable', 'lang\mirrors\unittest\fixture\FixtureInterface'],
      $this->sorted($this->reflect('lang.mirrors.unittest.fixture.FixtureImpl')->allInterfaces())
    );
  }

  #[@test]
  public function declared_interfaces() {
    $this->assertEquals(
      ['IteratorAggregate', 'lang\Closeable'],
      $this->sorted($this->reflect('lang.mirrors.unittest.fixture.FixtureImpl')->declaredInterfaces())
    );
  }

  #[@test]
  public function parent_interfaces() {
    $this->assertEquals(
      ['lang\Closeable', 'lang\mirrors\unittest\fixture\FixtureInterface'],
      $this->sorted($this->reflect('lang.mirrors.unittest.fixture.FixtureCloseable')->allInterfaces())
    );
  }

  #[@test]
  public function typeUses() {
    $this->assertTrue($this->reflect('lang.mirrors.unittest.fixture.FixtureImpl')->typeUses('lang\mirrors\unittest\fixture\FixtureTrait'));
  }

  #[@test]
  public function all_traits() {
    $this->assertEquals(
      ['lang\mirrors\unittest\fixture\FixtureTrait', 'lang\mirrors\unittest\fixture\FixtureUsed'],
      $this->sorted($this->reflect('lang.mirrors.unittest.fixture.FixtureUses')->allTraits())
    );
  }

  #[@test]
  public function declared_traits() {
    $this->assertEquals(
      ['lang\mirrors\unittest\fixture\FixtureUsed'],
      $this->sorted($this->reflect('lang.mirrors.unittest.fixture.FixtureUses')->declaredTraits())
    );
  }

  #[@test]
  public function with_constructor() {
    $this->assertEquals('__construct', $this->reflect(__CLASS__)->constructor()['name']);
  }

  #[@test]
  public function default_constructor() {
    $this->assertEquals('__default', $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->constructor()['name']);
  }

  #[@test]
  public function has_instance_field() {
    $this->assertTrue($this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->hasField('publicInstanceField'));
  }

  #[@test]
  public function has_inherited_field() {
    $this->assertTrue($this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->hasField('inheritedField'));
  }

  #[@test]
  public function has_trait_field() {
    $this->assertTrue($this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->hasField('traitField'));
  }

  #[@test]
  public function has_static_field() {
    $this->assertTrue($this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->hasField('publicClassField'));
  }

  #[@test]
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
      $this->sorted($this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->allFields())
    );
  }

  #[@test]
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
      $this->sorted($this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->declaredFields())
    );
  }

  #[@test]
  public function trait_fields() {
    $this->assertEquals(
      ['annotatedTraitField', 'traitField'],
      $this->sorted($this->reflect('lang.mirrors.unittest.fixture.FixtureTrait')->allFields())
    );
  }

  #[@test]
  public function instance_field() {
    $this->assertEquals(
      'publicInstanceField',
      $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->fieldNamed('publicInstanceField')['name']
    );
  }

  #[@test]
  public function static_field() {
    $this->assertEquals(
      'publicClassField',
      $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->fieldNamed('publicClassField')['name']
    );
  }

  #[@test]
  public function inherited_field() {
    $this->assertEquals(
      'inheritedField',
      $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->fieldNamed('inheritedField')['name']
    );
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function non_existant_field() {
    $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->fieldNamed('does.not.exist');
  }

  #[@test]
  public function fieldAnnotations_of_field_in_this_class() {
    $field= $this->reflect(__CLASS__)->fieldNamed('field');
    $this->assertEquals(['fixture' => null], $field['annotations']());
  }

  #[@test]
  public function has_instance_method() {
    $this->assertTrue($this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->hasMethod('publicInstanceMethod'));
  }

  #[@test]
  public function has_static_method() {
    $this->assertTrue($this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->hasMethod('publicClassMethod'));
  }

  #[@test]
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
      $this->sorted($this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->allMethods())
    );
  }

  #[@test]
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
      $this->sorted($this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->declaredMethods())
    );
  }

  #[@test]
  public function trait_methods() {
    $this->assertEquals(
      ['annotatedTraitMethod', 'traitMethod'],
      $this->sorted($this->reflect('lang.mirrors.unittest.fixture.FixtureTrait')->allMethods())
    );
  }

  #[@test]
  public function instance_method() {
    $this->assertEquals(
      'publicInstanceMethod',
      $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->methodNamed('publicInstanceMethod')['name']
    );
  }

  #[@test]
  public function static_method() {
    $this->assertEquals(
      'publicClassMethod',
      $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->methodNamed('publicClassMethod')['name']
    );
  }

  #[@test]
  public function inherited_method() {
    $this->assertEquals(
      'inheritedMethod',
      $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->methodNamed('inheritedMethod')['name']
    );
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function non_existant_method() {
    $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->methodNamed('does.not.exist');
  }

  #[@test]
  public function methodAnnotations_of_field_in_this_class() {
    $method= $this->reflect(__CLASS__)->methodNamed('method');
    $this->assertEquals(['fixture' => null], $method['annotations']());
  }

  #[@test]
  public function no_params() {
    $method= $this->reflect('lang.mirrors.unittest.fixture.FixtureParams')->methodNamed('noParam');
    $this->assertEquals([], $method['params']());
  }

  #[@test]
  public function one_param() {
    $method= $this->reflect('lang.mirrors.unittest.fixture.FixtureParams')->methodNamed('oneParam');
    $param= $method['params']()[0];
    $this->assertEquals(
      [0, 'arg', null, false, false, null],
      [$param['pos'], $param['name'], $param['type'], $param['ref'], $param['var'], $param['default']]
    );
  }

  #[@test]
  public function one_optional_param() {
    $method= $this->reflect('lang.mirrors.unittest.fixture.FixtureParams')->methodNamed('oneOptionalParam');
    $param= $method['params']()[0];
    $this->assertEquals(
      [0, 'arg', null, false, null, null],
      [$param['pos'], $param['name'], $param['type'], $param['ref'], $param['var'], $param['default']()]
    );
  }

  #[@test]
  public function typed_param() {
    $method= $this->reflect('lang.mirrors.unittest.fixture.FixtureParams')->methodNamed('oneTypeHintedParam');
    $param= $method['params']()[0];
    $this->assertEquals(new XPClass('lang.Type'), $param['type']());
  }

  #[@test]
  public function self_typehinted_param() {
    $method= $this->reflect('lang.mirrors.unittest.fixture.FixtureParams')->methodNamed('oneSelfTypeHintedParam');
    $param= $method['params']()[0];
    $this->assertEquals(new XPClass('lang.mirrors.unittest.fixture.FixtureParams'), $param['type']());
  }

  #[@test]
  public function array_typehinted_param() {
    $method= $this->reflect('lang.mirrors.unittest.fixture.FixtureParams')->methodNamed('oneArrayTypeHintedParam');
    $param= $method['params']()[0];
    $this->assertEquals(Type::$ARRAY, $param['type']());
  }

  #[@test]
  public function callable_typehinted_param() {
    $method= $this->reflect('lang.mirrors.unittest.fixture.FixtureParams')->methodNamed('oneCallableTypeHintedParam');
    $param= $method['params']()[0];
    $this->assertEquals(Type::$CALLABLE, $param['type']());
  }

  #[@test]
  public function has_constant() {
    $this->assertTrue($this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->hasConstant('CONSTANT'));
  }

  #[@test]
  public function all_constants() {
    $this->assertEquals(
      ['CONSTANT' => MemberFixture::CONSTANT, 'INHERITED' => MemberFixture::INHERITED],
      iterator_to_array($this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->allConstants())
    );
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function non_existant_constant() {
    $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->constantNamed('does.not.exist');
  }

  #[@test]
  public function constant_named() {
    $this->assertEquals(
      MemberFixture::CONSTANT,
      $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->constantNamed('CONSTANT')
    );
  }

  #[@test]
  public function isSubtypeOf_returns_false_for_self() {
    $this->assertFalse($this->reflect('lang.mirrors.unittest.fixture.FixtureImpl')->isSubtypeOf('lang\mirrors\unittest\fixture\FixtureImpl'));
  }

  #[@test]
  public function isSubtypeOf_parent() {
    $this->assertTrue($this->reflect('lang.mirrors.unittest.fixture.FixtureImpl')->isSubtypeOf('lang\mirrors\unittest\fixture\FixtureBase'));
  }

  #[@test]
  public function isSubtypeOf_implemented_interface() {
    $this->assertTrue($this->reflect('lang.mirrors.unittest.fixture.FixtureImpl')->isSubtypeOf('lang\mirrors\unittest\fixture\FixtureInterface'));
  }

  #[@test]
  public function trait_field_comment() {
    $field= $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->fieldNamed('traitField');
    $this->assertEquals('/** @type int */', $field['comment']());
  }

  #[@test]
  public function trait_field_annotations() {
    $field= $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->fieldNamed('annotatedTraitField');
    $this->assertEquals(['fixture' => null], $field['annotations']());
  }

  #[@test]
  public function trait_method_comment() {
    $field= $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->methodNamed('traitMethod');
    $this->assertEquals('/** @return void */', $field['comment']());
  }

  #[@test]
  public function trait_method_annotations() {
    $field= $this->reflect('lang.mirrors.unittest.fixture.MemberFixture')->methodNamed('annotatedTraitMethod');
    $this->assertEquals(['fixture' => null], $field['annotations']());
  }
}