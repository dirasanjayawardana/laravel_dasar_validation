<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Form</title>
</head>

<body>

    {{-- untuk menampilkan error ketika terjadi error, cukup gunakan variabel $errors di blade template --}}

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="/form" method="post">
        @csrf

        {{-- directive @error(key) --> jika ada error dengan key yg dimaksud maka akan ditampilkan di variabel $message --}}
        {{-- method old() --> untuk mendapatkan data sebelumnya, ketika terjadi exception, laravel menyimpan data form sebelumnya di session --}}

        <label>
            Username : @error('username') {{$message}} @enderror
            <input type="text" name="username" value="{{old('username')}}">
        </label> <br>

        <label>
            Password : @error('password') {{$message}} @enderror
            <input type="password" name="password" value="{{old('password')}}">
        </label><br>

        <input type="submit" value="Login">
    </form>

</body>

</html>
