Mirrors
=======

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/mirrors.svg)](http://travis-ci.org/xp-forge/mirrors)
[![XP Framework Mdodule](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.5+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_5plus.png)](http://php.net/)
[![Required HHVM 3.5+](https://raw.githubusercontent.com/xp-framework/web/master/static/hhvm-3_5plus.png)](http://hhvm.com/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/mirrors/version.png)](https://packagist.org/packages/xp-forge/mirrors)

Mirrors.

API
---
The entry point class is the type mirror:

```php
public class lang.mirrors.TypeMirror extends lang.Object {
  public var lang.mirrors.TypeMirror::$reflect

  public lang.mirrors.TypeMirror __construct(var $arg) throws lang.ClassNotFoundException

  public string name()
  public string comment()
  public self parent()
  public lang.mirrors.parse.CodeUnit unit()
  public lang.mirrors.Constructor constructor()
  public lang.mirrors.Methods methods()
  public lang.mirrors.Fields fields()
  public lang.mirrors.Constants constants()
  public lang.mirrors.Annotations annotations()
  public self resolve(string $name)
  public bool equals(var $cmp)
  public string toString()
  public string hashCode()
  public string getClassName()
  public lang.XPClass getClass()
}
```