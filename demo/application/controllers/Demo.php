<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Demo extends CI_Controller {
	/**
	 * Handles GET requests.
	 *
	 * @return void
	 */
	public function index($data = array())
	{
		$this->load->view('demo', $data);
	}

	/**
	 * Handles POST requests.
	 *
	 * @return void
	 */
	public function post()
	{
		$myUploadSettings = array(
			'upload_path'   => dirname(BASEPATH) ."/uploads/",
			'allowed_types' => "jpg|png",
			'max_width'     => 200
		);

		$viewData = array(
			'error' => "",
			'data'  => array()
		);

		// Check if the field `photo` wasn't leave empty.
		if ($_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
			// Load the upload library configured with our settings.
			$this->load->library('upload', $myUploadSettings);

			// Attempt to upload the file.
			if (!$this->upload->do_upload('photo')) {
				// Fetch all reported errors.
				$viewData['error'] = $this->upload->display_errors();
			} else {
				// Get the uploaded file data.
				$viewData['data'] = $this->upload->data();
			}
		} else {
			// No file was selected.
		}

		// Check if the field `photos` wasn't leave empty.
		if ($_FILES['photos']['error'][0] !== UPLOAD_ERR_NO_FILE) {
			// Load the multi-upload library configured with our settings.
			$this->load->library('uploads', $myUploadSettings);

			// Attempt to upload the file.
			if (!$this->uploads->do_upload('photos')) {
				// Fetch all reported errors.
				$viewData['error'] = $this->uploads->display_errors();
			} else {
				// Get the uploaded files data.
				$viewData['data'] = $this->uploads->data();
			}
		} else {
			// No file was selected.
		}

		$this->index($viewData);
	}
}
