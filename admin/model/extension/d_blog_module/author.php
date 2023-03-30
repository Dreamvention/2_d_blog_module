<?php

class ModelExtensionDBlogModuleAuthor extends Model {

    public function addAuthor($data) {
	    
	(VERSION >= '2.1.0.0') 
        ?$saltTok = $this->db->escape($salt = token(9))
        :$saltTok = $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9));
	    
        $user_id = (int) $data['user_id'];
        if(!empty($data['user_id']))
        {
            $this->db->query("UPDATE `" . DB_PREFIX . "user` SET 
                username = '" . $this->db->escape($data['username']) . "', 
                user_group_id = '" . $this->db->escape($data['user_group_id']) . "', 
                firstname = '" . $this->db->escape($data['firstname']) . "', 
                lastname = '" . $this->db->escape($data['lastname']) . "', 
                image = '" . $this->db->escape($data['image']) . "' 
                WHERE user_id = '" . (int) $data['user_id'] . "'");

            if ($data['password']) {
                $this->db->query("UPDATE `" . DB_PREFIX . "user` SET 
                    salt = '". $saltTok . "', 
                    password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' 
                    WHERE user_id = '" .(int)$data['user_id'] . "'");
            }
        }
        else
        {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "user` SET 
                username = '" . $this->db->escape($data['username']) .  "', 
                user_group_id = '" . $this->db->escape($data['user_group_id']) . "', 
                salt = '" . $saltTok . "', 
                password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', 
                firstname = '" . $this->db->escape($data['firstname']) . "', 
                lastname = '" . $this->db->escape($data['lastname']) . "', 
                image = '" . $this->db->escape($data['image']). "', 
                date_added = NOW(), status=1");
            $user_id = $this->db->getLastId();
        }

        $this->db->query("INSERT INTO `" . DB_PREFIX . "bm_author` SET 
            user_id = '" .(int)$user_id  . "', 
            author_group_id = '" . (int)$data['author_group_id'] . "'");

        $author_id = $this->db->getLastId();

        foreach ($data['author_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "bm_author_description 
                SET author_id = '" . (int) $author_id
                . "', language_id = '" . (int) $language_id
                . "', name = '" . $this->db->escape($value['name'])
                . "', description = '" . $this->db->escape($value['description'])
                . "', short_description = '" . $this->db->escape($value['short_description'])
				. "', meta_title = '" . $this->db->escape($value['meta_title'])
				. "', meta_description = '" . $this->db->escape($value['meta_description'])
				. "', meta_keyword = '" . $this->db->escape($value['meta_keyword'])	. "'");
        }

        return $author_id;
    }

    public function getAuthorByUserId($user_id) {
        $query = $this->db->query("SELECT author_id, author_group_id, user_id FROM " . DB_PREFIX . "bm_author WHERE user_id = '" . (int) $user_id . "'");

        $author_data=array();

        if(!empty($query->rows))
        {
            $author_data = array(
                'author_id' => $query->rows[0]['author_id'],
                'author_group_id' => $query->rows[0]['author_group_id'],
                'user_id' => $query->rows[0]['user_id']);
        }

        return $author_data;
    }

    public function editAuthor($author_id, $data) {
        $this->db->query("UPDATE `" . DB_PREFIX . "bm_author` SET 
            user_id = '" . (int) $data['user_id'] . "', 
            author_group_id = '" . (int) $data['author_group_id'] . "' 
            WHERE author_id = '" . (int) $author_id . "'");

        if(!empty($data['user_id']))
        {
            $this->db->query("UPDATE `" . DB_PREFIX . "user` SET 
                username = '" . $this->db->escape($data['username']) . "', 
                firstname = '" . $this->db->escape($data['firstname']) . "', 
                lastname = '" . $this->db->escape($data['lastname']) . "', 
                user_group_id = '" . $this->db->escape($data['user_group_id']) . "', 
                image = '" . $this->db->escape($data['image']) . "' 
                WHERE user_id = '" . (int)$data['user_id'] . "'");

            if ($data['password']) {
                if (VERSION > '2.3.0.2'){
                    $this->db->query("UPDATE `" . DB_PREFIX . "user` SET 
                    salt = '" . $this->db->escape($salt = token(9)) . "', 
                    password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' 
                    WHERE user_id = '" .(int)$data['user_id'] . "'");
                }else{
                    $this->db->query("UPDATE `" . DB_PREFIX . "user` SET 
                    salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', 
                    password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' 
                    WHERE user_id = '" .(int)$data['user_id'] . "'");
                }
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "bm_author_description WHERE author_id = '" . (int) $author_id . "'");
        foreach ($data['author_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "bm_author_description SET 
                language_id = '" . (int) $language_id . "', 
                name = '" . $this->db->escape($value['name']) . "', 
                author_id = '" . (int) $author_id . "', 
                description = '" . $this->db->escape($value['description']) . "', 
                short_description = '" . $this->db->escape($value['short_description']) . "',
				meta_title = '" . $this->db->escape($value['meta_title']) . "',
				meta_description = '" . $this->db->escape($value['meta_description']) . "',
				meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
        }
    }

    public function getNewUser($filter_data = array()) {
        $sql = "SELECT user_id, username, image, firstname, lastname, user_group_id FROM " . DB_PREFIX . "user WHERE user_id NOT IN( SELECT user_id FROM " . DB_PREFIX . "bm_author)";
        if (!empty($filter_data['filter_name'])) {
            $sql .= " AND username LIKE '" . $this->db->escape($filter_data['filter_name']) . "%'";
        }
        
        $query = $this->db->query($sql);
        return $query->rows;

    }

    public function deleteAuthor($author_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "bm_author WHERE author_id = '" . (int) $author_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bm_author_description WHERE author_id = '" . (int) $author_id . "'");
    }

    public function getAuthorDescriptions($author_id) {
        $author_description_data = array();

        $query = $this->db->query("SELECT * "
            . "FROM " . DB_PREFIX . "bm_author a LEFT JOIN " . DB_PREFIX . "bm_author_description ad ON (a.author_id = ad.author_id) "
            . " WHERE a.author_id = '" . (int) $author_id . "'");

        foreach ($query->rows as $result) {
            $author_description_data[$result['language_id']] = array(
                'name' => $result['name'],
                'description' => $result['description'],
                'short_description' => $result['short_description'],
				'meta_title' => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword' => $result['meta_keyword']
                );
        }

        return $author_description_data;
    }

    public function getAuthor($author_id) {
        $query = $this->db->query("SELECT a.author_id, a.author_group_id, a.user_id, u.image " 
        . "FROM " . DB_PREFIX . "bm_author a LEFT JOIN " . DB_PREFIX . "user u "
        . "ON (a.user_id = u.user_id) WHERE author_id = '" . (int) $author_id . "'");

        $author_data = array(
            'user_id' => $query->rows[0]['user_id']
        );

        return $query->row;
    }

    public function getAuthors($data = array()) {
        $sql = "SELECT a.author_id, ad.name, ad.description, ad.short_description, a.user_id "
        . "FROM " . DB_PREFIX . "bm_author a "
        . "LEFT JOIN " . DB_PREFIX . "bm_author_description ad "
        . "ON (a.author_id = ad.author_id) "
        . "WHERE ad.language_id = '" . (int) $this->config->get('config_language_id') . "' ";

        if (!empty($data['filter_name'])) {
            $sql .= " AND a.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sort_data = array(
            'ad.name'
            );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $this->db->escape($data['sort']);

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function hasPermission($permission) {
        $user_id = $this->user->getId();
        $author = $this->getAuthorByUserId($user_id);

        if (empty($author)) {
            return true;
        }

        $author_info = $this->getAuthor($author['author_id']);

        $this->load->model('extension/d_blog_module/author_group');
        $author_group = $this->model_extension_d_blog_module_author_group->getAuthorGroup($author_info['author_group_id']);
        if(!empty($author_group['permission']) && in_array($permission,$author_group['permission']))
        {
            return true;
        }
        else {
            return false;
        }
    }

    public function getTotalAuthors($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "bm_author a "
            . "LEFT JOIN " . DB_PREFIX . "bm_author_description ad "
            . "ON (a.author_id = ad.author_id) "
            . "WHERE ad.language_id = '" . (int) $this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND r.status = '" . (int) $data['filter_status'] . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
}
