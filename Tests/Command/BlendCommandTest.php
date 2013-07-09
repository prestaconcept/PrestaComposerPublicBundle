<?php

namespace Presta\AnyPublicBlendBundle\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Presta\AnyPublicBlendBundle\Command\BlendCommand;

/**
 * Description of TestBlendCommand
 *
 * @author dey
 */
class BlendCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new BlendCommand());

        $command = $application->find('presta:any-public-blend');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/.../', $commandTester->getDisplay());
    }
}

?>
