<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/styles/style.css">
    <link rel="stylesheet" href="/assets/styles/backoffice.css">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/procreate-logo.png">
    <title>ADMIN | COMMESSO VIRTUALE</title>
</head>
<body>
<nav id="navbar">
    <div class="container">
        <div class="logo" onclick="top.location.href = '/'"><img src="/assets/images/procreate-logo.png" alt="pro-create logo"></div>
        <div class="page">WORK ORDERS</div>
        <div></div>
    </div>
</nav>
<main class="container">
    <div class="back-btn" onclick="top.location.href = '/?admin'">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#79c3bd" version="1.1" id="Capa_1" viewBox="0 0 400.004 400.004" xml:space="preserve"><g><path d="M382.688,182.686H59.116l77.209-77.214c6.764-6.76,6.764-17.726,0-24.485c-6.764-6.764-17.73-6.764-24.484,0L5.073,187.757   c-6.764,6.76-6.764,17.727,0,24.485l106.768,106.775c3.381,3.383,7.812,5.072,12.242,5.072c4.43,0,8.861-1.689,12.242-5.072   c6.764-6.76,6.764-17.726,0-24.484l-77.209-77.218h323.572c9.562,0,17.316-7.753,17.316-17.315   C400.004,190.438,392.251,182.686,382.688,182.686z"/></g></svg>
    </div>
    <table id="data">
        <thead>
            <tr>
                <th>ID</th>
                <th>DATA DI CONSEGNA</th>
                <th>CLIENTE</th>
                <th>NEGOZIO</th>
                <th>&nbsp</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</main>
<script>
    let token = sessionStorage.getItem("adminToken")
    if(!token){
        top.location.href = "/?admin-login"
    }
    getData()
    function getData(){
        let sid = JSON.parse(sessionStorage.getItem("userData")).shop_id ?? 0
        fetch('https://andrew02.it/api/getWorks?sid='+sid, {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.result) {
                document.querySelector("#data tbody").innerHTML = ""
                resp.orders.forEach(o=>{
                    document.querySelector("#data tbody").insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${o.id}</td>
                            <td>
                               ${new Date(o.data).toLocaleDateString()}
                            </td>
                            <td>
                               ${o.cliente}
                            </td>
                            <td>
                               ${o.negozio}
                            </td>
                            <td>
                                <button onclick="window.open('/export/${o.url}', '_blank')" class="btn info">Scarica PDF</button>
                            </td>
                        </tr>
                    `)
                })
                if(!resp.orders.length){
                    document.querySelector("#data tbody").insertAdjacentHTML('beforeend', `
                        <tr>
                            <td colspan="5">Nessun ordine di lavoro presente</td>
                        </tr>
                    `)

                }
            } else {
                alert(data.msg)
            }
            return false;
        })
    }
    function logout(){
        sessionStorage.clear()
        top.location.reload()
    }
</script>
</body>
</html>