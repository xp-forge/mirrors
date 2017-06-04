<?php namespace lang\mirrors;

interface Source extends \lang\Value {

  /** @return bool */
  public function present();

  /** @return lang.mirrors.parse.CodeUnit */
  public function codeUnit();

  /** @return string */
  public function typeName();

  /** @return string */
  public function packageName();

  /** @return string */
  public function typeDeclaration();

  /** @return lang.Type */
  public function typeInstance();

  /** @return self */
  public function typeParent();

  /** @return string */
  public function typeComment();

  /** @return [:var] */
  public function typeAnnotations();

  /** @return lang.mirrors.Modifiers */
  public function typeModifiers();

  /** @return lang.mirrors.Kind */
  public function typeKind();

  /**
   * Returns whether this type is a subtype of a given argument
   *
   * @param  string $class
   * @return bool
   */
  public function isSubtypeOf($class);

  /**
   * Returns whether this type implements a given interface
   *
   * @param  string $name
   * @return bool
   */
  public function typeImplements($name);

  /**
   * Returns whether this type implements a given interface
   *
   * @param  string $name
   * @return bool
   */
  public function typeUses($name);

  /** @return php.Generator */
  public function declaredInterfaces();

  /** @return php.Generator */
  public function allTraits();

  /** @return php.Generator */
  public function declaredTraits();

  /** @return var */
  public function constructor();

  /** @return php.Generator */
  public function allFields();

  /** @return php.Generator */
  public function declaredFields();

  /** @return php.Generator */
  public function allMethods();

  /** @return php.Generator */
  public function declaredMethods();

  /** @return php.Generator */
  public function allConstants();

  /** @return php.Generator */
  public function allInterfaces();

  /**
   * Creates a new instance
   *
   * @param  var[] $args
   * @return var
   * @throws lang.IllegalArgumentException
   * @throws lang.mirrors.TargetInvocationException
   */
  public function newInstance($args);

  /**
   * Checks whether a given field exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasField($name);

  /**
   * Checks whether a given method exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasMethod($name);

  /**
   * Checks whether a given constant exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasConstant($name);

  /**
   * Gets a field by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function fieldNamed($name);

  /**
   * Gets a method by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function methodNamed($name);

  /**
   * Gets a constant by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function constantNamed($name);

  /**
   * Resolves a type name in the context of this reflection source
   *
   * @param  string $name
   * @return self
   */
  public function resolve($name);
}