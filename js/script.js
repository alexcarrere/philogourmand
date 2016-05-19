$(function(){

	//Déclaration des variables
	var error = 0;	//Variable pour indiquer si il y a des erreurs dans le formulaire
	var errorEmpty = 0; //Variable pour indiquer si il y a des champs vide dans le formulaire
	var txtError = []; //Variable pour stocker les messages d'erreurs
	var formValid = false; //Variable pour valider l'envoi du formulaire
  	var pattern = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/; //pattern de validation pour les emails

  	//Si le formulaire de contact est bien chargé sur la page
	if($('form#formMessageContact').length == 1){
		console.log('formulaire chargé');

		//On vérifie le formulaire dés que l'utilisateur modifie un champ
		$('[data-form="input-form"]').change(function () {
			verifFormContact();
		});

		//On enlève les erreurs et on remet les bordures par défaut si on click sur reset
		$('input[type="reset"]').click(function () {
			border_default($('[data-form="input-form"]'));
			$('.alert').remove();
		});

		//Si le formulaire est envoyé 
		$('#formMessageContact').submit(function (e) {
			e.preventDefault(); //on empèche le comportement de base de l'envoi

			verifFormContact(); //on vérifie le formulaire 

			//Si le formulaire est valide
			if (formValid == true){	

				var val = $(this).serialize();

				$.ajax({
					url: "contact_message.php",
					type: "post",
					data: val,
					dataType: 'text',
					success: function(data){
						$('.alert').remove();
						$('#formMessageContact').before('<div class="alert alert-success" role="alert">'+data+'</div>');
						$('[data-form="input-form"]').val('');
					},
				}); 
			}
		});
	};


	/* Fonctions */

	/**
	 * Change la couleur de la bordure de l'élément ciblé (red)
	 * @return void
	 */
	function border_error(e) {
		e.css('border-color', 'red');
	}

	
	/**
	 * Change la couleur de la bordure de l'élément ciblé (green)
	 * @return void
	 */
	function border_success(e) {
		e.css('border-color', 'green');
	}

	/**
	 * Change la couleur de la bordure de l'élément ciblé (orange)
	 * @return void
	 */
	function border_warning(e) {
		e.css('border-color', 'orange');
	}

	/**
	 * Change la couleur de la bordure de l'élément ciblé (#ccc)
	 * @return void
	 */
	function border_default(e) {
		e.css('border-color', '#ccc');
	}

	/**
	 * Vérifie le contenu des champs du formulaire de contact
	 * @return void
	 */
	function verifFormContact() {

		//Remise à zéro des variables d'erreurs
		error = 0;
		errorEmpty = 0;
		txtError = [];

		//On enlève les messages d'erreurs précédents
		$('.alert').remove();

      	//Vérification si l'adresse email est vide
      	if($('#email').val() == ''){
      		errorEmpty++;
      		border_warning($('#email'));
      	}
      	//Vérification si l'adresse email est correctement formatée
      	else if(!pattern.test($('#email').val())) {
      		error++;
      		border_error($('#email'));
      		txtError.push('Format de l\'email invalide');
      	} 
      	else {
      		border_success($('#email'));
      	}

      	//Vérification si le contenu est vide
      	if($('#content').val() == ''){
      		errorEmpty++;
      		border_warning($('#formContent'));
      	} else {
      		border_success($('#formContent'));
      	}

      	//Si un des champs est vide
      	if (errorEmpty > 0) {
      		//on affiche le message d'alerte
			$('#titleRep').after ('<div class="alert alert-warning" role="alert">Tous les champs sont obligatoires</div>');
			formValid = false; //on passe la valeur formValid à false, ce qui empêchera l'envoi du formulaire
      	}

      	//Si un des champs est incorrect
      	if (error > 0) {

      		//On affiche la ou les erreur(s)
      		msgError = '<div class="alert alert-danger" role="alert">';
      		$.each(txtError, function(index, value) {
      			msgError += value + '<br>';
      		});
      		msgError += '</div>';

      		$('#formMessageContact').before(msgError);
      		formValid = false;
      	} 

      	//si il n'y a aucune erreur
  		if (errorEmpty == 0 && error == 0) {
  			formValid = true; //on passe la valeur formValid à true, ce qui autorisera l'envoi du formulaire
  		} 
      	
	}
});