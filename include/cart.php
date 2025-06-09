<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/styles/style.css">
    <link rel="stylesheet" href="/assets/styles/cart.css">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/procreate-logo.png">
    <title>CARRELLO | COMMESSO VIRTUALE</title>
</head>
<body>
    <nav id="navbar">
        <div class="container">
            <div class="logo" onclick="top.location.href = '/'"><img src="/assets/images/procreate-logo.png" alt="pro-create logo"></div>
            <div class="page">CARRELLO</div>
            <div></div>
        </div>
    </nav>
    <main>
        <div class="back-btn" onclick="top.location.href = '/?shop'">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#79c3bd" version="1.1" id="Capa_1" viewBox="0 0 400.004 400.004" xml:space="preserve"><g><path d="M382.688,182.686H59.116l77.209-77.214c6.764-6.76,6.764-17.726,0-24.485c-6.764-6.764-17.73-6.764-24.484,0L5.073,187.757   c-6.764,6.76-6.764,17.727,0,24.485l106.768,106.775c3.381,3.383,7.812,5.072,12.242,5.072c4.43,0,8.861-1.689,12.242-5.072   c6.764-6.76,6.764-17.726,0-24.484l-77.209-77.218h323.572c9.562,0,17.316-7.753,17.316-17.315   C400.004,190.438,392.251,182.686,382.688,182.686z"/></g></svg>
        </div>
        <section id="cart">
        </section>
        <div class="wrap">
            <button class="cart-button" id="reset" onclick="localStorage.setItem('cart', '[]');top.location.reload()">RESET</button>
            <button class="cart-button" id="buy" onclick="order()">ORDINA</button>
        </div>
    </main>
    <script>
        let token = sessionStorage.getItem("token")
        if(!token){
            top.location.href = "/?login"
        }
        let old_cart = JSON.parse(localStorage.getItem("cart")),
            new_cart = []
        old_cart.forEach(c=>{
            let index = new_cart.findIndex(a=>a.id === c)
            if(index >= 0){
                new_cart[index].qta++
            } else {
                new_cart.push({
                    id: c,
                    qta: 1
                })
            }
        })
        fetch('https://andrew02.it/api/getCart', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({cart: new_cart})
        })
        .then(res => res.json())
        .then(data => {
            if (data.result) {
                document.getElementById("cart").insertAdjacentHTML('afterbegin', data.html)
            } else {
                alert(data.msg)
            }
            return false;
        })
        .catch(err => alert("Server error, try again alter."))
        function order() {
            fetch('https://andrew02.it/api/order', {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({cart: new_cart, uid: JSON.parse(sessionStorage.getItem("userData")).id})
            })
            .then(res => res.json())
            .then(data => {
                if (data.result) {
                    alert("Ordine Creato con Successo")
                    localStorage.setItem('cart', '[]')
                    top.location.reload()
                } else {
                    alert(data.msg)
                }
                return false;
            })
            .catch(err => alert("Server error, try again alter."))
        }
    </script>
</body>
</html>
