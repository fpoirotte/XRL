<?php

class XRL_Node
{
    protected $_properties;

    public function __construct(XMLReader $reader)
    {
        $skipNodes = array(XMLReader::SIGNIFICANT_WHITESPACE);
        do {
            if (!$reader->read())
                throw new InvalidArgumentException('Unexpected end of document');
        } while (in_array($reader->nodeType, $skipNodes));

        $fields = array(
            'name',
            'nodeType',
            'value',
            'isEmptyElement',
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

    public function emptyNodeExpansionWorked()
    {
        if ($this->_properties['nodeType'] == XMLReader::ELEMENT &&
            $this->_properties['isEmptyElement'] == TRUE) {
            $this->_properties['nodeType'] = XMLReader::END_ELEMENT;
            $this->_properties['isEmptyElement'] = FALSE;
            return TRUE;
        }
        return FALSE;
    }
}

