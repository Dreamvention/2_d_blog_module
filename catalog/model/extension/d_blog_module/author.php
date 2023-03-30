<?php

class ModelExtensionDBlogModuleAuthor extends Model {

	public function getAuthor($author_id) {
		$author_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bm_author a"
			. " LEFT JOIN " . DB_PREFIX . "bm_author_description ad "
			. "ON (a.author_id = ad.author_id) "
			. " LEFT JOIN " . DB_PREFIX . "user u "
			. "ON (a.user_id = u.user_id) "
			. "WHERE a.author_id = '" . (int) $author_id . "' AND ad.language_id = '".(int) $this->config->get('config_language_id')."'");

		foreach ($query->rows as $result) {
			$author_description_data= array(
				'author_id' => $result['author_id'],
				'name' => $result['name'],
				'description' => $result['description'],
				'short_description' => $result['short_description'],
				'meta_title' => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword' => $result['meta_keyword'],
				'image' => $result['image']
			);
		}
 
		return $author_description_data;
	}
	public function getAuthorByUserId($user_id) {
		$author_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bm_author a"
			. " LEFT JOIN " . DB_PREFIX . "bm_author_description ad "
			. "ON (a.author_id = ad.author_id) "
			. " LEFT JOIN " . DB_PREFIX . "user u "
			. "ON (a.user_id = u.user_id) WHERE a.user_id = '" . (int) $user_id . "' AND ad.language_id = '".(int) $this->config->get('config_language_id')."'");

		foreach ($query->rows as $result) {
			$author_description_data= array(
				'author_id' => $result['author_id'],
				'name' => $result['name'],
				'description' => $result['description'],
				'short_description' => $result['short_description'],
				'meta_title' => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword' => $result['meta_keyword'],
				'image' => $result['image']
			);
		}
 
		return $author_description_data;
	}
	
	public function editAuthor($author_id, $data) {
        if(!empty($data['description'])){
            foreach ($data['description'] as $language_id => $value) {
                $implode = array();

                if(isset($value['name'])){
                    $implode[] = "name='".$this->db->escape($value['name'])."'";
                }

                if(isset($value['description'])){
                    $implode[] = "description='".$this->db->escape($value['description'])."'";
                }

                if(count($implode) > 0){
                    $this->db->query("UPDATE " . DB_PREFIX . "bm_author_description SET ".implode(',', $implode)."
                    WHERE author_id = '". (int) $author_id."' AND language_id='". (int) $language_id."'");
                }
            }
        }
    }
}