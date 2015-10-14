<?php
namespace Ice\Validator;

use Ice\Core\Validator;

/**
 * Class Equal
 *
 * @see     Ice\Core\Validator
 * @package Ice\Validator;
 * @author  dp <email>
 */
class Equal extends Validator
{
    /**
     * Validate data by pattern
     *
     * example usage:
     * ```php
     *      $scheme = [
     *          'name' => [
     *              'Ice:Equal' => 'Vasya'
     *          ]
     *      ];
     * ```
     * or
     * ```php
     *      $scheme = [
     *          'name' => [
     *              'Ice:Equal' => [
     *                  'params' => 'Vasya'
     *                  'message => 'Not Vasya'
     *              ]
     *          ]
     *      ];
     * ```
     *
     * @param  $data
     * @param  mixed|null $scheme
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function validate($data, $scheme = null)
    {
        return in_array($data, (array)$scheme);
    }

    /**
     * Init object
     *
     * @param array $data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   2.0
     */
    protected function init(array $data)
    {
        // TODO: Implement init() method.
    }
}
