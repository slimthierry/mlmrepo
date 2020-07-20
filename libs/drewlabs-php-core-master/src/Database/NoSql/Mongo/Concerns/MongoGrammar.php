<?php

namespace Drewlabs\Core\Database\NoSql\Mongo\Concerns;

use Drewlabs\Core\Database\NoSql\Contracts\IGrammar;
use Drewlabs\Utils\Str;

class MongoGrammar implements IGrammar
{

    /**
     *
     * @var array
     */
    protected $query;

    /**
     * @inheritDoc
     */
    public function prepareQuery(array $conditions)
    {
        if (empty($conditions)) {
            $this->query =  $conditions;
            return $this;
        }
        if (!is_array($conditions[0])) {
            throw new \InvalidArgumentException("Conditions must be a two dimentional array");
        }
        if (count($conditions) > 1) {
            //    foreach ($conditions as $c) {
            # code...
            $query['$and'] = iterator_to_array($this->mgoConditionGenerator($conditions));
        } else {
            //    $query = $this->parseConditions(array(), $conditions[0])["query"];
            $query = $this->parseConditions(array(), $conditions[0]);
        }
        $this->query = $query;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toDbPrimaryKeyType($value)
    {
        if ($value instanceof \MongoDB\BSON\ObjectId) {
            return $value;
        }
        return MongoBsonTypes::convertToMongoType(MongoBsonTypes::BSON_OBJECTID, $value);
    }

    /**
     * Generator function for converting an array of conditions
     *
     * @param array $conditions
     * @return \Traversable
     */
    protected function mgoConditionGenerator(array $conditions)
    {
        foreach ($conditions as $c) {
            # code...
            yield $this->parseConditions(array(), $c);
        }
    }

    /**
     * Try parsing the user provided query into a mongodb valid query
     *
     * @param array $query
     * @param array $conditions
     * @return array
     */
    protected function parseConditions(array $query, array $conditions)
    {
        $query = array();
        $counted_contions = count($conditions);
        if (($counted_contions === 2) && \is_string($conditions[1]) && Str::contains($conditions[1], array('|', ':'))) {
            $parsed_conditions = $this->parseComplexParams($conditions[1]);
            $query[$conditions[0]] = $parsed_conditions;
        } elseif ($counted_contions === 2) {
            $query = $this->parseSimpleParams($conditions[0], $conditions[1]);
        } elseif ($counted_contions === 3) {
            $query = $this->parseSimpleParams($conditions[0], $conditions[2], $conditions[1]);
        } else {
            throw new \InvalidArgumentException("Condition parameters are malformed");
        }
        return $query;
        //   return array("operator" => $operator, "query" => $query);
    }

    /**
     * Build a query from provided fields
     *
     * @param string $field
     * @param mixed $value
     * @param string $operator
     * @return void
     */
    public function parseSimpleParams($field, $value, $operator = null)
    {
        if (in_array($operator, MongoOperators::EVALUATION_OPERATORS)) {
            return array($field => array(MongoOperators::parse($operator) => MongoBsonTypes::convertToMongoType('regex', $value)));
        }
        // Try converting the value type to a bson type if the type is specified
        $operator = isset($operator) ? (MongoOperators::parse($operator)) : MongoOperators::parse(MongoOperators::DEFAUlT_OPERATOR);
        return array($field => array($operator => $this->parseCommandSeperatedValueTypeRelation($operator, $value)));
    }

    /**
     * Try parsing query defined with complex anotation into a valid mongo query language
     *
     * @param string $param
     * @return array
     */
    protected function parseComplexParams($param)
    {
        $parts = \explode('|', $param);
        if (is_array($parts)) {
            $value = null;
            foreach ($parts as $v) {
                # code...
                if (!is_string($v)) {
                    $value = $param;
                    break;
                }
                $v_parts = \explode(':', $v);
                if ((count($v_parts) === 1)) {
                    $operator = MongoOperators::parse(MongoOperators::DEFAUlT_OPERATOR);
                    $last_part = $v_parts[0];
                } elseif ((count($v_parts) === 2) && !empty($v_parts[1])) {
                    $operator = MongoOperators::parse(!empty($v_parts[0]) ? $v_parts[0] : MongoOperators::DEFAUlT_OPERATOR);
                    $last_part = $v_parts[1];
                } else {
                    throw new \InvalidArgumentException('Complex condition parameters are not properly formed');
                }
                $value[$operator] = $this->parseCommandSeperatedValueTypeRelation($operator, $last_part);
            }
            return $value;
        }
        return $param;
    }

    /**
     * Convert a string with the syntax "value,type" or "value,value,value" to an array based on the operator
     *
     * @param string $op
     * @param string $relation
     * @return void
     */
    private function parseCommandSeperatedValueTypeRelation($op, $relation)
    {
        if (is_string($relation) && Str::contains($relation, ',')) {
            $values = \explode(',', $relation);
            if (in_array($op, MongoOperators::MGO_LIST_MATCH_OPERATORS)) {
                $relation = $values;
            } else {
                $relation = isset($values[1]) ? (MongoBsonTypes::convertToMongoType($values[1], $values[0])) : $relation;
            }
        }
        return $relation;
    }

    /**
     * @inheritDoc
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function set($query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unsetQuery()
    {
        $this->query = null;
    }

    /**
     * @inheritDoc
     */
    public function combine($operator, $lhs, $rhs)
    {
        $this->query = array(MongoOperators::parse($operator) => array($lhs, $rhs));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOrOperator()
    {
        return MongoOperators::MGO_OR_OPERATOR;
    }

    /**
     * @inheritDoc
     */
    public function getAndOperator()
    {
        return MongoOperators::MGO_AND_OPERATOR;
    }

    /**
     * @inheritDoc
     */
    public function getInOperator()
    {
        return MongoOperators::MGO_IN_OPERATOR;
    }
}
