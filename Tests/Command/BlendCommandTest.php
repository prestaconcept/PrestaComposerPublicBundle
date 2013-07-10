<?php

namespace Presta\AnyPublicBlendBundle\Tests\Command;

require_once __DIR__.'/../app/AppKernel.php';

use Symfony\Bundle\FrameworkBundle\Console\Application;
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
        $application = $this->getApplication();
        $application->add(new BlendCommand());
        
        $command = $application->find('presta:any-public-blend');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--force' => true));

        $this->assertRegExp('/The library foo\/bar has been added/', $commandTester->getDisplay());
    }
    
    /**
     * @return \AppKernel
     */
    protected function getApplication()
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();
        return new Application($kernel);
    }
}
