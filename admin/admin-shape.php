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
        <div class="page">SHAPE</div>
        <div></div>
    </div>
</nav>
<main class="container">
    <div class="back-btn" onclick="top.location.href = '/?admin'">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#79c3bd" version="1.1" id="Capa_1" viewBox="0 0 400.004 400.004" xml:space="preserve"><g><path d="M382.688,182.686H59.116l77.209-77.214c6.764-6.76,6.764-17.726,0-24.485c-6.764-6.764-17.73-6.764-24.484,0L5.073,187.757   c-6.764,6.76-6.764,17.727,0,24.485l106.768,106.775c3.381,3.383,7.812,5.072,12.242,5.072c4.43,0,8.861-1.689,12.242-5.072   c6.764-6.76,6.764-17.726,0-24.484l-77.209-77.218h323.572c9.562,0,17.316-7.753,17.316-17.315   C400.004,190.438,392.251,182.686,382.688,182.686z"/></g></svg>
    </div>
    <button id="create" class="btn info">Crea nuovo</button>
    <table id="data">
        <thead>
            <tr>
                <th>ID</th>
                <th>VALUE</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</main>
<div class="modal">
    <label for="value">Value</label>
    <input type="text" id="value">
    <button class="btn info">CREA</button>
    <button class="btn danger">ANNULLA</button>
</div>
<script>
    let token = sessionStorage.getItem("adminToken")
    if(!token){
        top.location.href = "/?admin-login"
    }
    document.getElementById("create").addEventListener("click", ()=> {
        document.querySelector(".modal").classList.toggle("active")
    })
    document.querySelector(".modal .btn.danger").addEventListener("click", ()=> {
        document.querySelector(".modal").classList.remove("active")
    })
    document.querySelector(".modal .btn.info").addEventListener("click", ()=> {
        let value = document.getElementById("value").value
        createData(value)
    })
    getData()
    function getData(){
        fetch('https://andrew02.it/api/getShapes', {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.result) {
                document.querySelector("#data tbody").innerHTML = ""
                resp.data.forEach(g=>{
                    document.querySelector("#data tbody").insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${g.id}</td>
                            <td><input type="text" data-id="${g.id}" value="${g.value}"></td>
                            <td>
                                <button data-id="${g.id}" class="btn success">Salva</button>
                                <button data-id="${g.id}" class="btn danger">Elimina</button>
                            </td>
                        </tr>
                    `)
                })
                document.querySelectorAll("#data .btn.success").forEach(btn=>{
                    btn.addEventListener("click", ()=>{
                        let id = btn.dataset.id
                        let value = document.querySelector(`input[data-id="${id}"]`).value
                        saveData(id, value)
                    })
                })
                document.querySelectorAll("#data .btn.danger").forEach(btn=>{
                    btn.addEventListener("click", ()=>{
                        let id = btn.dataset.id
                        deleteData(id)
                    })
                })
            } else {
                alert(data.msg)
            }
            return false;
        })
    }
    function saveData(id, value){
        fetch('https://andrew02.it/api/saveShape', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id, value: value})
        })
            .then(res => res.json())
            .then(data => {
                if (!data.result) {
                    alert(data.msg)
                } else {
                    getData()
                    alert("Valore modificato con successo")
                }
                return false;
            })
    }
    function deleteData(id, value){
        fetch('https://andrew02.it/api/deleteShape', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id, value: value})
        })
            .then(res => res.json())
            .then(data => {
                if (!data.result) {
                    alert(data.msg)
                } else {
                    getData()
                    alert("Valore eliminato con successo")
                }
                return false;
            })
    }
    function createData(value){
        fetch('https://andrew02.it/api/createShape', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ value: value})
        })
        .then(res => res.json())
        .then(data => {
            if (!data.result) {
                alert(data.msg)
            } else {
                document.querySelector(".modal").classList.remove("active")
                getData()
                alert("Valore creato con successo")
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