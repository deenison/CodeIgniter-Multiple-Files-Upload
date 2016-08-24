# CodeIgniter-Multiple-Uploads
Extend CodeIgniter' Upload library to support multiple file uploads through one HTML input-field.

---

## Installation
- [Download](https://github.com/deenison/CodeIgniter-Multiple-Files-Upload/releases) the latest stable package;
- Move both files from `/src` (`MY_Uploads.php`* and `Uploads.php`) to `<your application folder>/libraries`.

\* The `MY_` prefix should reflect your `subclass_prefix` configuration.
So, just as an example, if in here `/application/config/config.php` you've changed from
```
$config['subclass_prefix'] = 'MY_';
```
to
```
$config['subclass_prefix'] = 'FOO_';
```
you must rename the `MY_Uploads.php` file to `FOO_Uploads.php`, otherwise CI won't be able to override its library properly.

---

## Basic Usage Instructions
If you do not know the basics of how to upload a file using CI you can check out the [official documentation here](https://www.codeigniter.com/userguide3/libraries/file_uploading.html). There's also a running example in the [demo](https://github.com/deenison/CodeIgniter-Multiple-Files-Upload/tree/master/demo) folder using CI v3.1.0.

Basically, CI' `Upload` library serves us as an instance of a single upload and its data. So what this `Uploads` class do is to wrap that functionality in such way that it handles multiple `Upload` instances sharing the same settings.

Let's assume we have this form in a view:
```
<form action="/uploadHandler" method="POST" enctype="multipart/form-data">
    <label>
        Multiple photos:
        <input type="file" name="photos[]" accept="image/*" multiple>
    </label>
    <button type="submit">Submit</button>
</form>
```

and in our handler inside the controller:
```
// Check if any files were selected in the field `photos`.
if ($_FILES['photos']['error'][0] !== UPLOAD_ERR_NO_FILE) {
    // Define the settings that will be used against all files.
    $myUploadSettings = array(
        'upload_path'   => dirname(BASEPATH) ."/uploads/",
        'allowed_types' => "jpg|png",
        'min_width'     => 450
    );

    // Load the library with our settings.
    $this->load->library('uploads', $myUploadSettings);

    // Attempt to upload all files.
    $uploadedData = array();
    $uploadErrorsList = array();
    if (!$this->uploads->do_upload('photos')) {
        // Retrieve all errors in a single string.
        $uploadErrorsString = $this->uploads->display_errors();
        // Retrieve an associative array containing all errors separated by the files in which their occurred  (as fileName => errMessage).
        $uploadErrorsList = $this->uploads->getErrorMessages();
    } else {
        // All files were uploaded successfully.
    }

    // Retrieve an associative array containing some data from all files that were uploaded successfully (as fileName => fileData).
    $uploadedData = $this->uploads->data();

    // Check if any files were uploaded successfully.
    if (count($uploadedData) > 0) {
        // Yay, at least one file was uploaded!
    } else {
        // No files were uploaded.
    }

    // Check and handle errors that may occurred.
    if (count($uploadErrorsList) > 0) {
        // Damn, let's handle these errors.
    } else {
        // Yay, no errors!
    }
} else {
    // No files were selected.
}
```

---
## Compatibility
Tested in CodeIgniter v3.x

---
## FAQ
##### Can I use `Uploads` to handle single uploads?

Definitely! Just keep in mind that `$this->uploads->data()` will ALWAYS return an array listing associative arrays.
Let's compare the "old" and "new" ways to do so and that we uploaded an image named `myProfilePic.jpeg`.

Retrieve file data using CI default `Upload` library:
```
$uploadData = $this->upload->data();
/*
$uploadData = array(
    'filename' => "myProfilePic.jpeg",
    'width'    => 200,
    'height'   => 200,
    ...
);
*/
```

Now let's achieve the same thing using the `Uploads` library:
```
$uploadsData = $this->uploads->data();
$uploadData = $uploadsData[0];
/*
$uploadsData = array(
    0 => array(
        'filename' => "myProfilePic.jpeg",
        'width'    => 200,
        'height'   => 200,
        ...
    )
);
*/
```

---
## Feedback
Any feedback, feature requests, pull requests, bug reporting etc are wellcome.\
You can also [create an issue](https://github.com/deenison/CodeIgniter-Multiple-Files-Upload/issues).

---
## License
[MIT License](https://github.com/deenison/CodeIgniter-Multiple-Files-Upload/blob/master/LICENSE)\
Copyright (c) 2016, [Denison Martins](http://denison.me/en).
