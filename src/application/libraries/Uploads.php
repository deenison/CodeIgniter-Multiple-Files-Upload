<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Multi-File Uploading Class.
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Uploads
 * @author      Denison Martins <contact@denison.me>
 * @link        https://github.com/deenison/CodeIgniter-Multiple-Files-Upload
 */
class Uploads
{
    /**
     * Associative array listing all uploaded files and their raw post-upload data.
     *
     * @access protected
     *
     * @var    array
     */
    protected $_uploads = array();

    /**
     * Associative array containing all upload custom settings.
     *
     * @access protected
     *
     * @var    array
     */
    protected $_config = array();

    /**
     * Error messages list.
     *
     * @access protected
     *
     * @var    array
     */
    protected $_errMessages = array();

    /**
     * CI Singleton.
     *
     * @access protected
     *
     * @var    object
     */
    protected $_CI;

    /**
     * Class constructor.
     *
     * @param   array   $props
     * @return  void
     */
    public function __construct($config = array())
    {
        empty($config) OR $this->initialize($config, FALSE);

        $this->_CI =& get_instance();
        $this->_CI->load->library('upload');

        log_message('info', 'Uploads Class Initialized');
    }

    /**
     * Initialize preferences.
     *
     * @param   array   $config
     * @param   bool    $reset
     * @return  Uploads
     */
    public function initialize(array $config = array(), $reset = TRUE)
    {
        $reflection = new ReflectionClass($this);

        if ($reset === TRUE) {
            $defaults = $reflection->getDefaultProperties();
            foreach (array_keys($defaults) as $key) {
                if ($key[0] === '_') {
                    continue;
                }

                if (isset($config[$key])) {
                    if ($reflection->hasMethod('set_'. $key)) {
                        $this->{'set_'. $key}($config[$key]);
                    } else {
                        $this->key = $config[$key];
                    }
                } else {
                    $this->{$key} = $defaults[$key];
                }
            }
        } else {
            foreach ($config as $key => &$value) {
                if ($key[0] !== '_' && $reflection->hasProperty($key)) {
                    if ($reflection->hasMethod('set_'. $key)) {
                        $this->{'set_'. $key}($value);
                    } else {
                        $this->{$key} = $value;
                    }
                }
            }
        }

        $this->_config = $config;

        return $this;
    }

    /**
     * Set an error message.
     *
     * @param   string  $msg
     * @return  CI_Upload
     */
    public function set_error($msg, $log_level = 'error')
    {
        $this->_CI->lang->load('upload');

        is_array($msg) OR $msg = array($msg);
        foreach ($msg as $val) {
            $msg = ($this->_CI->lang->line($val) === FALSE) ? $val : $this->_CI->lang->line($val);

            $this->error_msg[] = $msg;

            log_message($log_level, $msg);
        }

        return $this;
    }

    /**
     * Perform the files upload.
     *
     * @param   string  $field
     * @return  bool
     */
    public function do_upload($field = 'userfile')
    {
        // Is $_FILES[$field] set? If not, no reason to continue.
        if (isset($_FILES[$field])) {
            $_file = $_FILES[$field];
        }
        // Does the field name contain array notation?
        elseif (($c = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $field, $matches)) > 1) {
            $_file = $_FILES;
            for ($i = 0; $i < $c; $i++) {
                // We can't track numeric iterations, only full field names are accepted
                if (($field = trim($matches[0][$i], '[]')) === '' OR ! isset($_file[$field])) {
                    $_file = NULL;
                    break;
                }

                $_file = $_file[$field];
            }
        }

        if (!isset($_file)) {
            $this->set_error('upload_no_file_selected', 'debug');

            return FALSE;
        }

        $filesToUploadList = array();
        // Check if we're dealing with multiple files
        if (is_string($_file['name'])) {
            array_push($filesToUploadList, $_file);
        } else {
            foreach ($_file['name'] as $fileIndex => $fileName) {
                array_push($filesToUploadList, array(
                    'name'     => $fileName,
                    'type'     => $_file['type'][$fileIndex],
                    'tmp_name' => $_file['tmp_name'][$fileIndex],
                    'error'    => $_file['error'][$fileIndex],
                    'size'     => $_file['size'][$fileIndex],
                ));
            }
            unset($fileName, $fileIndex);
        }

        $hasAnyErrors = FALSE;
        foreach ($filesToUploadList as $_file) {
            $upload = new MY_Upload($this->_config);

            if (!$upload->uploadFileObject($_file)) {
                $hasAnyErrors = TRUE;
                $this->_errMessages[$_file['name']] = $upload->error_msg[0];
            } else {
                $this->_uploads[$_file['name']] = $upload;
            }
        }

        return !$hasAnyErrors;
    }

    /**
     * Finalized Data Array.
     *
     * Returns an associative array containing all of the information
     * related to the upload, allowing the developer easy access in one array.
     *
     * @access  protected
     *
     * @param   string  $index
     * @return  mixed
     */
    protected function _getDataFromUpload($fileName)
    {
        if (empty($fileName) || !isset($this->_uploads[$fileName])) {
            return NULL;
        }

        $upload = $this->_uploads[$fileName];

        $data = array(
            'file_name'      => $upload->file_name,
            'file_type'      => $upload->file_type,
            'file_path'      => $upload->upload_path,
            'full_path'      => $upload->upload_path . $upload->file_name,
            'raw_name'       => str_replace($upload->file_ext, '', $upload->file_name),
            'orig_name'      => $upload->orig_name,
            'client_name'    => $upload->client_name,
            'file_ext'       => $upload->file_ext,
            'file_size'      => $upload->file_size,
            'is_image'       => $upload->is_image(),
            'image_width'    => $upload->image_width,
            'image_height'   => $upload->image_height,
            'image_type'     => $upload->image_type,
            'image_size_str' => $upload->image_size_str,
        );

        return $data;
    }

    /**
     * List of Finalized Data Arrays.
     *
     * Returns a list of associative arrays containing all of the information
     * related to the uploads, allowing the developer easy access any of them.
     *
     * @param   string  $index
     * @return  mixed
     */
    public function data($index = NULL, $fileName = "")
    {
        $validIndexes = array(
            'file_name', 'file_type', 'file_path', 'full_path', 'raw_name', 'orig_name', 'client_name', 'file_ext', 'file_size', 'is_image', 'image_width', 'image_height', 'image_type', 'image_size_str'
        );

        if (empty($this->_uploads) || (!empty($index) && !in_array($index, $validIndexes))) {
            return NULL;
        }

        if (!empty($fileName)) {
            return $this->_getDataFromUpload($fileName);
        }

        $data = array();
        foreach ($this->_uploads as $fileName => $upload) {
            $uploadData = $this->_getDataFromUpload($fileName);

            $data[] = $uploadData;
        }

        return $data;
    }

    /**
     * Get all upload errors.
     *
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->_errMessages;
    }

    /**
     * Display the error message.
     *
     * @param   string  $open
     * @param   string  $close
     * @return  string
     */
    public function display_errors($openTag = '<p>', $closeTag = '</p>')
    {
        $errorsString = "";

        $messages = $this->getErrorMessages();
        if (count($messages) > 0) {
            foreach ($messages as $fileName => $errorMessage) {
                $errorsString .= "{$openTag}\"<strong>{$fileName}</strong>\": {$errorMessage}{$closeTag}";
            }
        }

        return $errorsString;
    }
}
