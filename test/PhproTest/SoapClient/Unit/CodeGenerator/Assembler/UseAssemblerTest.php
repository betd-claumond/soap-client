<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Assembler\AssemblerInterface;
use Phpro\SoapClient\CodeGenerator\Assembler\UseAssembler;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Model\Type;
use Zend\Code\Generator\ClassGenerator;

/**
 * Class UseAssemblerTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator\Assembler
 */
class UseAssemblerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    function it_is_an_assembler()
    {
        $assembler = new UseAssembler('MyUsedClass');
        $this->assertInstanceOf(AssemblerInterface::class, $assembler);
    }

    /**
     * @test
     */
    function it_can_assemble_type_context()
    {
        $assembler = new UseAssembler('MyUsedClass');
        $context = $this->createContext();
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_can_assemble_property_context()
    {
        $assembler = new UseAssembler('MyUsedClass');
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type('MyNamespace', 'MyType', []);
        $property = new Property('prop1', 'string');
        $context = new PropertyContext($class, $type, $property);
        $this->assertTrue($assembler->canAssemble($context));
    }

    /**
     * @test
     */
    function it_assembles_a_type()
    {
        $assembler = new UseAssembler('MyUsedClass');
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use MyUsedClass;

class MyType
{


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_a_type_with_alias()
    {
        $assembler = new UseAssembler('MyUsedClass', 'Alias');
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use MyUsedClass as Alias;

class MyType
{


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_assembles_an_existing_use_with_alias()
    {
        $assembler = new UseAssembler('MyUsedClass', 'Alias');
        $context = $this->createContext();
        $context->getClass()->addUse('MyUsedClass');
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

use MyUsedClass;

class MyType
{


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @test
     */
    function it_does_not_assemble_use_for_the_same_namespace()
    {
        $assembler = new UseAssembler('MyNamespace');
        $context = $this->createContext();
        $assembler->assemble($context);

        $code = $context->getClass()->generate();
        $expected = <<<CODE
namespace MyNamespace;

class MyType
{


}

CODE;

        $this->assertEquals($expected, $code);
    }

    /**
     * @return TypeContext
     */
    private function createContext()
    {
        $class = new ClassGenerator('MyType', 'MyNamespace');
        $type = new Type('MyNamespace', 'MyType', []);

        return new TypeContext($class, $type);
    }
}
