<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - {{ config('app.name') }}</title>
</head>
<body>
    <h1>Blog Module</h1>
    <p>Welcome to the Blog module. This is an example module for Blade CMS.</p>

    <ul>
        <li><a href="{{ route('blog.post', 'example-post') }}">Example Post</a></li>
    </ul>
</body>
</html>
