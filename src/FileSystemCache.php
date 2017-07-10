<?php

namespace OffAxis;

class FileSystemCache {

    protected $_path    = '';
    protected $_key     = '';
    protected $_content = null;
    protected $_entity  = '';
    protected $_id      = 0;
    protected $_ttl     = '';
    protected $_data    = null;


    public function __construct($path, $entity, $id) {
        $this->_entity = $entity;
        $this->_id = $id;

        return $this->setKey()->setPath($path);
    }


    /*
     * Return $data stored in file
     */
    public function getData() {
		if( $this->getContent() && !$this->_data ) {
            $this->_data = $this->_content['data'];
        }
        return $this->_data;
	}


	public function store($data, $ttl) {
		$content = base64_encode(serialize(array(
			'entity' 	=> $this->_entity,
			'id'		=> $this->_id,
			'ttl'		=> $ttl,
			'data'		=> $data
		)));

		return file_put_contents($this->_path, $content) !== false;

	}


 	/*
	 * Return the content of the file
	 * array(
	 *		'entity' 	=> 'User',
	 *		'id'		=> 1,
	 *		'ttl'		=> 123456789,
	 *		'data'		=> array('id' => 1, 'label' => 'abc123', ....)
	 * )
	 */
     public function getContent() {
         if( $this->getPath() && !$this->_content ) {
             if( file_exists($this->getPath()) ) {
    			 $this->_content = unserialize(base64_decode(file_get_contents($this->getPath())));
    		 }
         }
         return $this->_content;
     }




	/*
	 * Generate a specific key from $entity name and id
	 */
	public function getKey() {
        return $this->_key;
    }

    public function setKey() {
		$this->_key = md5($this->_entity.$this->_$id);
        return $this;
	}


	/*
	 * Return if cache file is out of ttl
	 * By default : ttl is equal to config value
	 * if ttl == 0, ttl has not been activated for this file
	 */
	public function isValid() {
		return $this->_ttl === 0 || $this->_ttl > time();
	}


	/*
	 * Return path of cache file, depending on entity name, and cache key
	 */
    public function getPath() {
        return $this->_path;
    }

	public function setPath($basePath) {
		$this->_path = $basePath . $this->_entity . '/' . $this->_key[0] . '/' . $this->_key[1] . '/' . $this->_key;
        return $this;
	}
}

?>
