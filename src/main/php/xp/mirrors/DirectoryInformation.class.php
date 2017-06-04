<?php namespace xp\mirrors;

use io\Folder;
use lang\ClassLoader;
use lang\mirrors\Package;
use lang\mirrors\TypeMirror;
use lang\IllegalArgumentException;

class DirectoryInformation extends CollectionInformation {
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
    throw new IllegalArgumentException('Cannot find '.$uri.' in class path');
  }

  /** @return iterable */
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
    $this->displayCollection($this->package, $this->loader, $out);
    $out->writeLine('}');
  }
}