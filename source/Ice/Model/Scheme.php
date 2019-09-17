<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Scheme
 *
 * @property mixed scheme_pk
 * @property mixed table__json
 * @property mixed columns__json
 * @property mixed references__json
 * @property mixed indexes__json
 * @property mixed revision
 *
 * @see Ice\Core\Model
 *
 * @package Ice\Model
 */
class Scheme extends Model
{
    protected static function config()
    {
        return [
		    'dataSourceKey' => 'Ice\Data\Source\Mysqli/default.test',
		    'scheme' => [
		        'tableName' => 'ice_scheme',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
		        'comment' => '',
		    ],
		    'columns' => [
		        'table_name' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(255)',
		                'dataType' => 'varchar',
		                'length' => '255',
		                'characterSet' => 'utf8',
		                'nullable' => false,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'table_name',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 255,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'revision' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(12)',
		                'dataType' => 'varchar',
		                'length' => '12',
		                'characterSet' => 'utf8',
		                'nullable' => false,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'revision',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 12,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'table__json' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(1023)',
		                'dataType' => 'varchar',
		                'length' => '1023',
		                'characterSet' => 'utf8',
		                'nullable' => false,
		                'default' => '[]',
		                'comment' => '',
		            ],
		            'fieldName' => 'table__json',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 1023,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'columns__json' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'text',
		                'dataType' => 'text',
		                'length' => '65535',
		                'characterSet' => 'utf8',
		                'nullable' => false,
		                'default' => '[]',
		                'comment' => '',
		            ],
		            'fieldName' => 'columns__json',
		            'Ice\Core\Widget_Form' => 'Textarea',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 65535,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'references__json' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(1023)',
		                'dataType' => 'varchar',
		                'length' => '1023',
		                'characterSet' => 'utf8',
		                'nullable' => false,
		                'default' => '[]',
		                'comment' => '',
		            ],
		            'fieldName' => 'references__json',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 1023,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'indexes__json' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(1023)',
		                'dataType' => 'varchar',
		                'length' => '1023',
		                'characterSet' => 'utf8',
		                'nullable' => false,
		                'default' => '[]',
		                'comment' => '',
		            ],
		            'fieldName' => 'indexes__json',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 1023,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		    ],
		    'indexes' => [
		        'PRIMARY KEY' => [
		            'PRIMARY' => [],
		        ],
		        'FOREIGN KEY' => [],
		        'UNIQUE' => [],
		    ],
		    'references' => [],
		    'relations' => [
		        'oneToMany' => [],
		        'manyToOne' => [],
		        'manyToMany' => [],
		    ],
		    'revision' => '05151616_ees',
		];
    }
}