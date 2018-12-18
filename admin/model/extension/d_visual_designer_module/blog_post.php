<?php

class ModelExtensionDVisualDesignerModuleBlogPost extends Model
{
    public function getPosts($data = array())
    {

        $sql = "SELECT p.post_id ,pd.title";


        if (!empty($blog_category)) {
            $categories = join("','", $blog_category);
            $sql .= " FROM " . DB_PREFIX . "bm_post_to_category p2c
            LEFT JOIN " . DB_PREFIX . "bm_post p ON(p2c.post_id = p.post_id AND p2c.category_id IN ('" . $categories . "'))";
        } else {
            $sql .= " FROM " . DB_PREFIX . "bm_post p ";
        }

        $sql .= " LEFT JOIN " . DB_PREFIX . "bm_post_description pd ON (p.post_id = pd.post_id) "
            . "LEFT JOIN " . DB_PREFIX . "bm_post_to_store p2s ON (p.post_id = p2s.post_id) "
            . "Left JOIN " . DB_PREFIX . "bm_review r ON (p.post_id = r.post_id) "
            . "WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' "
            . "AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' "
            . "AND p.status = '1'" . " AND p.date_published < NOW()";
        if (!empty($data['posts'])) {
            $sql .= "AND p.post_id IN ('" . implode("', '", $data['posts']) . "')";
        }

        $sql .= " GROUP BY p.post_id";

        $sort_data = array(
            'p.date_published',
            'p.viewed'
        );
        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.date_published";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }


}
