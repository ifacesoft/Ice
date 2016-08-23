<?php
/**
 * Ice core model child abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

/**
 * Class Model_Child
 *
 * Core model child abstract class
 *
 * @see \Ice\Core\Model
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since   0.0
 */
abstract class Model_Child extends Model
{
    /**
     * Return query for get root Model
     *
     * @return QueryResult
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getRoots()
    {
        return Query::getBuilder(self::getClass())
            ->isNull('/_fk')
            ->getSelectQuery('*');
    }
}
