<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">

	<title>Admin | Site de Blog</title>

	<?php include('./header.php'); ?>
	<?php
	session_start();
	if (isset($_SESSION['login_id']))
		header("location:index.php?page=home");
	?>

	<style>
		/* Arrière-plan */
		.login-container {
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100vh;
			background-color: #f5f5f5;
			/* Fond gris clair */
			margin: 0;
		}

		/* Carte de connexion */
		.login-card {
			background-color: #ffffff;
			/* Fond blanc pour la carte */
			padding: 2rem;
			border-radius: 8px;
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
			text-align: center;
			width: 50%;
			display: flex;
			flex-direction: column;
			align-items: center;
		}

		/* Logo */
		.logo {
			/* max-width: 100px; */
			/* Ajuster la taille pour la réactivité */
			margin-bottom: 1rem;
			max-width: 100%;
		}

		/* Titre du formulaire */
		h2 {
			font-size: 1.5rem;
			color: #00a9c6;
			/* Bleu du logo */
			margin-bottom: 1.5rem;
		}

		#login-form {
			width: 100%;
		}

		/* Champs de saisie */
		.input-field {
			width: 100%;
			padding: 0.8rem;
			margin: 0.5rem 0;
			border: 1px solid #b0b0b0;
			/* Bordure grise */
			border-radius: 4px;
			font-size: 1rem;
			color: #333333;
			box-sizing: border-box;
		}

		.input-field:focus {
			outline: none;
			border-color: #00a9c6;
			/* Bordure bleue au focus */
			box-shadow: 0 0 5px rgba(0, 169, 198, 0.5);
		}

		/* Bouton de soumission */
		.submit-button {
			width: 100%;
			padding: 0.8rem;
			background-color: #00a9c6;
			/* Bouton bleu */
			border: none;
			border-radius: 4px;
			font-size: 1rem;
			color: white;
			cursor: pointer;
			transition: background-color 0.3s ease;
		}

		.submit-button:hover {
			background-color: #008b9c;
			/* Bleu plus foncé au survol */
		}

		.submit-button:active {
			background-color: #006a76;
			/* Bleu encore plus foncé au clic */
		}

		.error-alert {
			color: #d9534f;
			/* Rouge pour l'erreur */
			background-color: #f8d7da;
			border: 1px solid #f5c6cb;
			padding: 10px;
			margin-bottom: 1rem;
			border-radius: 4px;
			text-align: center;
		}
	</style>

</head>

<body>
	<div class="login-container">
		<div class="login-card">
			<div class="logo">
				<img src="./assets/cmc.png" alt="Logo">
			</div>
			<h2>Gestion D'archivage</h2>
			<form id="login-form">
				<div class="form-group">
					<input type="text" id="username" name="username" class="input-field" placeholder="Nom d'utilisateur">
				</div>
				<div class="form-group">
					<input type="password" id="password" name="password" class="input-field" placeholder="Mot de passe">
				</div>
				<button type="submit" class="submit-button">Se connecter</button>
				<div id="error-message" class="error-alert" style="display: none;">Nom d'utilisateur ou mot de passe incorrect</div>
			</form>
		</div>
	</div>

	<script>
		$('#login-form').submit(function(e) {
			e.preventDefault();
			const button = $(this).find('.submit-button');
			button.attr('disabled', true).text('Connexion...');

			if ($(this).find('.error-alert').length > 0)
				$(this).find('.error-alert').hide();

			$.ajax({
				url: 'ajax.php?action=login',
				method: 'POST',
				data: $(this).serialize(),
				error: err => {
					console.log(err);
					button.removeAttr('disabled').text('Connexion');
				},
				success: function(resp) {
					if (resp == 1) {
						location.reload('index.php?page=home');
					} else {
						$('#error-message').show();
						button.removeAttr('disabled').text('Connexion');
					}
				}
			});
		});
	</script>
</body>

</html>