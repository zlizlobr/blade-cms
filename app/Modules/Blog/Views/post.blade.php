<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $slug }} - Blog</title>
</head>
<body>
    <h1>Post: {{ $slug }}</h1>
    <p>This is a blog post page.</p>

    <a href="{{ route('blog.index') }}">&larr; Back to Blog</a>
</body>
</html>
