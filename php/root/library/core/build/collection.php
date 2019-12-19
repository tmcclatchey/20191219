<?php
    abstract class CollectionBase implements IteratorAggregate, ArrayAccess 
    {
        protected $data;
        public function __construct()
        {
            $this->data = array();
        }

        public function Count()
        {
            return count($this->data);
        }

        #region " IteratorAggregate "
        public function getIterator()
        {
            if (!is_array($this->data))
            {
                $this->data = array();
            }
            foreach ($this->data as $item => $key)
            {
                yield $item => $key;
            }
        }
        #endregion
        
        #region " ArrayAccess "
        public function offsetExists ( $offset )
        {
            return array_key_exists($offset, $this->data);
        }
        public function offsetGet ( $offset )
        {
            if (!$this->offsetExists($offset))
            {
                $this->data[$offset] = null;
            }
            return $this->data[$offset];
        }
        public function offsetSet ( $offset , $value )
        {
            throw new ReadOnlyException('Collection', $offset);
        }
        public function offsetUnset ( $offset )
        {
            throw new ReadOnlyException('Collection', $offset);
        }
        public function __isset($offset)
        {
            return $this->offsetExists($offset);
        }
        public function __get($offset)
        {
            return $this->offsetGet($offset);
        }
        public function __set($offset, $value)
        {
            return $this->offsetSet($offset, $value);
        }
        public function __unset($offset)
        {
            return $this->offsetUnset($offset);
        }
        #endregion

        #region " Serializable "
        public function Encode()
        {
            if (!is_array($this->data))
            {
                $this->data = array();
            }
            return json_encode($this->data);
        }
        public function Decode($encodedData)
        {
            if (!is_array($this->data))
            {
                $this->data = array();
            }
            $result = json_decode($encodedData, true);
            if (!is_array($result))
            {
                throw new DecodingFailedException();
            }
            $this->data = $result;
        }
        #endregion
    }
?>