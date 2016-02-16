<?php
/**
 * Comparison operator class.
 */
namespace Nord\Lumen\DynamoDb;

/**
 * Class ComparisonOperator.
 *
 * @package Nord\Lumen\DynamoDb
 */
class ComparisonOperator
{

    /**
     * Get mapping between operator and DynamoDb operator.
     *
     * @return array List of operator => DynamoDb operator.
     */
    public static function getOperatorMapping()
    {
        return [
            '='  => 'EQ',
            '>'  => 'GT',
            '>=' => 'GE',
            '<'  => 'LT',
            '<=' => 'LE',
            'in' => 'IN',
            '!=' => 'NE',
        ];
    }

    /**
     * Get a list of supported operators.
     *
     * @return array The supported operators.
     */
    public static function getSupportedOperators()
    {
        return array_keys(static::getOperatorMapping());
    }

    /**
     * Checks if the given operator is valid.
     *
     * @param string $operator The operator to check.
     *
     * @return bool True if the operator is valid, false otherwise.
     */
    public static function isValidOperator($operator)
    {
        $mapping = static::getOperatorMapping();

        return isset( $mapping[strtolower($operator)] );
    }

    /**
     * Get the DynamoDb operator.
     *
     * @param string $operator The operator to get.
     *
     * @return string The DynamoDb operator string.
     */
    public static function getDynamoDbOperator($operator)
    {
        $mapping = static::getOperatorMapping();

        return $mapping[$operator];
    }

    /**
     * Get supported operators for a query.
     *
     * @return array List of operators.
     */
    public static function getQuerySupportedOperators()
    {
        return ['EQ'];
    }

    /**
     * Checks if the given operator is a valid query operator.
     *
     * @param string $operator The operator.
     *
     * @return bool True if the operator is valid, false otherwise.
     */
    public static function isValidQueryOperator($operator)
    {
        $dynamoDbOperator = static::getDynamoDbOperator($operator);

        return static::isValidQueryDynamoDbOperator($dynamoDbOperator);
    }

    /**
     * Checks if the given DynamoDb operator is valid for queries.
     *
     * @param string $dynamoDbOperator The DynamoDb operator.
     *
     * @return bool True if operator is valid, false otherwise.
     */
    public static function isValidQueryDynamoDbOperator($dynamoDbOperator)
    {
        return in_array($dynamoDbOperator, static::getQuerySupportedOperators());
    }
}
