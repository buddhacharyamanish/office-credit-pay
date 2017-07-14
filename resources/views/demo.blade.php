<html>
<body>
<form action="{{ route('demo.upload') }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type="file" name="demo">
    <input type="submit" value="Submit">
</form>
</body>
</html>