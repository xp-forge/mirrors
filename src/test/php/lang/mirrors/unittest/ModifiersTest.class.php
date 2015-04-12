<?php namespace lang\mirrors\unittest;

use lang\mirrors\Modifiers;

class ModifiersTest extends AbstractMethodTest {

  /** @return var[][] */
  private function modifiers() {
    return [
      [MODIFIER_PUBLIC, 'public'],
      [MODIFIER_PROTECTED, 'protected'],
      [MODIFIER_PRIVATE, 'private'],

      [MODIFIER_PUBLIC | MODIFIER_STATIC, 'public static'],
      [MODIFIER_PROTECTED | MODIFIER_STATIC, 'protected static'],
      [MODIFIER_PRIVATE | MODIFIER_STATIC, 'private static'],

      [MODIFIER_PUBLIC | MODIFIER_FINAL, 'public final'],
      [MODIFIER_PROTECTED | MODIFIER_FINAL, 'protected final'],
      [MODIFIER_PRIVATE | MODIFIER_FINAL, 'private final'],

      [MODIFIER_PUBLIC | MODIFIER_ABSTRACT, 'public abstract'],
      [MODIFIER_PROTECTED | MODIFIER_ABSTRACT, 'protected abstract'],
      [MODIFIER_PRIVATE | MODIFIER_ABSTRACT, 'private abstract'],
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
    $this->assertEquals(MODIFIER_PUBLIC, (new Modifiers(0))->bits());
  }

  #[@test]
  public function passing_empty_string_yields_public_as_default() {
    $this->assertEquals(MODIFIER_PUBLIC, (new Modifiers(''))->bits());
  }

  #[@test]
  public function passing_empty_array_yields_public_as_default() {
    $this->assertEquals(MODIFIER_PUBLIC, (new Modifiers([]))->bits());
  }

  #[@test, @values('modifiers')]
  public function names($bits, $names) {
    $this->assertEquals($names, (new Modifiers($bits))->names());
  }

  #[@test]
  public function equals_itself() {
    $modifiers= new Modifiers(MODIFIER_PUBLIC);
    $this->assertTrue($modifiers->equals($modifiers));
  }

  #[@test]
  public function equals_other_instance_with_same_bits() {
    $this->assertTrue((new Modifiers(MODIFIER_PUBLIC))->equals(new Modifiers(MODIFIER_PUBLIC)));
  }

  #[@test]
  public function does_not_equal_instance_with_differing_bits() {
    $this->assertFalse((new Modifiers(MODIFIER_PUBLIC))->equals(new Modifiers(MODIFIER_PROTECTED)));
  }
}