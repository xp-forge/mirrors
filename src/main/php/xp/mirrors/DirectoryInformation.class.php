<?php namespace xp\mirrors;

use io\Folder;
use lang\ClassLoader;
use lang\mirrors\Package;
use lang\mirrors\TypeMirror;
use lang\IllegalArgumentException;

class DirectoryInformation extends Information {
  private $folder, $loader, $package;

  /**
   * Creates a new directory information instance
   *
   * @param  string|io.Folder $folder
   */
  public function __construct($folder) {
    $this->folder= $folder instanceof Folder ? $folder : new Folder($folder);
    $uri= $this->folder->getURI();
    foreach (ClassLoader::getLoaders() as $loader) {
      if (
        0 === strncmp($uri, $loader->path, $l= strlen($loader->path)) &&
        $loader->providesPackage($package= strtr(substr($uri, $l, -1), DIRECTORY_SEPARATOR, '.'))
      ) {
        $this->loader= $loader;
        $this->package= new Package($package);
        return;
      }
    }
    throw new IllegalArgumentException('Cannot derive package name from '.$uri);
  }

  /** @return php.Generator */
  public function sources() {
    yield $this->loader;
  }

  /**
   * Display information
   *
   * @param  io.StringWriter $out
   * @return void
   */
  public function display($out) {
    $out->writeLine('directory ', $this->folder->getURI(), ' {');
    $ext= strlen(\xp::CLASS_FILE_EXT);

    $order= [
      'interface' => [],
      'trait'     => [],
      'enum'      => [],
      'class'     => []
    ];

    // Child packages
    foreach ($this->loader->packageContents($this->package->name()) as $entry) {
      $base= $this->package->isGlobal() ? '' : $this->package->name().'.';
      if ('/' === $entry{strlen($entry) - 1}) {
        $out->writeLine('  package ', $base.substr($entry, 0, -1));
      } else if (0 === substr_compare($entry, \xp::CLASS_FILE_EXT, -$ext)) {
        $mirror= new TypeMirror($this->loader->loadClass($base.substr($entry, 0, -$ext)));
        $order[$mirror->kind()->name()][]= self::declarationOf($mirror);
      }
    }

    // Types    
    foreach ($order as $type => $classes) {
      if (empty($classes)) continue;

      $out->writeLine();
      sort($classes);
      foreach ($classes as $name) {
        $out->writeLine('  ', $name);
      }
    }

    $out->writeLine('}');
  }
}