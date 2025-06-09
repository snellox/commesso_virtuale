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
        <div class="page">SNOWBOARD</div>
        <div></div>
    </div>
</nav>
<div class="relative">
    <div class="back-btn" onclick="top.location.href = '/?admin'">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#79c3bd" version="1.1" id="Capa_1" viewBox="0 0 400.004 400.004" xml:space="preserve"><g><path d="M382.688,182.686H59.116l77.209-77.214c6.764-6.76,6.764-17.726,0-24.485c-6.764-6.764-17.73-6.764-24.484,0L5.073,187.757   c-6.764,6.76-6.764,17.727,0,24.485l106.768,106.775c3.381,3.383,7.812,5.072,12.242,5.072c4.43,0,8.861-1.689,12.242-5.072   c6.764-6.76,6.764-17.726,0-24.484l-77.209-77.218h323.572c9.562,0,17.316-7.753,17.316-17.315   C400.004,190.438,392.251,182.686,382.688,182.686z"/></g></svg>
    </div>
    <button id="create" class="btn info">Crea nuovo</button>
    <main class="container">
        <table id="data">
            <thead>
            <tr>
                <th>ID</th>
                <th>BRAND</th>
                <th>FOOOT SIZE</th>
                <th>NAME</th>
                <th>DESCRIPTION</th>
                <th>STYLE OF RIDING</th>
                <th>GENDER</th>
                <th>SHAPE</th>
                <th>FLEX</th>
                <th>WEIGHT MIN</th>
                <th>WEIGHT MAX</th>
                <th>LENGTH</th>
                <th>PRICE</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </main>
</div>
<div class="modal">
    <label for="brand">Brand</label>
    <select id="brand"></select>

    <label for="size">Foot size</label>
    <select id="size"></select>

    <label for="name">Name</label>
    <input type="text" id="name">

    <label for="descr">Description</label>
    <textarea id="descr"></textarea>

    <label for="style">Style of riding</label>
    <select id="style"></select>

    <label for="gender">Gender</label>
    <select id="gender"></select>

    <label for="shape">Shape</label>
    <select id="shape"></select>

    <label for="flex">Flex</label>
    <select id="flex"></select>

    <label for="w_min">Weight Min</label>
    <input type="text" id="w_min">

    <label for="w_max">Weight Max</label>
    <input type="text" id="w_max">

    <label for="length">Length</label>
    <input type="text" id="length">

    <label for="price">Price</label>
    <input type="text" id="price">

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
        let value = {
            size: document.getElementById("size").value,
            name: document.getElementById("name").value,
            description: document.getElementById("descr").value,
            style: document.getElementById("style").value,
            gender: document.getElementById("gender").value,
            shape: document.getElementById("shape").value,
            flex: document.getElementById("flex").value,
            w_min: document.getElementById("w_min").value,
            w_max: document.getElementById("w_max").value,
            length: document.getElementById("length").value,
            price: document.getElementById("price").value,
            brand: document.getElementById("brand").value
        }
        createData(value)
    })
    getData()
    function getData(){
        let uid = JSON.parse(sessionStorage.getItem("userData")).id
        fetch('https://andrew02.it/api/getSnows?uid='+uid, {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.result) {
                document.querySelector("#data tbody").innerHTML = ""
                resp.data.forEach(snow=>{
                    let html = `
                        <tr>
                            <td>${snow.id}</td>
                            <td>
                                <select name="brand" data-id="${snow.id}">`
                    resp.brands.forEach(b=>{
                        html += `<option value="${b.id}"${parseInt(b.id) === parseInt(snow.id_brand) ? ' selected' : ''}>${b.value}</option>`
                    })
                    html += `
                                </select>
                            </td>
                            <td>
                                <select name="size" data-id="${snow.id}">`
                    resp.sizes.forEach(s=>{
                        html += `<option value="${s.id}"${parseInt(s.id) === parseInt(snow.id_type_lenghts) ? ' selected' : ''}>${s.value} (${s.min} - ${s.max})</option>`
                    })
                    html += `
                                </select>
                            </td>
                            <td><input type="text" data-id="${snow.id}" name="name" value="${snow.name}"></td>
                            <td><textarea data-id="${snow.id}" name="description">${snow.description}</textarea></td>
                            <td>
                                <select name="style" data-id="${snow.id}">`
                    resp.styles.forEach(s=>{
                        html += `<option value="${s.id}"${parseInt(s.id) === parseInt(snow.id_style_of_riding) ? ' selected' : ''}>${s.value}</option>`
                    })
                    html += `
                                </select>
                            </td>
                            <td>
                                <select name="gender" data-id="${snow.id}">`
                    resp.genders.forEach(g=>{
                        html += `<option value="${g.id}"${parseInt(g.id) === parseInt(snow.id_gender) ? ' selected' : ''}>${g.value}</option>`
                    })
                    html += `
                                </select>
                            </td>
                            <td>
                                <select name="shape" data-id="${snow.id}">`
                    resp.shapes.forEach(s=>{
                        html += `<option value="${s.id}"${parseInt(s.id) === parseInt(snow.id_shape) ? ' selected' : ''}>${s.value}</option>`
                    })
                    html += `
                                </select>
                            </td>
                            <td>
                                <select name="flex" data-id="${snow.id}">`
                    resp.flexs.forEach(f=>{
                        html += `<option value="${f.id}"${parseInt(f.id) === parseInt(snow.id_flex) ? ' selected' : ''}>${f.value}</option>`
                    })
                    html += `
                                </select>
                            </td>
                            <td><input type="text" data-id="${snow.id}" name="w_min" value="${snow.weight_min}"></td>
                            <td><input type="text" data-id="${snow.id}" name="w_max" value="${snow.weight_max}"></td>
                            <td><input type="text" data-id="${snow.id}" name="length" value="${snow.size}"></td>
                            <td><input type="text" data-id="${snow.id}" name="price" value="${snow.price}"></td>

                            <td>
                                <button data-id="${snow.id}" class="btn success">Salva</button>
                                <button data-id="${snow.id}" class="btn danger">Elimina</button>
                            </td>
                        </tr>
                    `
                    document.querySelector("#data tbody").insertAdjacentHTML('beforeend', html)
                })

                resp.brands.forEach(b=>{
                    document.getElementById("brand").innerHTML += `<option value="${b.id}">${b.value}</option>`
                })
                resp.sizes.forEach(s=>{
                    document.getElementById("size").innerHTML += `<option value="${s.id}">${s.value} (${s.min} - ${s.max})</option>`
                })
                resp.styles.forEach(s=>{
                    document.getElementById("style").innerHTML += `<option value="${s.id}">${s.value}</option>`
                })
                resp.genders.forEach(g=>{
                    document.getElementById("gender").innerHTML += `<option value="${g.id}">${g.value}</option>`
                })
                resp.shapes.forEach(s=>{
                    document.getElementById("shape").innerHTML += `<option value="${s.id}">${s.value}</option>`
                })
                resp.flexs.forEach(f=>{
                    document.getElementById("flex").innerHTML += `<option value="${f.id}">${f.value}</option>`
                })

                document.querySelectorAll("#data .btn.success").forEach(btn=>{
                    btn.addEventListener("click", ()=>{
                        let id = btn.dataset.id
                        let data = {
                            brand: document.querySelector(`[name="brand"][data-id="${id}"]`).value,
                            size: document.querySelector(`[name="size"][data-id="${id}"]`).value,
                            name: document.querySelector(`[name="name"][data-id="${id}"]`).value,
                            description: document.querySelector(`[name="description"][data-id="${id}"]`).value,
                            style: document.querySelector(`[name="style"][data-id="${id}"]`).value,
                            gender: document.querySelector(`[name="gender"][data-id="${id}"]`).value,
                            shape: document.querySelector(`[name="shape"][data-id="${id}"]`).value,
                            flex: document.querySelector(`[name="flex"][data-id="${id}"]`).value,
                            w_min: document.querySelector(`[name="w_min"][data-id="${id}"]`).value,
                            w_max: document.querySelector(`[name="w_max"][data-id="${id}"]`).value,
                            length: document.querySelector(`[name="length"][data-id="${id}"]`).value,
                            price: document.querySelector(`[name="price"][data-id="${id}"]`).value
                        }
                        saveData(id, data)
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
    function saveData(id, data){
        fetch('https://andrew02.it/api/saveSnow', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id, data: data})
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
        fetch('https://andrew02.it/api/deleteSnow', {
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
        fetch('https://andrew02.it/api/createSnow', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ data: value, uid: JSON.parse(sessionStorage.getItem("userData")).id})
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