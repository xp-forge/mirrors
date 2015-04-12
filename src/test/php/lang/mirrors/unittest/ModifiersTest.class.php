<?php namespace lang\mirrors\unittest;

use lang\mirrors\Modifiers;

class ModifiersTest extends AbstractMethodTest {

  /** @return var[][] */
  private function modifiers() {
    return [
      [Modifiers::IS_PUBLIC, 'public'],
      [Modifiers::IS_PROTECTED, 'protected'],
      [Modifiers::IS_PRIVATE, 'private'],

      [Modifiers::IS_STATIC | Modifiers::IS_PUBLIC, 'public static'],
      [Modifiers::IS_STATIC | Modifiers::IS_PROTECTED, 'protected static'],
      [Modifiers::IS_STATIC | Modifiers::IS_PRIVATE, 'private static'],

      [Modifiers::IS_FINAL | Modifiers::IS_PUBLIC, 'public final'],
      [Modifiers::IS_FINAL | Modifiers::IS_PROTECTED, 'protected final'],
      [Modifiers::IS_FINAL | Modifiers::IS_PRIVATE, 'private final'],

      [Modifiers::IS_ABSTRACT | Modifiers::IS_PUBLIC, 'public abstract'],
      [Modifiers::IS_ABSTRACT | Modifiers::IS_PROTECTED, 'protected abstract'],
      [Modifiers::IS_ABSTRACT | Modifiers::IS_PRIVATE, 'private abstract'],
    ];
  }

  #[@test, @values('modifiers')]
  public function bits_from_int($bits, $names) {
    $this->assertEquals($bits, (new Modifiers($bits))->bits());
  }

  #[@test, @values('modifiers')]
  public function bits_from_array($bits, $names) {
    $this->assertEquals($bits, (new Modifiers(explode(' ', $names)))->bits());
  }

  #[@test, @values('modifiers')]
  public function bits_from_string($bits, $names) {
    $this->assertEquals($bits, (new Modifiers($names))->bits());
  }

  #[@test]
  public function passing_zero_yields_public_as_default() {
    $this->assertEquals(Modifiers::IS_PUBLIC, (new Modifiers(0))->bits());
  }

  #[@test]
  public function passing_empty_string_yields_public_as_default() {
    $this->assertEquals(Modifiers::IS_PUBLIC, (new Modifiers(''))->bits());
  }

  #[@test]
  public function passing_empty_array_yields_public_as_default() {
    $this->assertEquals(Modifiers::IS_PUBLIC, (new Modifiers([]))->bits());
  }

  #[@test, @values('modifiers')]
  public function names($bits, $names) {
    $this->assertEquals($names, (new Modifiers($bits))->names());
  }

  #[@test]
  public function equals_itself() {
    $modifiers= new Modifiers(Modifiers::IS_PUBLIC);
    $this->assertTrue($modifiers->equals($modifiers));
  }

  #[@test]
  public function equals_other_instance_with_same_bits() {
    $this->assertTrue((new Modifiers(Modifiers::IS_PUBLIC))->equals(new Modifiers(Modifiers::IS_PUBLIC)));
  }

  #[@test]
  public function does_not_equal_instance_with_differing_bits() {
    $this->assertFalse((new Modifiers(Modifiers::IS_PUBLIC))->equals(new Modifiers(Modifiers::IS_PROTECTED)));
  }
}