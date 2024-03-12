<?php

declare(strict_types=1);

namespace XGraphQL\Delegate;

use GraphQL\Executor\Promise\Promise;
use GraphQL\Executor\Promise\PromiseAdapter;
use GraphQL\Language\AST\FragmentDefinitionNode;
use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Type\Schema;

/**
 * Responsible to delegate GraphQL query execution.
 */
interface DelegatorInterface
{
    /**
     * @param Schema $executionSchema
     * @param OperationDefinitionNode $operation
     * @param FragmentDefinitionNode[] $fragments
     * @param array<string, mixed> $variables
     * @return Promise promised value MUST be an instance of `GraphQL\Executor\ExecutionResult`.
     */
    public function delegateToExecute(
        Schema $executionSchema,
        OperationDefinitionNode $operation,
        array $fragments = [],
        array $variables = []
    ): Promise;

    /**
     * @return PromiseAdapter an adapter use to deal with delegated promise
     */
    public function getPromiseAdapter(): PromiseAdapter;
}
