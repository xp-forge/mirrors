Mirrors change log
==================

## ?.?.? / ????-??-??

* Foreign class support:
  . Added support for the types resource, mixed, null, false and true.
  . Added support for the type aliases float, integer and boolean.
  . Added support for the type "object" (interpreted as Type::$VAR).
  (@thekid)
* Fixed bug with field types not being resolved according to the current
  context, e.g. `self` or unqualified names. Previously already worked
  with methods and their parameters.
  (@thekid)
* Added string representations for the members accessors `constants()`,
  `fields()` and `methods()`.
  (@thekid)

## 1.0.0 / 2015-07-26

* **Dropped support for PHP7 alpha1, now requires alpha2!** This second
  alpha includes the "Throwable" RFC which changes the builtin exception
  hierarchy: https://wiki.php.net/rfc/throwable-interface
  (@thekid)
* Allowed PHP keywords as, break, case, callable, catch, clone, const,
  continue, declare, default, do, else, extends, finally, for, foreach,
  function, global, goto, if, include, instanceof, insteadof, interface,
  list, namespace, new, require, return, switch, throw, trait, try, use,
  var, while and yield in annotation keys.
  (@thekid)

## 0.9.0 / 2015-07-12

* Added Parameter::declaringRoutine(). See xp-forge/mirrors#15 - @thekid
* Added forward compatibility with XP 6.4.0 - @thekid

## 0.8.0 / 2015-06-13

* **Heads up: Minimum requirement is now XP 6.3.1!**
* Added support for PHP7's [scalar type hints](https://wiki.php.net/rfc/scalar_type_hints_v5)
  (@thekid)
* Added support for PHP7's [return type syntax](https://wiki.php.net/rfc/return_types)
  (@thekid)
* Added forward compatibility handling of PHP7's BaseException class
  to method invocations and instance creations
  (@thekid)

## 0.7.0 / 2015-06-07

* **Heads up: Minimum requirement is now XP 6.3.0!**
* Merged PR #14: Add type union support
  (@thekid)
* Added support for `array` and `callable` type unions
  (@thekid)

## 0.6.0 / 2015-05-20

* Resolved ambiguity in function types in conjunction with arrays.
  See xp-framework/core#74
  (@thekid)
* Added new `Package::isGlobal()` method and `Package::$GLOBAL`
  (@thekid)

## 0.5.1 / 2015-05-16

* Added support for namespace aliases - @thekid
* Added support for relative namespace references - @thekid

## 0.5.0 / 2015-05-16

* Changed default, reflection and code sources to behave consistently
  regarding when types are not found. Now, all three sources return
  an type mirror referencing an incomplete type. To check for a type's
  existance, use the new `TypeMirror::present()` method.
  (@thekid)

## 0.4.0 / 2015-05-05

* Updated dependency on xp-framework/core to `~6.2` - @thekid
* Merged PR #11: Hack language support. This adds support for Hack's
  native annotations and type system as well as its constructor argument
  promotion on HHVM as well as when using the code reflection source.
  http://docs.hhvm.com/manual/en/hack.attributes.php
  http://docs.hhvm.com/manual/en/hack.annotations.types.php
  http://docs.hhvm.com/manual/en/hack.constructorargumentpromotion.php
  (@thekid)

## 0.3.0 / 2015-04-20

* Merged PR #10: Reflection sources. Types can now be reflected both via
  the reflection classes or directly from the source code. The latter is
  important in cases we want to reflect classes during class loading.
  (@thekid)

## 0.2.1 / 2015-04-13

* Fixed `new T(...)` inside annotations not creating instances - @thekid
* Fixed issue #9: GenericTypeRef broken - @thekid

## 0.2.0 / 2015-04-13

* Implemented `toString()` and string casts for Annotation instances
  (@thekid)
* Added lang.mirrors.Annotation::declaredIn() - @thekid
* Added lang.mirrors.Constant::declaredIn() - @thekid
* Implemented `toString()` and string casts for all members: Fields,
  methods, constants and constructors.
  (@thekid)
* Changed lang.mirrors.Field::get() to `read()` - @thekid
* Changed lang.mirrors.Field::set() to `modify()` - @thekid

## 0.1.1 / 2015-04-12

* Made this library work with HHVM 3.5
  (@thekid)
* Changed dependency for xp-forge parse from "dev-master" to "0.1"
  (@thekid)

## 0.1.0 / 2015-04-12

* First public release - @thekid
