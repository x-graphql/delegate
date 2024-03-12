<?php

declare(strict_types=1);

namespace XGraphQL\Delegate;

use GraphQL\Type\Schema;

/**
 * Responsible to delegate GraphQL query execution via schema
 */
interface SchemaDelegatorInterface extends DelegatorInterface
{
    /**
     * @return Schema used to delegate execution.
     */
    public function getSchema(): Schema;
}
