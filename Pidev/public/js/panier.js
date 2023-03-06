<script src="https://unpkg.com/axios@1.1.2/dist/axios.min.js"></script>

const spans_total_cart = document.querySelectorAll("span#spanAddQuantity");
const spans_quantity = document.querySelectorAll("span[id^='js_quantity_']");
const spans_prix = document.querySelectorAll("span[id^='js_prix_']");
const span_prix_total = document.querySelector("#prix_total");
const span_products = document.querySelectorAll("#js_Product");


//script pour le button AddToCart
	function onClickBtnAddToCart(event) {
		event.preventDefault();
		const url = this.href;
		//supprimer le produit
		axios.get(url).then(function (response) {
			console.log(response.data);
			spans_total_cart.forEach(span => {
			span.textContent=response.data.quantity;
			});

		});
	}

	document.querySelectorAll('a.buttonAddToCart').forEach(function (link) {
		link.addEventListener('click', onClickBtnAddToCart);
	});


 


//script pour le button removeProduit
	function onClickBtnRemove(event) {
		event.preventDefault();
		const url = this.href;
		//supprimer le produit
		axios.get(url).then(function (response) {
			const productId = response.data.id;
			const productRow = document.getElementById(`js_Product_${productId}`);
			

			if (productRow) {
				productRow.remove();
			}
			//changer le prix totale
			span_prix_total.textContent = response.data.total;

		});
	}

	document.querySelectorAll('a#btnRemoveProduit').forEach(function (link) {
		link.addEventListener('click', onClickBtnRemove);
	});

//script pour le button plus
	function onClickBtnPlus(event) {
		event.preventDefault();
		const url = this.href;
		
		axios.get(url).then(function (response) {
			//changer la quantitée
			spans_quantity.forEach(span => {
      		const productId = span.id.split('_')[2];
			if (response.data.id == productId) {
				span.textContent = response.data.quantite;
			}
      		});
			//changer le prix
			spans_prix.forEach(span => {
      		const productId = span.id.split('_')[2];
			if (response.data.id == productId) {
				span.textContent = response.data.prix;
			}
      		});
			//changer le prix totale
			span_prix_total.textContent = response.data.total;
			
		});
	}

	document.querySelectorAll('a.buttonplus').forEach(function (link) {
		link.addEventListener('click', onClickBtnPlus);
	});
//script pour le button moins
	function onClickBtnMoins(event) {
		event.preventDefault();
		const url = this.href;
		//changer la quantitée
		axios.get(url).then(function (response) {
			spans_quantity.forEach(span => {
      		const productId = span.id.split('_')[2];
			if (response.data.id == productId) {
				span.textContent = response.data.quantite;
			}
      		});
			//changer le prix
			spans_prix.forEach(span => {
      		const productId = span.id.split('_')[2];
			if (response.data.id == productId) {
				span.textContent = response.data.prix;
			}
      		});
			//changer le prix totale
			span_prix_total.textContent = response.data.total;
		});
	}

	document.querySelectorAll('a.buttonMoins').forEach(function (link) {
		link.addEventListener('click', onClickBtnMoins);
	});



