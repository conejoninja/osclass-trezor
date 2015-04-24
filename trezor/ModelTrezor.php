<?php

class ModelTrezor extends DAO
{

    private static $instance ;
    public static function newInstance() {
        if( !self::$instance instanceof self ) {
            self::$instance = new self ;
        }
        return self::$instance ;
    }

    function __construct() {
        parent::__construct();
        $this->setTableName('t_trezor');
        $this->setPrimaryKey('s_address');
        $this->setFields( array('fk_i_user_id', 's_address') );
    }

    public function install() {
        $this->import(TREZOR_PATH . 'struct.sql');
        osc_set_preference('logo', '', 'trezor', 'STRING');
    }

    public function uninstall() {
        $this->dao->query(sprintf('DROP TABLE %s', $this->getTableName()) ) ;
        Preference::newInstance()->delete(array('s_section' => 'trezor'));
    }

    public function import($file)
    {
        $sql = file_get_contents($file);

        if(! $this->dao->importSQL($sql) ){
            throw new Exception( "Error importSQL::ModelTrezor<br>".$file ) ;
        }
    }

    public function findByUser($user_id) {
        $this->dao->select('*') ;
        $this->dao->from($this->getTableName());
        $this->dao->where('fk_i_user_id', $user_id);
        $result = $this->dao->get();
        if($result) {
            return $result->row();
        }
        return array('fk_i_user_id' => '', 's_address' => '');
    }

}