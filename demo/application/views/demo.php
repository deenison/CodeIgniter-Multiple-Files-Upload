<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if (isset($error) && !empty($error)): ?>
<?php echo $error; ?>
<?php endif; ?>

<?php if (isset($data) && !empty($data)): ?>
<pre><?php echo print_r($data); ?></pre>
<?php endif; ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CodeIgniter Multi-File uploading Test</title>
    </head>
    <body>
        <form action="/upload" method="POST" enctype="multipart/form-data">
            <label>
                Single file:
                <input type="file" name="photo">
            </label>
            <label>
                Multiple files:
                <input type="file" name="photos[]" multiple>
            </label>
            <button type="submit">Submit</button>
        </form>
    </body>
</html>
