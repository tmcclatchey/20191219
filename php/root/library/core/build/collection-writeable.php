<?php
    abstract class CollectionWriteableBase extends CollectionBase
    {
        public function __construct()
        {
            parent::__construct();
        }
        #region " ArrayAccess "
        public function offsetSet ( $offset , $value )
        {
            $this->data[$offset] = $value;
        }
        public function offsetUnset ( $offset )
        {
            if (array_key_exists($offset, $this->data))
            {
                unset($this->data[$offset]);
            }
        }
        #endregion
    }
?>