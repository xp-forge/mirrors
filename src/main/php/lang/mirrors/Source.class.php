<?php namespace lang\mirrors;

interface Source {

  /** @return lang.mirrors.parse.CodeUnit */
  public function codeUnit();

  /** @return string */
  public function typeName();

  /** @return string */
  public function packageName();

  /** @return string */
  public function typeDeclaration();

  /** @return self */
  public function typeParent();

  /** @return string */
  public function typeComment();

  /** @return var */
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
   * @param  bool
   */
  public function typeImplements($name);

  /** @return php.Generator */
  public function allInterfaces();

  /** @return php.Generator */
  public function declaredInterfaces();

  /** @return php.Generator */
  public function allTraits();

  /** @return php.Generator */
  public function declaredTraits();

  /**
   * Returns whether this type implements a given interface
   *
   * @param  string $name
   * @param  bool
   */
  public function typeUses($name);

  /** @return [:var] */
  public function constructor();

  /**
   * Creates a new instance
   *
   * @param  var[] $args
   * @return lang.Generic
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
   * Gets a field by its name
   *
   * @param  string $name
   * @return var
   */
  public function fieldNamed($name);

  /** @return php.Generator */
  public function allFields();

  /** @return php.Generator */
  public function declaredFields();

  /**
   * Checks whether a given method exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasMethod($name);

  /**
   * Gets a method by its name
   *
   * @param  string $name
   * @return var
   */
  public function methodNamed($name);

  /** @return php.Generator */
  public function allMethods();

  /** @return php.Generator */
  public function declaredMethods();

  /**
   * Checks whether a given constant exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasConstant($name);

  /**
   * Gets a constant by its name
   *
   * @param  string $name
   * @return var
   */
  public function constantNamed($name);

  /** @return php.Generator */
  public function allConstants();

  /**
   * Resolves a type name in the context of this reflection source
   *
   * @param  string $name
   * @return self
   */
  public function resolve($name);
}