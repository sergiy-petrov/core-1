<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Extend;

use Flarum\Extension\Extension;
use Flarum\Extension\ExtensionManager;
use Illuminate\Contracts\Container\Container;

class WithExtension implements ExtenderInterface
{
    /**
     * @var string[]|array
     */
    protected $extensions;
    /**
     * @var callable
     */
    protected $callable;

    /**
     * WithExtension constructor.
     *
     * @param string|string[] $extension: The extension or extensions that need to be checked whether they are enabled
     *                                   before executing the extenders inside the callable.
     * @param callable $callable: A callable that returns one or an array of extenders to be executed when the given
     *                          extension(s) are enabled.
     */
    public function __construct($extension, callable $callable)
    {
        $this->extensions = (array) $extension;
        $this->callable = $callable;
    }

    public function extend(Container $container, Extension $extension = null)
    {
        /** @var ExtensionManager $extensions */
        $extensions = $container->make(ExtensionManager::class);

        $enabled = true;

        foreach ($this->extensions as $extension) {
            $id = Extension::nameToId($extension);

            $enabled = $enabled && $extensions->isEnabled($id);
        }

        if (! $enabled) {
            return;
        }

        $extenders = (array) $this->callable();

        /** @var ExtenderInterface $extender */
        foreach ($extenders as $extender) {
            $extender->extend($container, $extension);
        }
    }
}
