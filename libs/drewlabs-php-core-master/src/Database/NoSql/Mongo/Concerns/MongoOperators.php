<?php

namespace Drewlabs\Core\Database\NoSql\Mongo\Concerns;

/*
|--------------------------------------------------------------------------
| Mongo Operators
|--------------------------------------------------------------------------
|
| Operators belows are mongodb query and projection operators with their
| respectives definitions. This class serve as matching concern between
| model queries operators and mongodb ones
|
|$eq    Matches values that are equal to a specified value.
|$gt    Matches values that are greater than a specified value.
|$gte    Matches values that are greater than or equal to a specified value.
|$in    Matches any of the values specified in an array.
|$lt    Matches values that are less than a specified value.
|$lte    Matches values that are less than or equal to a specified value.
|$ne    Matches all values that are not equal to a specified value.
|$nin    Matches none of the values specified in an array.
|
|--------------------------------------------------------------------------
| Logical
|--------------------------------------------------------------------------

|$and    Joins query clauses with a logical AND returns all documents that match the conditions of both clauses.
|$not    Inverts the effect of a query expression and returns documents that do not match the query expression.
|$nor    Joins query clauses with a logical NOR returns all documents that fail to match both clauses.
|$or    Joins query clauses with a logical OR returns all documents that match the conditions of either clause.
|
|--------------------------------------------------------------------------
| Element
|--------------------------------------------------------------------------
|$exists    Matches documents that have the specified field.
|$type    Selects documents if a field is of the specified type.
|
|--------------------------------------------------------------------------
| Evaluation
|--------------------------------------------------------------------------
|$expr    Allows use of aggregation expressions within the query language.
|$jsonSchema    Validate documents against the given JSON Schema.
|$mod    Performs a modulo operation on the value of a field and selects documents with a specified result.
|$regex    Selects documents where values match a specified regular expression.
|$text    Performs text search.
|$where    Matches documents that satisfy a JavaScript expression.
|
|--------------------------------------------------------------------------
| Geospatial
|--------------------------------------------------------------------------
|$geoIntersects    Selects geometries that intersect with a GeoJSON geometry. The 2dsphere index supports geoIntersects.
|$geoWithin    Selects geometries within a bounding GeoJSON geometry. The 2dsphere and 2d indexes support geoWithin.
|$near    Returns geospatial objects in proximity to a point. Requires a geospatial index. The 2dsphere and 2d indexes support near.
|$nearSphere    Returns geospatial objects in proximity to a point on a sphere. Requires a geospatial index. The 2dsphere and 2d indexes support nearSphere.
|
|--------------------------------------------------------------------------
| Geospatial
|--------------------------------------------------------------------------
|$all    Matches arrays that contain all elements specified in the query.
|$elemMatch    Selects documents if element in the array field matches all the specified $elemMatch conditions.
|$size    Selects documents if the array field is a specified size.
|
|--------------------------------------------------------------------------
| Bitwise
|--------------------------------------------------------------------------
|$bitsAllClear    Matches numeric or binary values in which a set of bit positions all have a value of 0.
|$bitsAllSet    Matches numeric or binary values in which a set of bit positions all have a value of 1.
|$bitsAnyClear    Matches numeric or binary values in which any bit from a set of bit positions has a value of 0.
|$bitsAnySet    Matches numeric or binary values in which any bit from a set of bit positions has a value of 1.
|
|--------------------------------------------------------------------------
| Projection Operators
|--------------------------------------------------------------------------
| $    Projects the first element in an array that matches the query condition.
| $elemMatch    Projects the first element in an array that matches the specified $elemMatch condition.
| $meta    Projects the document’s score assigned during $text operation.
| $slice    Limits the number of elements projected from an array. Supports skip and limit slices.
 */

class MongoOperators
{

 /**
  * Parse a user given operator to mongodb available operator
  *
  * @param string $operator
  * @return string
  */
    public static function parse($operator)
    {
        if (!is_string($operator)) {
            throw new \InvalidArgumentException("Invalid operator exception occurs, operator must be a valid PHP string");
        }
        if (in_array($operator, static::COMPARISON_OPERATORS)) {
            return static::parseComparisonOperator($operator);
        }
        if (in_array($operator, static::LOGICAL_OPERATORS)) {
            return static::parseLogicalOperator($operator);
        }
        if (in_array($operator, static::EVALUATION_OPERATORS)) {
            return static::parseEvaluationOperators($operator);
        }
        if (in_array($operator, static::ELEMENT_OPERATORS)) {
            return static::parseElementOperator($operator);
        }
    }

    /**
     * Convert user provided comparison operator to a mongodb comparison operator
     *
     * @param string $operator
     * @return string
     */
    public static function parseComparisonOperator($operator)
    {
        switch ($operator) {
   case '<>':
    return '$ne';
   case '=':
    return '$eq';
   case '>':
    return '$gt';
   case '>=':
    return '$gte';
   case '<':
    return '$lt';
   case '<=':
    return '$lte';
   case 'in':
    return '$in';
   case 'nin':
    return '$nin';
   default:
    throw new \InvalidArgumentException("$operator is not a valid comparison operator");
  }
    }

    /**
     * Convert user provided logical operator to a mongodb logical operator
     *
     * @param string $operator
     * @return string
     */
    public static function parseLogicalOperator($operator)
    {
        switch ($operator) {
   case '&&':
    return '$and';
   case '!':
    return '$not';
   case '||':
    return '$or';
   case 'nor':
    return '$nor';
   default:
    throw new \InvalidArgumentException("$operator is not a valid logical operator");
  }
    }

    /**
     * Convert user provided evaluation operator to a mongodb evaluation operator
     *
     * @param string $operator
     * @return string
     */
    public static function parseEvaluationOperators($operator)
    {
        switch ($operator) {
   case 'like':
    return '$regex';
   default:
    throw new \InvalidArgumentException("$operator is not a valid logical operator");
  }
    }

    /**
     * Convert user provided element operator to a mongodb element operator
     *
     * @param string $operator
     * @return string
     */
    public static function parseElementOperator($operator)
    {
        switch ($operator) {
   case 'exists':
    return '$exists';
   case 'type':
    return '$type';
   default:
    throw new \InvalidArgumentException("$operator is not a valid logical operator");
  }
    }

    const DEFAUlT_OPERATOR = '=';
    const MGO_OR_OPERATOR = '||';
    const MGO_AND_OPERATOR = '&&';
    const MGO_IN_OPERATOR = 'in';
    const MGO_LIST_MATCH_OPERATORS = array('$in', '$elemMatch', '$nin');
    const COMPARISON_OPERATORS = array('<>', '=', '>', '>=', '<', '<=', 'in', 'nin');
    const LOGICAL_OPERATORS = array('!', '||', '&&', 'nor');
    const EVALUATION_OPERATORS = array('like');
    const ELEMENT_OPERATORS = array('exists', 'type');
}
