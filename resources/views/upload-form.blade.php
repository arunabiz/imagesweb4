<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<form action="{{ route('image.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="image">Select Image:</label>
        <input type="file" name="image" id="image">
    </div>
    <div>
        <label for="name">Image Name:</label>
        <input type="text" name="name" id="name">
    </div>
    <button type="submit">Upload</button>
</form>




</html>
