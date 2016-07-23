Mirrors
=======

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/mirrors.svg)](http://travis-ci.org/xp-forge/mirrors)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.6+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_6plus.png)](http://php.net/)
[![Supports PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Supports HHVM 3.5+](https://raw.githubusercontent.com/xp-framework/web/master/static/hhvm-3_5plus.png)](http://hhvm.com/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/mirrors/version.png)](https://packagist.org/packages/xp-forge/mirrors)

The *Mirrors* library provides a replacement for the XP Framework's reflection API.

Features
--------
**Concise and fluent**: This library aims at reducing the amount of `if` statements in the calling code when working with reflection. One example is constructor handling - if a type doesn't declare a constructor, a default constructor is returned. Another is the `all()` iterator which can optionally filter on instance and static members, instead of having to loop and check whether the modifiers.

**Sources**: This library supports reflecting classes either by using PHP's reflection classes or directly from their source code. The latter can be useful to prevent classes from being loaded, and in situations where we want to reflect classes during the class loading process (e.g. for compile-time metaprogramming, see the [partial types library](https://github.com/xp-forge/partial)).

**Hack language**: This library supports HHVM's [Hack language](http://docs.hhvm.com/manual/en/hacklangref.php), mapping its type literals to the XP type system and its attributes to XP annotations. You can use Hack alongside PHP in HHVM; and this library will support a seamless migration, e.g. moving from return types declared in the apidocs to Hack's syntactic form, all the while maintaining the same reflection information.

**PHP7**: This library supports PHP7's [scalar type hints](https://wiki.php.net/rfc/scalar_type_hints_v5) and [return type syntax](https://wiki.php.net/rfc/return_types) using both runtime reflection and static code sources.

**Subcommand**: This library provides an [RFC #0303 integration](https://github.com/xp-framework/rfc/issues/303) and offers a "mirror" subcommand for the new XP runners. See `xp help mirror` on how to use it.

API
---
The entry point class is the type mirror. It can be constructed by passing either type literals (e.g. `util\Date`), fully qualified class names (e.g. `util.Date`), XPClass instances or PHP's ReflectionClass; and optionally supplying a source to load the information from. The constructor does not throw exceptions for non-existant types: Instead, use `present()` to check of the type exists.

```php
public class lang.mirrors.TypeMirror extends lang.Object {
  public __construct(var $arg[, lang.mirrors.Sources $source])

  public bool present()
  public string name()
  public lang.Type type()
  public string comment()
  public self parent()
  public lang.mirrors.Kind kind()
  public lang.mirrors.Package package()
  public lang.mirrors.Modifiers modifiers()
  public lang.mirrors.parse.CodeUnit unit()
  public lang.mirrors.Traits traits()
  public lang.mirrors.Interfaces interfaces()
  public lang.mirrors.Constructor constructor()
  public lang.mirrors.Methods methods()
  public lang.mirrors.Method method(string $named) throws lang.ElementNotFoundException
  public lang.mirrors.Fields fields()
  public lang.mirrors.Field field(string $named) throws lang.ElementNotFoundException
  public lang.mirrors.Constants constants()
  public lang.mirrors.Annotations annotations()
  public lang.mirrors.Annotation annotation(string $named) throws lang.ElementNotFoundException
  public self resolve(string $name)
}
```

### Constructor
In order to create instances of a type, use the `constructor()` method. It will always return a Constructor instance regardless whether the type has a declared constructor - use `present()` to test if necessary.

```php
public class lang.mirrors.Constructor extends lang.mirrors.Routine {
  public __construct(lang.mirrors.TypeMirror $mirror)

  public string name()
  public string comment()
  public [:var] tags()
  public bool present()
  public lang.mirrors.Modifiers modifiers()
  public lang.Generic newInstance([var... $args= null]) throws ...
  public lang.mirrors.Throws throws()
  public lang.mirrors.Parameters parameters()
  public lang.mirrors.Parameter parameter(string|int $arg) throws lang.ElementNotFoundException
  public lang.mirrors.TypeMirror declaredIn()
  public lang.mirrors.Annotations annotations()
  public lang.mirrors.Annotation annotation(string $named) throws lang.ElementNotFoundException
}
```

### Methods
Methods can be retrieved by invoke `methods()` on a mirror, which returns a Methods collection. It can in turn be used to check for method's existance, fetch a method by name or iterate over provided methods.

```php
public class lang.mirrors.Methods extends lang.Object implements php.IteratorAggregate {
  public __construct(lang.mirrors.TypeMirror $mirror)

  public static lang.mirrors.Predicates ofClass()
  public static lang.mirrors.Predicates ofInstance()
  public static lang.mirrors.Predicates withAnnotation(string $annotation)
  public static lang.mirrors.Predicates with(function(lang.mirrors.Member): bool $predicate)

  public bool provides(string $name)
  public lang.reflection.Method named(string $name) throws lang.ElementNotFoundException
  public php.Generator all([lang.mirrors.Predicates $filter= null])
  public php.Generator declared([lang.mirrors.Predicates $filter= null])
  public php.Generator getIterator()
}

public class lang.mirrors.Method extends lang.mirrors.Routine {
  public __construct(lang.mirrors.TypeMirror $mirror, var $arg)

  public string name()
  public string comment()
  public [:var] tags()
  public lang.mirrors.Modifiers modifiers()
  public lang.Type returns()
  public var invoke([lang.Generic $instance= null], [var[] $args= [ ]]) throws ...
  public lang.mirrors.Throws throws()
  public lang.mirrors.Parameters parameters()
  public lang.mirrors.Parameter parameter(string|int $arg) throws lang.ElementNotFoundException
  public lang.mirrors.TypeMirror declaredIn()
  public lang.mirrors.Annotations annotations()
  public lang.mirrors.Annotation annotation(string $named) throws lang.ElementNotFoundException
}

public class lang.mirrors.Parameters extends lang.Object implements php.IteratorAggregate {
  public __construct(lang.mirrors.Routine $mirror, var $reflect)

  public bool present()
  public int length()
  private [:var] lookup()
  public bool provides(string $name)
  public lang.mirrors.Parameter named(string $name) throws lang.ElementNotFoundException
  public lang.mirrors.Parameter first() throws lang.ElementNotFoundException
  public lang.mirrors.Parameter at(int $position) throws lang.ElementNotFoundException
  public php.Generator getIterator()
}

public class lang.mirrors.Parameter extends lang.Object {
  public __construct(lang.mirrors.Routine $mirror, var $reflect)

  public string name()
  public int position()
  public lang.mirrors.Routine declaringRoutine()
  public bool isOptional()
  public bool isVariadic()
  public bool isVerified()
  public lang.Type type()
  public var defaultValue() throws lang.IllegalStateException
}
```

### Fields
Fields can be retrieved by invoke `fields()` on a mirror, which returns a Fields collection. As with methods, it offers named lookup, iteration and existance checks.

```php
public class lang.mirrors.Fields extends lang.Object implements php.IteratorAggregate {
  public __construct(lang.mirrors.TypeMirror $mirror)

  public static lang.mirrors.Predicates ofClass()
  public static lang.mirrors.Predicates ofInstance()
  public static lang.mirrors.Predicates withAnnotation(string $annotation)
  public static lang.mirrors.Predicates with(function(lang.mirrors.Member): bool $predicate)

  public bool provides(string $name)
  public lang.reflection.Field named(string $name) throws lang.ElementNotFoundException
  public php.Generator all([lang.mirrors.Predicates $filter= null])
  public php.Generator declared([lang.mirrors.Predicates $filter= null])
  public php.Generator getIterator()
}

public class lang.mirrors.Field extends lang.mirrors.Member {
  public __construct(lang.mirrors.TypeMirror $mirror, var $arg)

  public string name()
  public string comment()
  public [:var] tags()
  public lang.mirrors.Modifiers modifiers()
  public var read([lang.Generic $instance= null]) throws lang.IllegalArgumentException
  public void modify(lang.Generic $instance, var $value) throws lang.IllegalArgumentException
  public lang.mirrors.TypeMirror declaredIn()
  public lang.mirrors.Annotations annotations()
  public lang.mirrors.Annotation annotation(string $named) throws lang.ElementNotFoundException
}
```

### Modifiers
Types, constructors, methods and fields can have modifiers, accessible via `modifiers()`.

```php
public class lang.mirrors.Modifiers extends lang.Object {
  const IS_STATIC = 1
  const IS_ABSTRACT = 2
  const IS_FINAL = 4
  const IS_PUBLIC = 256
  const IS_PROTECTED = 512
  const IS_PRIVATE = 1024
  const IS_NATIVE = 61440

  public __construct(var $arg)

  public int bits()
  public string names()
  public bool isStatic()
  public bool isAbstract()
  public bool isFinal()
  public bool isPublic()
  public bool isProtected()
  public bool isPrivate()
  public bool isNative()
}
```

### Constants
Class constants in PHP are static final fields with a separate syntax. You can use `constants()` to retrieve the collection of Constant instances:

```php
public class lang.mirrors.Constants extends lang.Object implements php.IteratorAggregate {
  public __construct(lang.mirrors.TypeMirror $mirror)

  public bool provides(string $name)
  public lang.reflection.Constant named(string $name) throws lang.ElementNotFoundException
  public php.Generator getIterator()
}

public class lang.mirrors.Constant extends lang.Object {
  public __construct(lang.mirrors.TypeMirror $mirror, string $name, var $value)

  public string name()
  public var value()
  public lang.mirrors.TypeMirror declaredIn()
}
```

### Annotations
Types, but also fields and methods may be annotated in the XP Framework. Any annotatable element provides an `annotations()` accessor. It returns an annotation collection usable for named lookups, iterations and existance checks.

```php
public class lang.mirrors.Annotations extends lang.Object implements php.IteratorAggregate {
  public __construct(lang.mirrors.TypeMirror $mirror, var $backing)

  public bool present()
  public bool provides(string $name)
  public lang.reflection.Method named(string $name) throws lang.ElementNotFoundException
  public php.Generator getIterator()
}

public class lang.mirrors.Annotation extends lang.Object {
  public __construct(lang.mirrors.TypeMirror $mirror, var $name, var $value)

  public string name()
  public var value()
  public lang.mirrors.TypeMirror declaredIn()
}
```