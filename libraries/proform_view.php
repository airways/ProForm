<?php


class PF_View extends View {
    public function __construct(View $view)
    {
        $this->EE =& get_instance();
        $this->set_cp_theme($view->_theme);
    }
    
    public function head_title($title)
    {
        return parent::head_title(strip_tags($title));
    }
}