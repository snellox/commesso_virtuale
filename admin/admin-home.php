<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/styles/style.css">
    <link rel="stylesheet" href="/assets/styles/home.css">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/procreate-logo.png">
    <title>ADMIN | COMMESSO VIRTUALE</title>
</head>
<body>
<nav id="navbar">
    <div class="container">
        <div class="logo" onclick="top.location.href = '/'"><img src="/assets/images/procreate-logo.png" alt="pro-create logo"></div>
        <div class="page">BACKOFFICE</div>
        <div></div>
    </div>
</nav>
<main class="container">
    <div class="logo"><img src="/assets/images/procreate-logo.png" alt="logo"></div>
    <ul id="menu">
        <li class="admin-only" onclick="top.location.href = '/?admin-gender'">GENDER</li>
        <li class="admin-only" onclick="top.location.href = '/?admin-brand'">BRAND</li>
        <li class="admin-only" onclick="top.location.href = '/?admin-flex'">FLEX</li>
        <li class="admin-only" onclick="top.location.href = '/?admin-style'">STYLE OF RIDING</li>
        <li class="admin-only" onclick="top.location.href = '/?admin-shape'">SHAPE</li>
        <li onclick="top.location.href = '/?admin-snow'">SNOWBOARD</li>
        <li class="admin-only" onclick="top.location.href = '/?admin-order'">ORDERS TO EXPORT</li>
        <li onclick="top.location.href = '/?admin-work'">WORK ORDERS</li>
        <li class="logout" onclick="logout()">LOGOUT</li>
    </ul>
</main>
<script>
    let token = sessionStorage.getItem("adminToken")
    if(!token){
        top.location.href = "/?admin-login"
    } else {
        let userData = JSON.parse(sessionStorage.getItem("userData"))
        if(userData.shop_id) {
            document.querySelectorAll(".admin-only").forEach(el=>{el.remove()})
        }
    }
    function logout(){
        sessionStorage.clear()
        top.location.reload()
    }
</script>
</body>
</html>