<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Compiler\CacheManager;
use Livewire\Compiler\Compiler;
use Livewire\Factory\Factory;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $viewCachePath = storage_path('framework/views-testing');
        File::makeDirectory($viewCachePath, 0777, true, true);
        $this->app['config']->set('view.compiled', $viewCachePath);
        $this->app->forgetInstance('blade.compiler');
        $this->app->singleton('blade.compiler', function ($app) use ($viewCachePath): BladeCompiler {
            return new BladeCompiler($app['files'], $viewCachePath);
        });

        foreach (config('livewire.component_locations', []) as $location) {
            if (! is_dir($location)) {
                continue;
            }

            $this->app['blade.compiler']->anonymousComponentPath($location);
            $this->app['view']->addLocation($location);
        }

        foreach (config('livewire.component_namespaces', []) as $namespace => $location) {
            if (! is_dir($location)) {
                continue;
            }

            $this->app['blade.compiler']->anonymousComponentPath($location, $namespace);
            $this->app['view']->addNamespace($namespace, $location);
        }

        $cachePath = storage_path('framework/views/livewire-testing');

        $this->app->forgetInstance('livewire.compiler');
        $this->app->forgetInstance('livewire.factory');

        $this->app->singleton('livewire.compiler', function () use ($cachePath): Compiler {
            return new Compiler(new CacheManager($cachePath));
        });

        $this->app->singleton('livewire.factory', function ($app): Factory {
            return new Factory($app['livewire.finder'], $app['livewire.compiler']);
        });
    }
}
