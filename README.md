# CodeIgniter-Multiple-Uploads
Extend CodeIgniter' Upload library to support multiple file uploads through one HTML input-field.

## Installation
- Download the `src` files;
- Move both files (`MY_Uploads.php`* and `Uploads.php`) to the folder `libraries` inside your `application` (or whatever you've named it) folder.

\* The `MY_` prefix should reflect your `subclass_prefix` configuration.
So, just as an example, if in here `/application/config/config.php` you've changed from
```
$config['subclass_prefix'] = 'MY_';
```
to
```
$config['subclass_prefix'] = 'YAY_';
```
you must rename the `MY_Uploads.php` file to `YAY_Uploads.php`, otherwise CI won't be able to override the `Upload` library properly.

## Usage
The usage is simple and simillar to the default one. You can checkout a "full" example running undo CI v.3.0.4 in the `demo` folder.

Basically, you'll need a `form`, a `route` and a `controller`.

The `form`:
```
<form action="/uploadHandler" method="POST" enctype="multipart/form-data">
    <label>
        Single photo:
        <input type="file" name="photo" accept="image/*">
    </label>
    <label>
        Multiple photos:
        <input type="file" name="photos[]" accept="image/*" multiple>
    </label>
    <button type="submit">Submit</button>
</form>
```

The `route`:
```
$route['uploadHandler'] = "UploadController/uploadHandler";
```

The `controller` (in this example `UploadController.php`):
```

```
If we want to upload only a single file, we may use the


What happens if we upload 3 files but one of them fails?
What happens if we use `uploads` library to handle single upload files?
