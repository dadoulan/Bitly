<?php

	if(!empty($_GET['q'])){

		// Variable
		$shortcut = htmlspecialchars($_GET['q']);

		//Existe-t-elle dans la base de donnée?
		
		$bdd = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8', 'root', '');
		$requete = $bdd->prepare('SELECT COUNT(*) AS nombre FROM links WHERE shortcut = ?');
		$requete->execute([$shortcut]);

		while ($resultat = $requete->fetch()){
			
			if($resultat['nombre'] != 1){
				header('location: ./?error=true&message=Adresse url non connue');
				exit();
			}

		}

		// Redirection 
		$req = $bdd->prepare('SELECT * FROM links WHERE shortcut = ?');
		$req->execute([$shortcut]);

		while($resultat = $req->fetch()){

			header('location: '.$resultat['url']);
			exit();

		}
	}

	if(!empty($_POST['url'])){
		// Creation variable
		$url = htmlspecialchars($_POST['url']);

		// Verification format url

		if(!filter_var($url, FILTER_VALIDATE_URL)){

			header('location: ./?error=true&message=Adresse url non valide');
			exit();
			
		}

		// Creation shortcut
		$shortcut = crypt($url, rand());

		// Verification doublon
		$bdd = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8', 'root', '');
		// $req = $bdd->prepare('SELECT COUNT(*) AS nombre FROM links WHERE url = ?');
		// $req->execute([$url]);

		// while($resultat = $req->fetch()){
		// 	if($resultat['nombre'] != 0){
		// 		header('location: ./?error=true&message=Adresse url déja utilisée');
		// 		exit();
		// 	}
		// }

		// Ajout raccourcie à la base de donnée
		$ajout = $bdd->prepare('INSERT INTO links(url,shortcut) VALUES(?,?)');
		$ajout->execute([$url, $shortcut]);

		header("location: ./?short=$shortcut");
		exit();

	}
?>

<html>
	<head>
			<meta charset="utf-8">
			<title>BITLY - Raccourcissez vos urls</title>
			<link rel="stylesheet" href="design/default.css">
	<link rel="icon" type="image/png" href="assets/favicon.png">
	</head>
  <body>

	<!-- PRESENTATION -->
	<section id="main">
				
				<!-- CONTAINER -->
		<div class="container">
			
			<!-- EN-TETE -->
			<?php require_once('src/header.php'); ?>		

			<!-- PROPOSITION -->
			<h1>Une url longue ? Raccourcissez-là ?</h1>
			<h2>Largement meilleur et plus court que les autres.</h2>

			<!-- FORM -->
			<form method="post" action="index.php">
				<input type="url" name="url" placeholder="Collez un lien à raccourcir">
				<input type="submit" value="Raccourcir">
			</form>

			<?php if(isset($_GET['error']) && isset($_GET['message'])) { ?>
									
				<div class="center">
					<div id="result">
						<b><?php echo htmlspecialchars($_GET['message']); ?></b>
					</div>
				</div>
									
			<?php } else if(isset($_GET['short'])) { ?>
									
				<div class="center">
					<div id="result">
						<b>URL RACCOURCIE : </b>
						http://localhost/?q=<?php echo htmlspecialchars($_GET['short']); ?>
					</div>
				</div>
									
			<?php } ?>

		</div>

		</section>

			<!-- MARQUES -->
	<section id="brands">
		
		<!-- CONTAINER -->
		<div class="container">
			<h3>Ces marques nous font confiance</h3>
			<img src="assets/1.png" alt="1" class="picture">
			<img src="assets/2.png" alt="2" class="picture">
			<img src="assets/3.png" alt="3" class="picture">
			<img src="assets/4.png" alt="4" class="picture">
		</div>

	</section>

	<!-- PIED DE PAGE -->
	<?php require_once('src/footer.php'); ?>		
  </body>
</html>