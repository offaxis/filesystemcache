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
        $this->_entity = strtolower($entity);
        $this->_id = $id;

        return $this->setKey()->setPath($path)->setContent();
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

    public function getTtl() {
        if( $this->getContent() && !$this->_ttl ) {
            $this->_ttl = $this->_content['ttl'];
        }
        return $this->_ttl;
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
         return $this->_content;
     }

     public function setContent() {
         if( $this->getPath() && !$this->_content ) {
             if( file_exists($this->getPath()) ) {
    			 $this->_content = unserialize(base64_decode(file_get_contents($this->getPath())));
    		 }
         }
     }




	/*
	 * Generate a specific key from $entity name and id
	 */
	public function getKey() {
        return $this->_key;
    }

    public function setKey() {
		$this->_key = md5($this->_entity.$this->_id);
        return $this;
	}


	/*
	 * Return if cache file is out of ttl
	 * By default : ttl is equal to config value
	 * if ttl == 0, ttl has not been activated for this file
	 */
	public function isValid() {
		return $this->getTtl() && ($this->getTtl() === 0 || $this->getTtl() > time());
	}


	/*
	 * Return path of cache file, depending on entity name, and cache key
	 */
    public function getPath() {
        return $this->_path;
    }

	public function setPath($basePath) {
		$prePath = $basePath . $this->_entity . '/' . $this->_key[0] . '/' . $this->_key[1];
		if( !is_dir($prePath) ) {
			mkdir($prePath, 0777, true);
		}
		$this->_path = $prePath . '/' . $this->_key;
		return $this;
	}

}

?>
