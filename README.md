Mirrors
=======

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/mirrors.svg)](http://travis-ci.org/xp-forge/mirrors)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.6+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_6plus.png)](http://php.net/)
[![Required HHVM 3.5+](https://raw.githubusercontent.com/xp-framework/web/master/static/hhvm-3_5plus.png)](http://hhvm.com/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/mirrors/version.png)](https://packagist.org/packages/xp-forge/mirrors)

Mirrors.

API
---
The entry point class is the type mirror:

```php
public class lang.mirrors.TypeMirror extends lang.Object {
  public lang.mirrors.TypeMirror __construct(var $arg) throws lang.ClassNotFoundException

  public string name()
  public string comment()
  public self parent()
  public lang.mirrors.Kind kind()
  public lang.mirrors.parse.CodeUnit unit()
  public lang.mirrors.Traits traits()
  public lang.mirrors.Interfaces interfaces()
  public lang.mirrors.Constructor constructor()
  public lang.mirrors.Methods methods()
  public lang.mirrors.Fields fields()
  public lang.mirrors.Constants constants()
  public lang.mirrors.Annotations annotations()
  public self resolve(string $name)
}
```

### Constructor
In order to create instances of a type, use the `constructor()` method. It will always return a Constructor instance regardless whether the type has a declared constructor - use `present()` to test if necessary.

```php
public class lang.mirrors.Constructor extends lang.mirrors.Routine {
  public lang.mirrors.Constructor __construct(lang.mirrors.TypeMirror $mirror)

  public string name()
  public lang.mirrors.Modifiers modifiers()
  public string comment()
  public [:var] tags()
  public bool present()
  public lang.Generic newInstance([var* $args= null])
  public lang.mirrors.Parameters parameters()
  public lang.mirrors.TypeMirror declaredIn()
  public lang.mirrors.Annotations annotations()
}
```

### Methods
Methods can be retrieved by invoke `methods()` on a mirror, which returns a Methods collection. It can in turn be used to check for method's existance, fetch a method by name or iterate over provided methods.

```php
public class lang.mirrors.Methods extends lang.Object implements php.IteratorAggregate {
  public lang.mirrors.Methods __construct(lang.mirrors.TypeMirror $mirror)

  public bool provides(string $name)
  public lang.reflection.Method named(string $name) throws lang.ElementNotFoundException
  public php.Generator of(int $kind)
  public php.Generator declared()
  public php.Generator getIterator()
}

public class lang.mirrors.Method extends lang.mirrors.Routine {
  public lang.mirrors.Method __construct(lang.mirrors.TypeMirror $mirror, var $arg)

  public string name()
  public lang.mirrors.Modifiers modifiers()
  public string comment()
  public [:var] tags()
  public lang.Type returns()
  public var invoke([lang.Generic $instance= null], [var[] $args= [ ]]) throws ...
  public lang.mirrors.Parameters parameters()
  public lang.mirrors.TypeMirror declaredIn()
  public lang.mirrors.Annotations annotations()
}

public class lang.mirrors.Parameters extends lang.Object implements php.IteratorAggregate {
  public lang.mirrors.Parameters __construct(lang.mirrors.Method $mirror, var $reflect)

  public bool present()
  private [:var] lookup()
  public bool provides(string $name)
  public lang.mirrors.Parameter named(string $name) throws lang.ElementNotFoundException
  public lang.mirrors.Parameter first() throws lang.ElementNotFoundException
  public php.Generator getIterator()
}

public class lang.mirrors.Parameter extends lang.Object {
  public lang.mirrors.Parameter __construct(lang.mirrors.Method $mirror, var $reflect)

  public string name()
  public int position()
  public bool isOptional()
  public bool isVariadic()
  public lang.Type type()
  public var defaultValue() throws lang.IllegalStateException
}
```

### Fields
Fields can be retrieved by invoke `fields()` on a mirror, which returns a Fields collection. As with methods, it offers named lookup, iteration and existance checks.

```php
public class lang.mirrors.Fields extends lang.Object implements php.IteratorAggregate {
  public lang.mirrors.Fields __construct(lang.mirrors.TypeMirror $mirror)

  public bool provides(string $name)
  public lang.reflection.Field named(string $name) throws lang.ElementNotFoundException
  public php.Generator of(int $kind)
  public php.Generator declared()
  public php.Generator getIterator()
}

public class lang.mirrors.Field extends lang.mirrors.Member {
  public lang.mirrors.Field __construct(lang.mirrors.TypeMirror $mirror, var $arg)

  public string name()
  public lang.mirrors.Modifiers modifiers()
  public var get([lang.Generic $instance= null]) throws lang.IllegalArgumentException
  public void set(lang.Generic $instance, var $value) throws lang.IllegalArgumentException
  public lang.mirrors.TypeMirror declaredIn()
  public lang.mirrors.Annotations annotations()
}
```

### Modifiers
Both methods and fields can have access modifiers, accessible via `modifiers()`.

```php
public class lang.mirrors.Modifiers extends lang.Object {
  const IS_STATIC = 1
  const IS_ABSTRACT = 2
  const IS_FINAL = 4
  const IS_PUBLIC = 256
  const IS_PROTECTED = 512
  const IS_PRIVATE = 1024

  public lang.mirrors.Modifiers __construct(var $arg)

  public int bits()
  public string names()
  public bool isStatic()
  public bool isAbstract()
  public bool isFinal()
  public bool isPublic()
  public bool isProtected()
  public bool isPrivate()
}
```

### Constants
Class constants in PHP are static final fields with a separate syntax. You can use `constants()` to retrieve the collection of Constant instances:

```php
public class lang.mirrors.Constants extends lang.Object implements php.IteratorAggregate {
  public lang.mirrors.Constants __construct(lang.mirrors.TypeMirror $mirror)

  public bool provides(string $name)
  public lang.reflection.Constant named(string $name) throws lang.ElementNotFoundException
  public php.Generator getIterator()
}

public class lang.mirrors.Constant extends lang.Object {
  public lang.mirrors.Constant __construct(var $name, var $value)

  public string name()
  public var value()
}
```

### Annotations
Types, but also fields and methods may be annotated in the XP Framework. Any annotatable element provides an `annotations()` accessor. It returns an annotation collection usable for named lookups, iterations and existance checks.

```php
public class lang.mirrors.Annotations extends lang.Object implements php.IteratorAggregate {
  public lang.mirrors.Annotations __construct(var $mirror, var $backing)

  public bool present()
  public bool provides(string $name)
  public lang.reflection.Method named(string $name) throws lang.ElementNotFoundException
  public php.Generator getIterator()
}

public class lang.mirrors.Annotation extends lang.Object {
  public lang.mirrors.Annotation __construct(var $type, var $name, var $value)

  public string name()
  public var value()
}
```