<?php
// Prevent users from directly accessing this script
if(!defined('ABSPATH')) exit;
?>

<!-- Page HTML -->
<div class="wrap">
	<!-- Thuisbezorgd beoordelingen logo -->
	<a href="http://musa.pw/wordpress" target="_blank" class="thuisbezorgd-logo">
		<img src="<?= plugins_url('img/plugin_logo.png', __DIR__ ) ?>" alt="Thuisbezorgd Beoordelingen" title="Thuisbezorgd Beoordelingen door http://musa.pw/wordpress">
	</a>
	
	<!-- Thuisbezorgd beoordelingen settings -->
	<form action="options.php" method="POST" enctype="multipart/formdata">
		<?php
			// WP fields security
			settings_fields('thuisbezorgd-beoordelingen-settings');

			// Get input fields
			do_settings_sections('settings.php');

			// Submit form button
			submit_button();

			// Errors
			settings_errors();
		?>
	</form>

	<hr>
	<center>
		<h1>Veelgestelde vragen</h1>
	</center>

	<h2>Wat doet deze plugin?</h2>
	<p>De Thuisbezorgd Beoordelingen plugin maakt het mogelijk om de beoordelingen op je thuisbezorgd pagina te weergeven op je Wordpress website. Deze beoordelingen worden automatisch geupdate via je thuisbezorgd pagina.</p>

	<h2>Hoe gebruik ik de Thuisbezorgd Beoordelingen plugin?</h2>
	<ol>
		<li>Maak een nieuwe pagina aan voor de Thuisbezorgd Beoordelingen of gebruik een bestaande pagina.</li>
		<li>Bewerk de pagina en voer de volgende shortcode in waar je de beoordelingen wilt hebben <code>[thuisbezorgd-beoordelingen]</code></li>
	</ol>

	<h2>Is deze plugin van Thuisbezorgd.nl?</h2>
	<p>Nee. Deze plugin is niet gemaakt door thuisbezorgd.nl en deze wordt ook niet erkend/ondersteund door Thuisbezorgd. De ontwikkelaar van de plugin is <a href="http://musa.pw/wordpress" target="_blank">Musa</a>.</p>

	<h2>Ik heb een suggestie/tip voor deze plugin, kan ik deze doorgeven?</h2>
	<p>Ja. Als je een verbeterpunt of tip voor de plugin hebt dan hoor ik dat graag via <a href="mailto:mussesemou99@gmail.com">mussesemou99@gmail.com</a>.</p>

	<h2>Is deze plugin gratis?</h2>
	<p>Deze plugin is en blijft 100% gratis, maar iedere donatie wordt zeer gewaardeerd. <br>

	<div class="thuisbezorgd-donate">
		IBAN: NL95 ABNA 0574 7934 96<br>
		Op naam van Musa <br>
		Omschrijving: Donatie Thuisbezorgd plugin
	</div> <br>
	
	Of via PayPal <br>
	
	<a href="https://paypal.me/musa11971" target="_blank">
		<img style="width: 170px" src="<?= plugins_url('img/donate.png', __DIR__ ) ?>" alt="Doneer via PayPal">
	</a>

	</p>
</div>
