<?php

declare(strict_types=1);

namespace XGraphQL\Delegate\Test;

use GraphQL\Executor\ExecutionResult;
use GraphQL\Executor\Executor;
use GraphQL\Executor\Promise\Adapter\SyncPromiseAdapter;
use GraphQL\Executor\Promise\Promise;
use GraphQL\Language\Parser;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use PHPUnit\Framework\TestCase;
use XGraphQL\Delegate\SchemaDelegator;

class SchemaDelegatorTest extends TestCase
{
    public function testConstructor(): void
    {
        $schema = $this->createStub(Schema::class);
        $instance = new SchemaDelegator($schema);

        $this->assertInstanceOf(SchemaDelegator::class, $instance);
        $this->assertEquals(Executor::getPromiseAdapter(), $instance->getPromiseAdapter());
        $this->assertEquals($schema, $instance->getSchema());
    }

    public function testCanExecuteQueryViaSchema()
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'dummy' => [
                        'type' => Type::string(),
                        'resolve' => fn () => 'dummy'
                    ]
                ]
            ])
        ]);
        $adapter = new SyncPromiseAdapter();
        $instance = new SchemaDelegator($schema, $adapter);
        $operation = Parser::operationDefinition(
            <<<'GQL'
query test($include: Boolean!) {
    a: dummy @include(if: $include)
    ...b
}
GQL
        );
        $fragment = Parser::fragmentDefinition(
            <<<'GQL'
fragment b on Query {
   b: dummy @skip(if: $include)
}
GQL
        );

        $promise = $instance->delegateToExecute(
            new Schema([]),
            $operation,
            ['b' => $fragment],
            ['include' => true],
        );

        $this->assertInstanceOf(Promise::class, $promise);

        $result = $adapter->wait($promise);

        $this->assertInstanceOf(ExecutionResult::class, $result);
        $this->assertEquals(['a' => 'dummy'], $result->data);
        $this->assertCount(0, $result->errors);
    }
}
