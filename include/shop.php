<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/styles/style.css">
    <link rel="stylesheet" href="/assets/styles/shop.css">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/procreate-logo.png">
    <title>NEGOZIO | COMMESSO VIRTUALE</title>
</head>
<body>
    <nav id="navbar">
        <div class="container">
            <div class="logo" onclick="top.location.href = '/'"><img src="/assets/images/procreate-logo.png" alt="pro-create logo"></div>
            <div class="page">NEGOZIO</div>
            <div class="admin" onclick="top.location.href = '/?cart'">
                <img src="/assets/images/cart.svg" alt="cart">
            </div>
        </div>
    </nav>
    <main>
        <header class="hero">
            <div class="container">
                <p>SCOPRI LA TAVOLA CHE FA PER TE</p>
            </div>
        </header>
        <section id="shop">
            <div id="filters"></div>
            <div id="snowboards"></div>
        </section>
    </main>
    <script>
        let token = sessionStorage.getItem("token")
        if(!token){
            top.location.href = "/?login"
        }
        //Generazione filtri
        fetch('https://andrew02.it/api/getFilters', {
            method: "GET",
            headers: { "Content-Type": "application/json" },
        })
        .then(res => res.json())
        .then(data => {
            if (data.result) {
                document.getElementById("filters").insertAdjacentHTML('afterbegin',data.html)
                document.querySelectorAll(".filter-title").forEach(title=>{
                    title.addEventListener("click", ()=>{
                        title.parentElement.querySelector(".minimize").classList.toggle("hidden")
                        title.parentElement.querySelector(".filter-title").classList.toggle("rotate")
                    })
                })
                document.getElementById("reset").addEventListener("click",()=>{
                    localStorage.removeItem("filtri")
                    top.location.reload()
                })
                document.getElementById("search").addEventListener("click",()=>{
                    saveFilters()
                })
                getModels()
            } else {
                alert(data.msg)
            }
            return false;
        })
        .catch(err => alert("Server error, try again alter."))
        function getModels(){
            fetch('https://andrew02.it/api/getModels', {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({
                    filtri: JSON.parse(localStorage.getItem("filtri") ?? '[]')
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.result) {
                    let html = ''
                    if(data.models.length){
                        html = '<ul class="models">'
                        data.models.forEach(model=>{
                            html += `
                                    <li class="model">
                                        <div class="cart-btn" onclick="addCart(${model.id})"><img src="/assets/images/cart.svg" alt="cart svg"></div>
                                        <img class="model-img" src="/assets/images/${model.image}" alt="${model.name} image">
                                        <div class="">
                                            <p>${model.name}</p>
                                            <p class="model-price">${model.price.replace(".", ",")} €</p>
                                        </div>
                                    <li>
                                    `
                        })
                        html += '</ul>'
                    } else {
                        html = '<h2>Nessuna tavola trovata.</h2>'
                    }
                    document.getElementById("snowboards").innerHTML = ''
                    document.getElementById("snowboards").insertAdjacentHTML("beforeend", html)
                } else {
                    alert(data.msg)
                }
            })
            .catch(err => alert("Server error, try again alter."))
        }
        function saveFilters(){
            let filters = {
                brand: [],
                gender: [],
                flex: [],
                style: [],
                shape: [],
                weight: 0,
                size: 0
            }
            document.querySelectorAll("#brand input:checked").forEach(checkbox=>{
                filters.brand.push(checkbox.value)
            })
            document.querySelectorAll("#gender input:checked").forEach(checkbox=>{
                filters.gender.push(checkbox.value)
            })
            document.querySelectorAll("#flex input:checked").forEach(checkbox=>{
                filters.flex.push(checkbox.value)
            })
            document.querySelectorAll("#style input:checked").forEach(checkbox=>{
                filters.style.push(checkbox.value)
            })
            document.querySelectorAll("#shape input:checked").forEach(checkbox=>{
                filters.shape.push(checkbox.value)
            })
            filters.weight = document.getElementById("weight").value
            filters.size = document.getElementById("size").value
            localStorage.setItem("filtri", JSON.stringify(filters))
            getModels()
        }
        function addCart(product){
            let cart = JSON.parse(localStorage.getItem("cart") ?? "[]")
            cart.push(product)
            localStorage.setItem("cart", JSON.stringify(cart))
        }
    </script>
</body>
</html>
