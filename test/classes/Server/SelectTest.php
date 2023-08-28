<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Server;

use PhpMyAdmin\Config;
use PhpMyAdmin\Server\Select;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Util;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function __;

#[CoversClass(Select::class)]
class SelectTest extends AbstractTestCase
{
    /**
     * Prepares environment for the test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        //$_REQUEST
        $_REQUEST['log'] = 'index1';
        $_REQUEST['pos'] = 3;

        //$GLOBALS
        $config = Config::getInstance();
        $config->settings['MaxRows'] = 10;
        $GLOBALS['server'] = 1;
        $config->settings['ServerDefault'] = 'server';
        $config->settings['RememberSorting'] = true;
        $config->settings['SQP'] = [];
        $config->settings['MaxCharactersInDisplayedSQL'] = 1000;
        $config->settings['ShowSQL'] = true;
        $config->settings['TableNavigationLinksMode'] = 'icons';
        $config->settings['LimitChars'] = 100;

        $GLOBALS['table'] = 'table';

        $config->settings['DefaultTabServer'] = 'welcome';

        $config->settings['Servers'] = [
            '0' => [
                'host' => 'host0',
                'port' => 'port0',
                'only_db' => 'only_db0',
                'user' => 'user0',
                'auth_type' => 'config',
            ],
            '1' => [
                'host' => 'host1',
                'port' => 'port1',
                'only_db' => 'only_db1',
                'user' => 'user1',
                'auth_type' => 'config',
            ],
        ];
        //$_SESSION
    }

    /**
     * Test for Select::render
     */
    #[DataProvider('renderDataProvider')]
    public function testRender(bool $notOnlyOptions): void
    {
        $config = Config::getInstance();
        if ($notOnlyOptions) {
            $config->settings['DisplayServersList'] = null;
        }

        $html = Select::render($notOnlyOptions);
        $server = $config->settings['Servers']['0'];

        if ($notOnlyOptions) {
            $this->assertStringContainsString(
                Util::getScriptNameForOption(
                    $config->settings['DefaultTabServer'],
                    'server',
                ),
                $html,
            );

            $this->assertStringContainsString(
                __('Current server:'),
                $html,
            );
            $this->assertStringContainsString(
                '(' . __('Servers') . ')',
                $html,
            );
        }

        //server items
        $this->assertStringContainsString($server['host'], $html);
        $this->assertStringContainsString($server['port'], $html);
        $this->assertStringContainsString($server['only_db'], $html);
        $this->assertStringContainsString($server['user'], $html);
    }

    /** @return mixed[][] */
    public static function renderDataProvider(): array
    {
        return [
            'only options' => [false],
            'not only options' => [true],
        ];
    }
}
