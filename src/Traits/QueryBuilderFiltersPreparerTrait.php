<?php

declare(strict_types=1);

namespace DrSoftFr\PrestaShopModuleHelper\Traits;

use Doctrine\DBAL\Query\QueryBuilder;
use Exception;

trait QueryBuilderFiltersPreparerTrait
{
    /**
     * Builds the filter condition for a query
     *
     * @param QueryBuilder $qb The query builder object
     * @param string $filterField The field to filter on
     * @param string $filterValue The value to filter on
     * @param string $alias (optional) The table alias
     * @param string $operator The operator to use for the filter condition
     * @param string $type The type of the filter value
     *
     * @throws Exception If an invalid operator is provided
     */
    private function buildFilterCondition(QueryBuilder $qb, string $filterField, string $filterValue, string $alias, string $operator, string $type): void
    {
        $formatedFilterFieldWithAlias = empty($alias) ? $filterField : "$alias.$filterField";
        $sqlParameter = "{$alias}_{$filterField}_" . md5((string)rand(1, 1000));
        $sqlParameterKey = ":$sqlParameter";

        switch ($operator) {
            case '>':
                $qb->andWhere(
                    $qb
                        ->expr()
                        ->gt(
                            $formatedFilterFieldWithAlias,
                            $sqlParameterKey
                        )
                );

                break;
            case '>=':
                $qb->andWhere(
                    $qb
                        ->expr()
                        ->gte(
                            $formatedFilterFieldWithAlias,
                            $sqlParameterKey
                        )
                );

                break;
            case '<':
                $qb->andWhere(
                    $qb
                        ->expr()
                        ->lt(
                            $formatedFilterFieldWithAlias,
                            $sqlParameterKey
                        )
                );

                break;
            case '<=':
                $qb->andWhere(
                    $qb
                        ->expr()
                        ->lte(
                            $formatedFilterFieldWithAlias,
                            $sqlParameterKey
                        )
                );

                break;
            case '=':
                $qb->andWhere(
                    $qb
                        ->expr()
                        ->eq(
                            $formatedFilterFieldWithAlias,
                            $sqlParameterKey
                        )
                );

                break;
            case 'LIKE':
                $qb->andWhere(
                    $qb
                        ->expr()
                        ->like(
                            $formatedFilterFieldWithAlias,
                            $sqlParameterKey
                        )
                );

                break;
            default:
                throw new Exception('Invalid operator: ' . $operator);
        }

        $this->buildParameterValue($qb, $sqlParameter, $filterValue, $operator, $type);
    }

    /**
     * @param QueryBuilder $qb The query builder instance
     * @param string $key The parameter key
     * @param string $value The parameter value
     * @param string $operator The operator used for the parameter
     * @param string $type The type of the parameter
     *
     * @throws Exception If the parameter type is invalid
     */
    private function buildParameterValue(QueryBuilder $qb, string $key, string $value, string $operator, string $type): void
    {
        switch ($type) {
            case 'DATE_MIN':
                $qb->setParameter($key, sprintf('%s 0:0:0', $value));

                break;
            case 'DATE_MAX':
                $qb->setParameter($key, sprintf('%s 23:59:59', $value));

                break;
            case 'INT':
                $qb->setParameter($key, (int)$value);

                break;
            case 'STRING':
                if ('LIKE' === $operator) {
                    $qb->setParameter($key, "%$value%");
                } else {
                    $qb->setParameter($key, $value);
                }

                break;
            default:
                throw new Exception('Invalid parameter type: ' . $type);
        }
    }

    /**
     * Handles the query by checking and preparing the parameters.
     *
     * @param QueryBuilder $qb The query builder object
     * @param array $filters The array of filters to apply
     * @param array $parameters The array of parameters to check
     *
     * @throws Exception If the parameters are not valid
     */
    public function handle(QueryBuilder $qb, array $filters, array $parameters): void
    {
        $this->parametersChecker($parameters);
        $this->prepareQuery($qb, $filters, $parameters);
    }

    /**
     * @param array $parameters The array of parameters to check
     *
     * @throws Exception If the parameters are not valid
     */
    private function parametersChecker(array $parameters)
    {
        $allowedOperators = [
            '>',
            '>=',
            '<',
            '<=',
            '=',
            'LIKE'
        ];

        $allowedTypes = [
            'DATE',
            'INT',
            'STRING'
        ];

        $typesWithOperatorRequired = [
            'INT',
            'STRING'
        ];

        foreach ($parameters as $parameter) {
            if (!is_array($parameter)) {
                throw new Exception('Parameters should be an array');
            }

            if (!empty($parameter['alias']) && !is_string($parameter['alias'])) {
                throw new Exception('Alias parameter should be a string');
            }

            if (
                !empty($parameter['operator']) &&
                !in_array($parameter['operator'], $allowedOperators, true)
            ) {
                throw new Exception('Invalid parameter operator');
            }

            if (
                empty($parameter['type']) ||
                !in_array($parameter['type'], $allowedTypes, true)
            ) {
                throw new Exception('Invalid parameter type');
            }

            if (
                in_array($parameter['type'], $typesWithOperatorRequired, true) &&
                empty($parameter['operator'])
            ) {
                throw new Exception('Operator is required for this type: ' . $parameter['type']);
            }
        }
    }

    /**
     * Prepare the query by adding filter conditions based on the provided filters and parameters.
     *
     * @param QueryBuilder $qb The query builder object
     * @param array $filters The array of filters to apply
     * @param array $parameters The array of parameters used for filtering
     *
     * @throws Exception If the filter field is not found in the parameters array
     */
    private function prepareQuery(QueryBuilder $qb, array $filters, array $parameters): void
    {
        foreach ($filters as $filterField => $filterValue) {
            if (!array_key_exists($filterField, $parameters)) {
                continue;
            }

            if ($filterValue === '') {
                continue;
            }

            $alias = $parameters[$filterField]['alias'];
            $operator = $parameters[$filterField]['operator'];
            $type = $parameters[$filterField]['type'];

            if ($type === 'DATE') {
                if (isset($filterValue['from'])) {
                    $this->buildFilterCondition(
                        $qb,
                        $filterField,
                        $filterValue['from'],
                        $alias,
                        '>=',
                        'DATE_MIN'
                    );
                }

                if (isset($filterValue['to'])) {
                    $this->buildFilterCondition(
                        $qb,
                        $filterField,
                        $filterValue['to'],
                        $alias,
                        '<=',
                        'DATE_MAX'
                    );
                }

                continue;
            }

            $this->buildFilterCondition(
                $qb,
                $filterField,
                $filterValue,
                $alias,
                $operator,
                $type
            );
        }
    }
}
