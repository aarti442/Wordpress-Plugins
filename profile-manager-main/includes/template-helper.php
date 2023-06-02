<?php
class TemplateHelper {

    protected $template_dir = 'templates/';

    public function __construct($template_dir = null) {
        if ($template_dir !== null) {
            // you should check here if this dir really exists
            $this->template_dir = $template_dir;
        }
    }

    /**
	 * Function to display the content template file
	 * @since    1.0.0
     * @param  $template_file
     * @param  $data
     * @return 
	 */

    
    public function render($template_file, $data = array()) {
        if (file_exists($this->template_dir.$template_file)) {
            foreach ($data as $key => $value) {
                ${$key} = $value;
            }
            include $this->template_dir.$template_file;
        } else {
            throw new Exception('no template file ' . $template_file . ' present in directory ' . $this->template_dir);
        }
    }

}