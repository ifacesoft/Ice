<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Module;
use Ice\Helper\Console;
use Symfony\Component\Finder\Finder;

class Sami extends Action
{

    /**
     * Action config
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    protected static function config()
    {
        return [
            'view' => ['template' => ''],
            'input' => [
                'vendor' => ['default' => 'sami/sami'],
                'command' => ['default' => '/sami.php'],
                'config' => ['default' => 'vendor/sami.php']
            ]
        ];
    }

    /**
     * Run action
     *
     * @param  array $input
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function run(array $input)
    {
        $command = VENDOR_DIR . $input['vendor'] . $input['command'];
        $config = Module::getInstance()->get(Module::CONFIG_DIR) . $input['config'];

        Console::run(
            [
                'php ' . $command . ' update ' . $config
            ]
        );
    }
}