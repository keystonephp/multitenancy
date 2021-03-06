<?php

declare(strict_types=1);

namespace Keystone\Multitenancy\Query\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Keystone\Multitenancy\Model\TenantScoped;
use RuntimeException;

class TenantScopedFilter extends SQLFilter
{
    /**
     * @var string|null
     */
    private $column;

    /**
     * @return string|null
     */
    public function getColumn(): ?string
    {
        return $this->column;
    }

    /**
     * @param string $column
     */
    public function setColumn(string $column)
    {
        $this->column = $column;
    }

    /**
     * @param ClassMetadata $targetEntity
     * @param string $targetTableAlias
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$targetEntity->getReflectionClass()->implementsInterface(TenantScoped::class)) {
            return '';
        }

        if ($this->getColumn() === null) {
            throw new RuntimeException('Tenant query filter column not set');
        }

        if (!$this->hasParameter('id')) {
            throw new RuntimeException('The "id" filter parameter was not set');
        }

        return sprintf(
            '%s.%s = %s',
            $targetTableAlias,
            $this->getColumn(),
            $this->getParameter('id')
        );
    }
}
