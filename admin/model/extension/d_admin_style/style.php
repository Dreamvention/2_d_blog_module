<?php

class ModelExtensionDAdminStyleStyle extends Model {

    public function getStyles($theme_name){
        $this->document->addStyle('view/stylesheet/d_admin_style/core/normalize/normalize.css');
        $this->document->addStyle('view/stylesheet/d_admin_style/themes/'.$theme_name.'/styles.css');
    }
    public function getAvailableThemes(){
        $dir = DIR_APPLICATION . 'view/stylesheet/d_admin_style/themes';
        $name_dirs = scandir($dir);
        return  array_diff($name_dirs, array('.', '..'));
    }
    public function getLanguageText($data)
    {
        $this->language->load('extension/d_admin_style/style');
        $data['entry_admin_style'] = $this->language->get('entry_admin_style');
        return $data;
    }
}
