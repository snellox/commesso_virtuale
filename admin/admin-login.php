<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/styles/style.css">
    <link rel="stylesheet" href="/assets/styles/login.css">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/procreate-logo.png">
    <title>ACCESSO ADMIN | COMMESSO VIRTUALE</title>
</head>
<body>
<nav id="navbar">
    <div class="container">
        <div class="logo" onclick="top.location.href = '/'"><img src="/assets/images/procreate-logo.png" alt="pro-create logo"></div>
        <div class="page">ACCESSO BACKOFFICE</div>
        <div class="admin" onclick="top.location.href = '/?login'">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#79c3bd" version="1.1" viewBox="0 0 495.398 495.398" xml:space="preserve">
                <g>
                    <path d="M487.083,225.514l-75.08-75.08V63.704c0-15.682-12.708-28.391-28.413-28.391c-15.669,0-28.377,12.709-28.377,28.391 v29.941L299.31,37.74c-27.639-27.624-75.694-27.575-103.27,0.05L8.312,225.514c-11.082,11.104-11.082,29.071,0,40.158 c11.087,11.101,29.089,11.101,40.172,0l187.71-187.729c6.115-6.083,16.893-6.083,22.976-0.018l187.742,187.747 c5.567,5.551,12.825,8.312,20.081,8.312c7.271,0,14.541-2.764,20.091-8.312C498.17,254.586,498.17,236.619,487.083,225.514z"/>
                    <path d="M257.561,131.836c-5.454-5.451-14.285-5.451-19.723,0L72.712,296.913c-2.607,2.606-4.085,6.164-4.085,9.877v120.401 c0,28.253,22.908,51.16,51.16,51.16h81.754v-126.61h92.299v126.61h81.755c28.251,0,51.159-22.907,51.159-51.159V306.79     c0-3.713-1.465-7.271-4.085-9.877L257.561,131.836z"/>
                </g>
            </svg>
        </div>
    </div>
</nav>
<main>
    <div class="container">
        <div class="logo"><img src="/assets/images/procreate-logo.png" alt="logo"></div>
        <form id="login" novalidate method="post">
            <label for="email">Email</label>
            <input type="email" id="email" name="email">
            <label for="pw">Password</label>
            <input type="password" id="pw" name="pw">
            <button type="submit" id="login-btn">ACCEDI</button>
        </form>
    </div>
</main>
<script>
    document.getElementById("login").addEventListener("submit", function(event){
        event.preventDefault()
        let data = {
            email: checkEmail(document.getElementById("email").value),
            pw: checkPassword(document.getElementById("pw").value)
        }
        console.log(data)
        let error = false
        if(!data.email){
            document.getElementById("email").classList.add("error")
            error = true
        }
        if(!data.pw){
            document.getElementById("pw").classList.add("error")
            error = true
        }
        if(error){
            return false
        }
        document.querySelectorAll(".error").forEach(el=>el.classList.remove("error"))
        fetch('https://andrew02.it/api/admin-login', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email: document.getElementById("email").value, pw: document.getElementById("pw").value })
        })
            .then(res => res.json())
            .then(data => {
                if (data.result) {
                    sessionStorage.setItem("adminToken", data.token)
                    sessionStorage.setItem("userData", JSON.stringify(data.data))
                    top.location.href = '/?admin'
                } else {
                    alert(data.msg)
                }
                return false;
            })
            .catch(err => alert("Server error, try again alter."))
        return false
    })
    function checkEmail(email) {
        let re = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;
        return re.test(email);
    }
    function checkPassword(pw) {
        let re = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
        return re.test(pw);
    }
</script>
</body>
</html>
