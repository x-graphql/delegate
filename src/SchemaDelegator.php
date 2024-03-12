<?php

declare(strict_types=1);

namespace XGraphQL\Delegate;

use GraphQL\Executor\Executor;
use GraphQL\Executor\Promise\Promise;
use GraphQL\Executor\Promise\PromiseAdapter;
use GraphQL\GraphQL;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Type\Schema;

final readonly class SchemaDelegator implements SchemaDelegatorInterface
{
    private PromiseAdapter $promiseAdapter;

    public function __construct(private Schema $schema, PromiseAdapter $promiseAdapter = null)
    {
        $this->promiseAdapter = $promiseAdapter ?? Executor::getPromiseAdapter();
    }

    /**
     * @throws \Exception
     */
    public function delegateToExecute(
        Schema $executionSchema,
        OperationDefinitionNode $operation,
        array $fragments = [],
        array $variables = []
    ): Promise {
        $source = new DocumentNode([
            'definitions' => new NodeList([...array_values($fragments), $operation])
        ]);

        return GraphQL::promiseToExecute(
            promiseAdapter: $this->getPromiseAdapter(),
            schema: $this->getSchema(),
            source: $source,
            variableValues: $variables,
            operationName: $operation->name?->value,
        );
    }

    public function getSchema(): Schema
    {
        return $this->schema;
    }

    public function getPromiseAdapter(): PromiseAdapter
    {
        return $this->promiseAdapter;
    }
}
