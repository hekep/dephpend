<?php

declare(strict_types=1);

namespace Mihaeu\PhpDependencies\Analyser;

use Mihaeu\PhpDependencies\Dependencies\DependencyMap;
use Mihaeu\PhpDependencies\OS\PhpFileSet;
use PhpParser\NodeTraverser;

/**
 * @covers Mihaeu\PhpDependencies\Analyser\StaticAnalyser
 */
class StaticAnalyserTest extends \PHPUnit\Framework\TestCase
{
    /** @var StaticAnalyser */
    private $analyser;

    /** @var DependencyInspectionVisitor|\PHPUnit_Framework_MockObject_MockObject */
    private $dependencyInspectionVisitor;

    /** @var Parser */
    private $parser;

    public function setUp()
    {
        /** @var NodeTraverser $nodeTraverser */
        $nodeTraverser = $this->createMock(NodeTraverser::class);
        $this->dependencyInspectionVisitor = $this->createMock(DependencyInspectionVisitor::class);
        $this->parser = $this->createMock(Parser::class);

        $this->analyser = new StaticAnalyser(
            $nodeTraverser,
            $this->dependencyInspectionVisitor,
            $this->parser
        );
    }

    public function testAnalyse()
    {
        $this->dependencyInspectionVisitor->method('dependencies')->willReturn(new DependencyMap());
        $dependencies = $this->analyser->analyse(new PhpFileSet());
        $this->assertEquals(new DependencyMap(), $dependencies);
    }
}
