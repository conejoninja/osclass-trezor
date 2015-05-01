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
        $this->setFields( array('fk_i_user_id', 's_address', 'b_admin') );
    }

    public function install() {
        $this->import(TREZOR_PATH . 'struct.sql');
        osc_set_preference('logo', '', 'trezor', 'STRING');
        osc_set_preference('version', '120', 'trezor', 'INTEGER');
    }

    public function uninstall() {
        $this->dao->query(sprintf('DROP TABLE %s', $this->getTableName()) ) ;
        Preference::newInstance()->delete(array('s_section' => 'trezor'));
    }

    public function updateVersion() {
        $version = osc_get_preference('version', 'trezor');
        if($version<120) {
            osc_set_preference('version', 120, 'trezor');
            $this->dao->query(sprintf('ALTER TABLE %s ADD b_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER fk_i_user_id', $this->getTableName()));
            $this->dao->query(sprintf("ALTER TABLE `%s` DROP PRIMARY KEY ,ADD PRIMARY KEY ( `s_address`, `b_admin` )  ", $this->getTableName()));
            osc_reset_preferences();
        }
    }

    public function import($file)
    {
        $sql = file_get_contents($file);

        if(! $this->dao->importSQL($sql) ){
            throw new Exception( "Error importSQL::ModelTrezor<br>".$file ) ;
        }
    }

    public function findByUser($user_id, $b_admin = 0) {
        $this->dao->select('*') ;
        $this->dao->from($this->getTableName());
        $this->dao->where('fk_i_user_id', $user_id);
        $this->dao->where('b_admin', $b_admin);
        $result = $this->dao->get();
        if($result) {
            return $result->row();
        }
        return array('fk_i_user_id' => '', 's_address' => '');
    }

    public function findByAddress($address, $b_admin = 0) {
        $this->dao->select('*') ;
        $this->dao->from($this->getTableName());
        $this->dao->where('s_address', $address);
        $this->dao->where('b_admin', $b_admin);
        $result = $this->dao->get();
        if($result) {
            return $result->row();
        }
        return array('fk_i_user_id' => '', 's_address' => '');
    }

}