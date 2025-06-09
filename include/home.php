<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/styles/style.css">
    <link rel="stylesheet" href="/assets/styles/home.css">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/procreate-logo.png">
    <title>HOME | COMMESSO VIRTUALE</title>
</head>
<body>
    <nav id="navbar">
        <div class="container">
            <div class="logo" onclick="top.location.href = '/'"><img src="/assets/images/procreate-logo.png" alt="pro-create logo"></div>
            <div class="page">HOME</div>
            <div></div>
        </div>
    </nav>
    <main class="container">
        <div class="logo"><img src="/assets/images/procreate-logo.png" alt="logo"></div>
        <ul id="menu">
            <li onclick="top.location.href = '/?negozio'">NEGOZIO</li>
            <li onclick="top.location.href = '/?profilo'">PROFILO</li>
            <li class="logout" onclick="logout()">LOGOUT</li>
        </ul>
    </main>
    <script>
        let token = sessionStorage.getItem("token")
        if(!token){
            top.location.href = "/?login"
        }
        function logout(){
            sessionStorage.clear()
            top.location.reload()
        }
    </script>
</body>
</html>