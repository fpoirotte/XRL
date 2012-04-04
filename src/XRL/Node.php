<?php

class XRL_Node
{
    protected $_properties;

    public function __construct(XMLReader $reader)
    {
        if (!$reader->read())
            throw new InvalidArgumentException('Unexpected end of document');

        $fields = array(
            'name',
            'nodeType',
            'value',
        );

        $this->_properties = array();
        foreach ($fields as $field)
            $this->_properties[$field] = $reader->$field;
    }

    public function __get($field)
    {
        if (!isset($this->_properties[$field]))
            throw new UnexpectedValueException("Unknown property '$field'");

        return $this->_properties[$field];
    }
}

