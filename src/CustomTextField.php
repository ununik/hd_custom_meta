<?php
namespace HdCustomMeta;

class CustomTextField extends Field
{
    protected $_content = '';

    public function __construct( $field )
    {
        if ( isset( $field['content'] ) ) {
            $this->_content = $field['content'];
        }
    }

    public function output()
    {
        return $this->_content;
    }
}