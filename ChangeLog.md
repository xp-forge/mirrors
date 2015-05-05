Mirrors change log
==================

## ?.?.? / ????-??-??

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
