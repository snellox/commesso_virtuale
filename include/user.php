<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/styles/style.css">
    <link rel="stylesheet" href="/assets/styles/user.css">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/procreate-logo.png">
    <title>PROFILO | COMMESSO VIRTUALE</title>
</head>
<body>
    <nav id="navbar">
        <div class="container">
            <div class="logo" onclick="top.location.href = '/'"><img src="/assets/images/procreate-logo.png" alt="pro-create logo"></div>
            <div class="page">PROFILO</div>
            <div></div>
        </div>
    </nav>
    <main class="container">
        <div class="back-btn" onclick="top.location.href = '/'">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#79c3bd" version="1.1" id="Capa_1" viewBox="0 0 400.004 400.004" xml:space="preserve"><g><path d="M382.688,182.686H59.116l77.209-77.214c6.764-6.76,6.764-17.726,0-24.485c-6.764-6.764-17.73-6.764-24.484,0L5.073,187.757   c-6.764,6.76-6.764,17.727,0,24.485l106.768,106.775c3.381,3.383,7.812,5.072,12.242,5.072c4.43,0,8.861-1.689,12.242-5.072   c6.764-6.76,6.764-17.726,0-24.484l-77.209-77.218h323.572c9.562,0,17.316-7.753,17.316-17.315   C400.004,190.438,392.251,182.686,382.688,182.686z"/></g></svg>
        </div>
        <div class="container">
            <h2 class="section-title">I tuoi dati</h2>
            <table>
                <tr>
                    <td>Nome</td>
                    <td id="data_name"></td>
                </tr>
                <tr>
                    <td>Cognome</td>
                    <td id="data_surname"></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td id="data_email"></td>
                </tr>
            </table>
            <h2 class="section-title">Tessera fedeltà</h2>
            <table>
                <tr>
                    <td>Punti maturati</td>
                    <td id="data_discount" class="text-right"></td>
                </tr>
                <tr>
                    <td>Scadenza</td>
                    <td id="data_exp" class="text-right"></td>
                </tr>
            </table>
        </div>
    </main>
    <script>
        let token = sessionStorage.getItem("token")
        if(!token){
            top.location.href = "/?login"
        }
        let userData = JSON.parse(sessionStorage.getItem("userData") ?? '[]')
        if(!userData){
            top.location.href = "/?login"
        }
        document.getElementById("data_name").innerText = userData.name
        document.getElementById("data_surname").innerText = userData.surname
        document.getElementById("data_email").innerText = userData.email
        document.getElementById("data_discount").innerText = userData.discount ? parseFloat(userData.discount).toFixed(2).replace(".", ",") : '0,00'
        document.getElementById("data_exp").innerText = userData.expiring_discount ? new Date(userData.expiring_discount).toLocaleDateString("it-IT") : '-'

    </script>
</body>
</html>