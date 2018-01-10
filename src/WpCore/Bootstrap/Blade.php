<?php

namespace WpCore\Bootstrap;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

/**
 * Class Blade to use blade template inside WP
 */
class Blade
{
    /**
     * Paths of templates to render
     * @var array
     */
    public $templates = '';

    /**
     * Path where template should be cached
     * @var string
     */
    public $compiled = '';

    /**
     * Filesystem for bladeCompiler
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Eventdispatcher for bladeCompiler
     * @var Eventdispatcher
     */
    protected $eventDispatcher;

    /**
     * Blade compiler
     * @var BladeCompiler
     */
    protected $bladeCompiler;

    /**
     * viewResolver for blade templates
     * @var EngineResolver
     */
    protected $viewResolver;

    /**
     * viewFinder to find blade templates
     * @var FileViewFinder
     */
    protected $viewFinder;

    /**
     * viewFactory to compile blade templates
     * @var Factory
     */
    protected $viewFactory;

    /**
     * Init Blade compiler
     */
    public function __construct()
    {
        // Setup templates and compiled paths
        $this->templates = config('blade.templates');
        $this->compiled = config('blade.compiled');

        // Dependencies
        $this->filesystem = new Filesystem;
        $this->eventDispatcher = new Dispatcher(new Container);

        // Create Blade Compiler
        $this->bladeCompiler = new BladeCompiler($this->filesystem, $this->compiled);
        $this->extend();

        // Create View Factory capable of rendering PHP and Blade templates
        $this->viewResolver = new EngineResolver;
        $this->viewResolver->register('blade', function () {
            return new CompilerEngine($this->bladeCompiler, $this->filesystem);
        });
        $this->viewResolver->register('php', function () {
            return new PhpEngine;
        });
        $this->viewFinder = new FileViewFinder($this->filesystem, $this->templates);
        $this->viewFactory = new Factory($this->viewResolver, $this->viewFinder, $this->eventDispatcher);
    }

    /**
     * Render Blade template for Front
     * @param  string $view The view to render
     * @param  array  $with Data to pass to the view
     * @return string       Html rendered
     */
    public static function render($view, $with = [])
    {
        $instance = new static;
        return $instance->viewFactory->make($view, $with)->render();
    }

    /**
     * Custom directives
     * @return string Html of the directive
     */
    public function extend()
    {
        // The Loop
        $this->bladeCompiler->directive('loop', function () {
            return '<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>';
        });

        $this->bladeCompiler->directive('loopend', function () {
            return '<?php endwhile; endif; ?>';
        });

        $this->bladeCompiler->directive('loopelse', function () {
            return '<?php endwhile; ?><?php else: ?>';
        });

        $this->bladeCompiler->directive('loopendelse', function () {
            return '<?php endif; ?>';
        });

        // @svg
        $this->bladeCompiler->directive('svg', function ($name, $width = null, $height = null) {
            $size = '';
            if ($width != null) {
                $size .= ' width="' . $width . '"';
            }
            if ($height != null) {
                $size .= ' height="' . $height . '"';
            }
            return '<svg role="img"' . $size . '><use xlink:href="' . get_template_directory_uri() . '/build/svg/sprite.svg#' . $name . '" /></svg>';
        });
    }
}
