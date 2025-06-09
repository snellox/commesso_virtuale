<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/styles/style.css">
    <link rel="stylesheet" href="/assets/styles/login.css">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/procreate-logo.png">
    <title>LOGIN | COMMESSO VIRTUALE</title>
</head>
<body>
    <nav id="navbar">
        <div class="container">
            <div class="logo" onclick="top.location.href = '/'"><img src="/assets/images/procreate-logo.png" alt="pro-create logo"></div>
            <div class="page">LOGIN</div>
            <div class="admin" onclick="top.location.href = '/?admin'">
                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 16 16">
                    <path fill="#79c3bd" fill-rule="evenodd" d="M10.0248,9.97521 L5.83008,14.1699 C4.72551,15.2745 2.93464,15.2745 1.83008,14.1699 C0.725505,13.0654 0.725506,11.2745 1.83008,10.1699 L6.02479,5.97521 C6.0084,5.81904 6,5.6605 6,5.5 C6,3.18096 7.7542,1.27164 10.008,1.02658 C10.1696,1.00901 10.3338,1 10.5,1 C11.1906,1 11.8448,1.15555 12.4295,1.43351 L10.2851,3.57797 C9.6993,4.16376 9.6993,5.11351 10.2851,5.69929 C10.8709,6.28508 11.8206,6.28508 12.4064,5.69929 L14.5564,3.54932 C14.8407,4.13945 15,4.80112 15,5.5 C15,5.65429 14.9922,5.80676 14.9771,5.95705 C14.748,8.22767 12.831,10 10.5,10 C10.3395,10 10.181,9.9916 10.0248,9.97521 Z M9.28499,7.88658 L4.41586,12.7557 C4.09234,13.0792 3.56781,13.0792 3.24429,12.7557 C2.92077,12.4322 2.92077,11.9077 3.24429,11.5841 L8.11342,6.715 L9.28499,7.88658 Z"/>
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
            <p class="register">Sei nuovo? Registrati <a href="/?registrati">qui</a>.</p>
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
            fetch('https://andrew02.it/api/auth', {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email: document.getElementById("email").value, pw: document.getElementById("pw").value })
            })
            .then(res => res.json())
            .then(data => {
                if (data.result) {
                    sessionStorage.setItem("token", data.token)
                    sessionStorage.setItem("userData", JSON.stringify(data.data))
                    top.location.href = '/'
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
