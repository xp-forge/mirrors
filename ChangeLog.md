Mirrors change log
==================

## ?.?.? / ????-??-??

## 6.0.0 / 2020-04-10

* Removed support for Hack Language - @thekid
* Implemented xp-framework/rfc#334: Drop PHP 5.6:
  . **Heads up:** Minimum required PHP version now is PHP 7.0.0
  . Rewrote code base, grouping use statements
  . Converted `newinstance` to anonymous classes
  (@thekid)

## 5.0.6 / 2020-04-05

* Implemented RFC #335: Remove deprecated key/value pair annotation syntax
  (@thekid)

## 5.0.5 / 2020-01-07

* Added compatibility with XP 10 - @thekid

## 5.0.4 / 2019-11-25

* Adjusted to new unittest APIs - @thekid

## 5.0.3 / 2019-08-26

* Fixed PHP 7.4 compatibility:
  - Refrain from using curly braces used for array offsets
  - Used `MODIFIER_*` constants from lang.base.php instead of hardcoded values
  - Removed *Function `ReflectionType::__toString()` is deprecated* warnings
  - Removed *Trying to access array offset on value of type null* warnings
  (@thekid)
* Changed code to use `util.Objects::stringOf()` instead of the deprecated
  `xp::stringOf()` core functionality.
  (@thekid)

## 5.0.2 / 2018-12-29

* Fixed issue with PHP 7.3 and constructors - @thekid

## 5.0.1 / 2017-06-17

* Fixed issue #44: HHVM PHP7 mode - @thekid

## 5.0.0 / 2017-06-04

* No longer show non-public members by default in `xp mirror` command
  line tool. Supply `--all` to include them.
  (@thekid)
* Added forward compatibility with XP 9.0.0 - @thekid

## 4.2.3 / 2017-02-07

* Fixed issue #40: PHP7 grouped use statement by merging PR #41
  (@thekid)

## 4.2.2 / 2017-01-31

* Fixed issue #39: Exception for xml.Node mirror output - @thekid

## 4.2.1 / 2016-09-19

* Fixed issue #38: Method `lang\mirrors\Method::__toString()` must not throw 
  an exception
  (@thekid)

## 4.2.0 / 2016-08-28

* Added forward compatibility with XP 8.0.0 - @thekid

## 4.1.0 / 2016-07-24

* Merged PR #37: Implement object type union - @thekid
* Fixed parsing `iterable` type from apidocs - @thekid

## 4.0.0 / 2016-07-24

* Added preliminary support for iterable types (currently inactive
  due to PHP [bug 72661](https://bugs.php.net/bug.php?id=72661); see
  the [iterable RFC](https://wiki.php.net/rfc/iterable)).
  (@thekid)
* Added support for nullable type syntax present in PHP 7.1; see the
  [nullable types RFC](https://wiki.php.net/rfc/nullable_types)
  (@thekid)
* Added support for `void` return type syntax present in PHP 7.1, see
  the [void return type RFC](https://wiki.php.net/rfc/void_return_type)
  (@thekid)
* Added support for non-`lang.Generic` instances - @thekid
* **Heads up: Dropped PHP 5.5 support!** The minimum PHP version now
  required is PHP 5.6, see PR #36
  (@thekid)
* Merged PR #34: Refactor: Use ClassParser from core instead of PHP
  syntax, yielding a big improvement on performance and memory use
  (@thekid)

## 3.0.0 / 2016-02-21

* Added version compatibility with XP 7 - @thekid

## 2.1.3 / 2016-01-24

* Fixed highlighting to work with both XP6 and XP7 - @thekid

## 2.1.2 / 2016-01-23

* Fix code to use `nameof()` instead of the deprecated `getClassName()`
  method from lang.Generic. See xp-framework/core#120
  (@thekid)

## 2.1.1 / 2016-01-23

* Fixed `declaredInterfaces()` and `allInterfaces()` methods to behave
  consistently across the respective implementations.
  (@thekid)
* Fixed parse errors in files with newline after `<?php`/`<?hh` tag
  (@thekid)
* Merged pull request #34: Lazy initialization for members, caching 
  (@thekid)

## 2.1.0 / 2016-01-17

* Added a new `lang.mirrors.InstanceMirror` class as a shortcut to
  creating a TypeMirror and pass `get_class($arg)`.
  (@thekid)

## 2.0.0 / 2016-01-17

* Implemented #33: Added new method `lang.mirrors.TypeMirror::type()`
  (@thekid)
* Implemented #30: Added "xp mirror" command. See xp-framework/rfc#303
  (@thekid)

## 1.6.0 / 2016-01-17

* Merged pull request #32: Fix variadics.
  . Via PHP 5.6 syntax *(also supported in HHVM)*
  . Via PHP 7 syntax
  . Declared via `var...` in api documentation
  . Declared via `var*` in api documentation
  . Now consistent with xp-framework/core, plus fixes #31
  (@thekid)

## 1.5.0 / 2015-12-20

* Merged pull request #27: Additional shortcuts - @thekid

## 1.4.5 / 2015-12-20

* Added dependency on tokenize library which has since been extracted
  from XP core.
  (@thekid)

## 1.4.4 / 2015-10-27

* Fixed constructor and method invocations leaving exception cause of
  TargetInvocationExceptions empty when native exceptions are raised.
  (@thekid)

## 1.4.3 / 2015-10-27

* Fixed issue #28: Parsing multiline annotation swallows whitespace
  (@thekid)

## 1.4.2 / 2015-10-26

* Fixed not handling parent types correctly when type has no parent
  (@thekid)
* Improved performance when OpCache extension is not loaded
  (@thekid)

## 1.4.1 / 2015-10-25

* Fixed issue #25: Errors with opcache.save_comments=0
  (@thekid)

## 1.4.0 / 2015-10-25

* Merged PR #23: Backport to PHP 5.5. Minimum PHP version required is
  now **PHP 5.5.0**.
  (@thekid)
* Fixed member references to class constants, the `::class` literal and
  static type members inside annotations
  (@thekid)
* Fixed allConstants() and constantNamed() in `lang.mirrors.FromCode`
  (@thekid)

## 1.3.1 / 2015-08-25

* Fixed default constructor's modifiers() method returning an integer
  instead of a `lang.reflect.Modifiers` instance as declared.
  (@thekid)

## 1.3.0 / 2015-08-25

* Implemented PR #22 - Annotations via compiled meta data. This is a
  huge performance improvement for code previously handled with core
  reflection or loaded via XP compiler.
  (@thekid)
* Implemented PR #21 - Add filtering for methods and fields:
  . Added optional parameter "filter" to `declared()` method
  . Added new `all()` method with same signature as declared()
  . Deprecated the of() method and its bitfield parameter.
  (@thekid)

## 1.2.0 / 2015-08-08

* Fixed issue #18 - Failing tests with opcache.save_comments=0 - @thekid

## 1.1.0 / 2015-07-27

* Fixed issue #13 - Native and foreign class support:
  . Added support for the types resource, mixed, null, false and true.
  . Added support for the type aliases float, integer and boolean.
  . Added support for the type "object" (interpreted as Type::$VAR).
  . Added support for the type $this (interpreted as "self").
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
