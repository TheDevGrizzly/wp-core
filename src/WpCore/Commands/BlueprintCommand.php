<?php

namespace WpCore\Commands;

/**
 * Class BlueprintCommand to generate blueprints
 */
class BlueprintCommand extends \WP_CLI_Command
{
    /**
     * Setup to create Model
     * @var array
     */
    protected $model = [
        'blueprint' => __DIR__ . '/../Blueprints/Model.php',
        'directory' => TEMPLATEPATH . '/app/Models/',
        'label'     => 'Model'
    ];

    /**
     * Setup to create Controller
     * @var array
     */
    protected $controller = [
        'blueprint' => __DIR__ . '/../Blueprints/Controller.php',
        'directory' => TEMPLATEPATH . '/app/Controllers/',
        'label'     => 'Controller'
    ];

    /**
     * Setup to create Command
     * @var array
     */
    protected $command = [
        'blueprint' => __DIR__ . '/../Blueprints/Command.php',
        'directory' => TEMPLATEPATH . '/app/Commands/',
        'label'     => 'Command'
    ];

    /**
     * Setup to create Mail
     * @var array
     */
    protected $mail = [
        'blueprint' => __DIR__ . '/../Blueprints/Mail.php',
        'directory' => TEMPLATEPATH . '/app/Mails/',
        'label'     => 'Mail'
    ];

    /**
     * Setup to create Shortcode
     * @var array
     */
    protected $shortcode = [
        'blueprint' => __DIR__ . '/../Blueprints/Shortcode.php',
        'directory' => TEMPLATEPATH . '/app/Shortcodes/',
        'label'     => 'Shortcode'
    ];

    /**
     * Command to generate Model
     * @param  array $args Command arguments
     */
    public function model($args)
    {
        list($name) = $args;

        $this->createFile($this->model, $name);
    }

    /**
     * Command to generate Controller
     * @param  array $args Command arguments
     */
    public function controller($args)
    {
        list($name) = $args;

        $this->createFile($this->controller, $name);
    }

    /**
     * Command to generate Model and Controller
     * @param  array $args Command arguments
     */
    public function ressource($args)
    {
        list($name) = $args;

        $this->createFile($this->model, $name);
        $this->createFile($this->controller, $name . 'Controller');
    }

    /**
     * Command to generate Command
     * @param  array $args Command arguments
     */
    public function command($args)
    {
        list($name) = $args;

        $this->createFile($this->command, $name);
    }

    /**
     * Command to generate Mail
     * @param  array $args Command arguments
     */
    public function mail($args)
    {
        list($name) = $args;

        $this->createFile($this->mail, $name);
    }

    /**
     * Command to generate Shortcode
     * @param  array $args Command arguments
     */
    public function shortcode($args)
    {
        list($name) = $args;

        $this->createFile($this->shortcode, $name);
    }

    /**
     * Helper to create files
     * @param  array $args Command arguments
     */
    /**
     * Helper to create files
     * @param  array $type Setup to create the file
     * @param  string $name Name of the file to create
     */
    private function createFile($type, $name)
    {
        $filename = $type['directory'] . $name . '.php';

        // Check if file already exists
        if (file_exists($filename)) {
            \WP_CLI::error($type['label'] . ' ' . $name . ' already exists');
            \WP_CLI::halt();
        }

        // Get blueprint
        $content = file_get_contents($type['blueprint']);
        if ($content === false) {
            \WP_CLI::error('Error when getting blueprint file for ' . $type['label']);
            \WP_CLI::halt();
        }

        // Generate file
        $content = str_replace('Blueprint', $name, $content);
        $content = str_replace('blueprint', strtolower($name), $content);
        if (file_put_contents($filename, $content) === false) {
            \WP_CLI::error('Error when creating file');
        } else {
            \WP_CLI::success($type['label'] . ' ' . $name . ' was created successfully!');
        }
    }
}
