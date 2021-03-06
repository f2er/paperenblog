<?php

/**
 * 2012-3-18 17:34:28
 * 用戶模型
 * @author paperen<paperen@gmail.com>
 * @link http://iamlze.cn
 * @version 0.0
 * @package paperenblog
 * @subpackage application/models/
 */
class User_model extends CI_Model
{

    const EDITOR = 2;
    const ADMIN = 4;
    const USER = 1;

	/**
	 * 表映射
	 * @var array
	 */
	private $_tables = array(
		'user' => 'user',
	);

	/**
	 * 根據用戶名獲取用戶數據
	 * @param string $name 用戶名
	 * @return array
	 */
	public function get_by_name( $name )
	{
		return $this->db->select(
								'u.id,u.name,u.password,u.email,u.url,u.lastlogin,u.lastip,u.role,u.token,u.data'
						)
						->from( "{$this->_tables['user']} as u" )
						->where( 'u.name', $name )
						->get()
						->row_array();
    }

    /**
	 * 根據作者名獲取作者數據
	 * @param string $name 用戶名
	 * @return array
	 */
	public function get_author_by_name( $name )
	{
		return $this->db->select(
								'u.id,u.name,u.password,u.email,u.url,u.lastlogin,u.lastip,u.role,u.token,u.data'
						)
						->from( "{$this->_tables['user']} as u" )
						->where( 'u.name', $name )
                        ->where( 'u.role >=', self::EDITOR )
                        ->where( 'u.role !=', self::ADMIN )
						->get()
						->row_array();
    }

	/**
	 * 根據作者ID獲取作者數據
	 * @param string $id 用戶ID
	 * @return array
	 */
	public function get_author_by_id( $id )
	{
		return $this->db->select(
								'u.id,u.name,u.password,u.email,u.url,u.lastlogin,u.lastip,u.role,u.token,u.data'
						)
						->from( "{$this->_tables['user']} as u" )
						->where( 'u.id', $id )
                        ->where( 'u.role >=', self::EDITOR )
                        ->where( 'u.role !=', self::ADMIN )
						->get()
						->row_array();
    }

	/**
	 * 根据用户ID获取用户数据
	 * @param int $user_id 用户ID
	 * @return array
	 */
	public function get_by_id( $user_id )
	{
		return $this->db->select(
								'u.id,u.name,u.password,u.email,u.url,u.lastlogin,u.lastip,u.role,u.token,u.data'
						)
						->from( "{$this->_tables['user']} as u" )
						->where( 'u.id', $user_id )
						->get()
						->row_array();

	}

	/**
	 * 獲取所有作者數據
	 * @param int $per_page
	 * @param int $offset
	 * @return array
	 */
    public function get_all_author( $per_page = 0, $offset = 0 )
    {
        return $this->db->select(
								'u.id,u.name,u.email,u.url,u.lastlogin,u.lastip,u.role,u.data'
						)
						->from( "{$this->_tables['user']} as u" )
						->where( 'u.role >=', self::EDITOR )
                        ->where( 'u.role !=', self::ADMIN )
						->get()
						->result_array();
    }

	/**
	 * 所有用戶數據
	 * @param int $per_page
	 * @param int $offset
	 * @return array
	 */
	public function get_all( $per_page = 0, $offset = 0 )
	{
		return $this->db->select(
								'u.id,u.name,u.email,u.url,u.lastlogin,u.lastip,u.role,u.token,u.data'
						)
						->from( "{$this->_tables['user']} as u" )
						->order_by( 'u.id', 'desc' )
						->get()
						->result_array();
	}

	/**
	 * 更新最近登陸時間與IP
	 * @param array $data
	 * @param int $userid
	 * @return int
	 */
	public function update_lastlogin( $data, $userid )
	{
		$update_data = array(
			'lastlogin' => $data['lastlogin'],
			'lastip' => $data['lastip'],
		);
		$this->db->where( 'id', $userid )
				->update( $this->_tables['user'], $update_data );
		return $this->db->affected_rows();
	}

	public function update_token( $token, $userid )
	{
		$update_data = array(
			'token' => $token,
		);
		$this->db->where( 'id', $userid )
				->update( $this->_tables['user'], $update_data );
		return $this->db->affected_rows();
	}

	public function insert( $data )
	{
		$insert_data = array(
			'name' => $data['name'],
			'url' => $data['url'],
			'email' => $data['email'],
			'password' => md5( $data['password'] ),
			'lastlogin' => isset( $data['lastlogin'] ) ? $data['lastlogin'] : '',
			'lastip' => isset( $data['lastip'] ) ? $data['lastip'] : '',
			'role' => $data['role'],
			'token' => isset( $data['token'] ) ? $data['token'] : '',
			'data' => isset( $data['data'] ) ? $data['data'] : '',
		);
		$this->db->insert( $this->_tables['user'], $insert_data );
		return $this->db->insert_id();
	}

	/**
	 * 更新指定用户ID的用户数据
	 * @param array $data
	 * @param int $user_id
	 * @return int 影响行数
	 */
	public function update( $data, $user_id )
	{
		$update_data = array(
			'url' => $data['url'],
			'email' => $data['email'],
			'token' => isset( $data['token'] ) ? $data['token'] : '',
			'data' => isset( $data['data'] ) ? $data['data'] : '',
		);
		if ( isset( $data['role'] ) ) $update_data['role'] = $data['role'];
		if ( isset( $data['name'] ) ) $update_data['name'] = $data['name'];
		if( $data['password'] ) $update_data['password'] = md5( $data['password'] );
		$this->db->where('id', $user_id)
				->update( $this->_tables['user'], $update_data );
		return $this->db->affected_rows();
	}

	/**
	 * 用戶總數
	 * @return int
	 */
	public function total()
	{
		return $this->db->count_all_results( $this->_tables['user'] );
	}

}

// end of Tag_model
