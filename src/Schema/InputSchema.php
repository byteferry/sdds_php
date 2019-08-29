<?php

/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Schema;


/**
 * Class InputSchema
 * @package Sdds\Schema
 */
class InputSchema extends Schema
{

    /**
     * InputSchema constructor.
     * @param \Sdds\DataDiagram\DataNode $data_node
     */
    public function __construct($channel_name,$data_node)
    {
        parent::__construct($channel_name,$data_node);
    }


    /**
     * @param string $schema_file
     * @return $this
     * @throws \Sdds\Exceptions\SchemaException
     */
    public function init($schema_file){

        parent::init($schema_file);

        if(isset($this->schema_definition['sdds']['decode'])){
            $this->type_list = $this->schema_definition['sdds']['decode'];
        }else{
            $this->type_list = $this->schema_definition['sdds'];
        }

        parent::setTopNode();

    }

}