// carrega videos assincronamente
setTimeout(function(){
	var iframe = '<iframe class="elasticMedia" src="https://www.youtube.com/embed/rrT6v5sOwJg" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
	
	document.querySelector('.elasticMedia-container').innerHTML = iframe;
	console.log("Executou");
}, 500);