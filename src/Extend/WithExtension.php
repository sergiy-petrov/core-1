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
     * @var string
     */
    protected $extension;
    /**
     * @var callable
     */
    protected $callable;

    public function __construct(string $extension, callable $callable)
    {
        $this->extension = $extension;
        $this->callable = $callable;
    }

    public function extend(Container $container, Extension $extension = null)
    {
        /** @var ExtensionManager $extensions */
        $extensions = $container->make(ExtensionManager::class);

        $id = Extension::nameToId($this->extension);

        if ($extensions->isEnabled($id)) {
            $extenders = (array) $this->callable();

            /** @var ExtenderInterface $extender */
            foreach ($extenders as $extender) {
                $extender->extend($container, $extension);
            }
        }
    }
}
