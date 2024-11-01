<?php
/*
Plugin Name: Thuisbezorgd Beoordelingen
Plugin URI: http://musa.pw/wordpress
Description: Laat de Thuisbezorgd beoordelingen van uw restaurant zien op uw Wordpress website.
Version: 2.0
Author: Musa Semou
Author URI: http://musa.pw/wordpress
*/

// Prevent users from directly accessing this script
if(!defined('ABSPATH')) exit;

// Require simple HTML DOM library, but first check if it already exists
if(!class_exists('simple_html_dom')) require 'html_dom.php';

/* Add Thuisbezorgd Beoordelingen menu item for admins */
function thuisbezorgd_beoordelingen_add_admin_menu() {
	add_menu_page(
		'Thuisbezorgd beoordelingen opties', // Page title
		'Thuisbezorgd beoordelingen', // Menu title
		'manage_options', // Capability
		plugin_dir_path(__FILE__) . 'settings/settings.php', // Menu slug
		null, // HTML
		'dashicons-format-status' // Icon
	);
}
add_action('admin_menu', 'thuisbezorgd_beoordelingen_add_admin_menu');

/* Custom settings for Thuisbezorgd Beoordelingen */
function thuisbezorgd_beoordelingen_settings_init()
{
	// Register the settings
	register_setting(
		'thuisbezorgd-beoordelingen-settings',
		'thuisbezorgd-beoordelingen-settings',
		'thuisbezorgd_beoordelingen_settings_validate');
	
	// Create a single section
	add_settings_section(
		'thuisbezorgd-beoordelingen-section',
		'Thuisbezorgd Beoordelingen opties',
		'thuisbezorgd_beoordelingen_settings_html',
		'settings.php'
	);

	// Create URL input
	add_settings_field(
		'thuisbezorgd-beoordelingen-url',
		'Thuisbezorgd restaurant URL',
		'thuisbezorgd_beoordelingen_urlinput_html',
		'settings.php',
		'thuisbezorgd-beoordelingen-section'
	);

	// Create Hide negative input
	add_settings_field(
		'thuisbezorgd-beoordelingen-negative',
		'Negatieve beoordelingen verbergen',
		'thuisbezorgd_beoordelingen_negativeinput_html',
		'settings.php',
		'thuisbezorgd-beoordelingen-section'
	);

	// Create max input
	add_settings_field(
		'thuisbezorgd-beoordelingen-max',
		'Maximum aantal beoordelingen',
		'thuisbezorgd_beoordelingen_maxinput_html',
		'settings.php',
		'thuisbezorgd-beoordelingen-section'
	);
}
add_action('admin_init', 'thuisbezorgd_beoordelingen_settings_init');

/* Validate posted settings */
function thuisbezorgd_beoordelingen_settings_validate($input) {
	// Get new and old restaurant URLs
	$newRestaurantURL = $input['thuisbezorgd-beoordelingen-url'];
	$oldRestaurantURL = get_option('thuisbezorgd-beoordelingen-settings')['thuisbezorgd-beoordelingen-url'];

	// Get the new max review count
	$newMax = $input['thuisbezorgd-beoordelingen-max'];

	// Validate if the new URL is correct, else revert to old
	if(!thuisbezorgd_beoordelingen_isValidThuisbezorgd($newRestaurantURL)) // Does not contain thuisbezorgd.nl/
	{
		add_settings_error(
			'thuisbezorgd-beoordelingen',
			'fout',
			'Voer een geldige Thuisbezorgd restaurant URL in.',
			'error'
		);
		$input['thuisbezorgd-beoordelingen-url'] = $oldRestaurantURL;
	}

	// Validate if the max reviews is valid
	if($newMax < 1 || $newMax > 20) $input['thuisbezorgd-beoordelingen-max'] = 20;
	
	return $input;
}

/* Settings HTML generator */
function thuisbezorgd_beoordelingen_settings_html() {
	// Empty
}

/* URL input HTML generator */
function thuisbezorgd_beoordelingen_urlinput_html() {
	$allOptions = get_option('thuisbezorgd-beoordelingen-settings'); 

	// Get current restaurant URL
	if(isset($allOptions['thuisbezorgd-beoordelingen-url']))
	{
		$currentRestaurantURL = $allOptions['thuisbezorgd-beoordelingen-url'];
	}
	else $currentRestaurantURL = '';

	echo '
		<input name="thuisbezorgd-beoordelingen-settings[thuisbezorgd-beoordelingen-url]" type="url" placeholder="Bijv: https://www.thuisbezorgd.nl/dominos-pizza-amsterdam-buitenveldert" value="'. $currentRestaurantURL .'" required>
		<p class="description">De URL van uw restaurant op Thuisbezorgd. Bijvoorbeeld: <b>https://www.thuisbezorgd.nl/dominos-pizza-amsterdam-buitenveldert</b></p>
	';
}

/* Negative input HTML generator */
function thuisbezorgd_beoordelingen_negativeinput_html() {
	$allOptions = get_option('thuisbezorgd-beoordelingen-settings'); 

	// Get current negative status
	if(isset($allOptions['thuisbezorgd-beoordelingen-negative']))
	{
		if($allOptions['thuisbezorgd-beoordelingen-negative'] == true) {
			$currentNegative = 'checked';
		}
	}
	else $currentNegative = '';

	echo '
		<input name="thuisbezorgd-beoordelingen-settings[thuisbezorgd-beoordelingen-negative]" type="checkbox" '. $currentNegative .'>
		<p class="description">Selecteer deze optie als je negatieve beoordelingen wilt verbergen.</b></p>
	';
}

/* maximum reviews input HTML generator */
function thuisbezorgd_beoordelingen_maxinput_html() {
	$allOptions = get_option('thuisbezorgd-beoordelingen-settings'); 

	// Get current maximum reviews
	if(isset($allOptions['thuisbezorgd-beoordelingen-max']))
	{
		$currentMax = $allOptions['thuisbezorgd-beoordelingen-max'];
	}
	else $currentMax = 20;

	echo '
		<input name="thuisbezorgd-beoordelingen-settings[thuisbezorgd-beoordelingen-max]" type="number" placeholder="20" value="'. $currentMax .'" required min="1" max="20">
		<p class="description">Het maximum aantal beoordelingen dat moet worden weergeven op de pagina. (1-20)</b></p>
	';
}

/* Check if valid thuisbezorgd URL */
function thuisbezorgd_beoordelingen_isValidThuisbezorgd($checkURL) {
	if(!strlen($checkURL)) return false;
	if(!strstr($checkURL, 'https://www.thuisbezorgd.nl')) return false;
	return true;
}

/* Handle shortcode [thuisbezorgd-beoordelingen] */
function thuisbezorgd_beoordelingen_shortcode($attributes){
	return thuisbezorgd_beoordelingen_endReviewsHTML();
}
add_shortcode('thuisbezorgd-beoordelingen', 'thuisbezorgd_beoordelingen_shortcode');

/* Get end HTML for the reviews */
function thuisbezorgd_beoordelingen_endReviewsHTML() {
	$thuisbezorgdURL = get_option('thuisbezorgd-beoordelingen-settings')['thuisbezorgd-beoordelingen-url'];

	// Check if the configured Thuisbezorgd URL is valid
	if(thuisbezorgd_beoordelingen_isValidThuisbezorgd($thuisbezorgdURL)) {
		// Create the reviews URL
		$insertPos = strpos($thuisbezorgdURL, '.nl/') + 4;
		$thuisbezorgdURL = substr_replace($thuisbezorgdURL, 'beoordelingen-', $insertPos, 0);

		// Create context (user agent) to prevent 403 forbidden
		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
				"User-Agent:    Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6\r\n".
				"Cookie: foo=bar\r\n"
			)
		);
		$context = stream_context_create($opts);

		// Get raw HTML using Simple DOM parser and using the context
		$rawHTML = file_get_html($thuisbezorgdURL, false, $context);

		// Begin looping through all review elements found in HTML
		$reviewList = [];

		// Get option if hiding negatives is enabled
		$hideNegatives = get_option('thuisbezorgd-beoordelingen-settings')['thuisbezorgd-beoordelingen-negative'];

		// Get the maximum amount of reviews
		$maxReviews = get_option('thuisbezorgd-beoordelingen-settings')['thuisbezorgd-beoordelingen-max'];

		foreach($rawHTML->find('.restaurantreview') as $element) {
			if(count($reviewList) == $maxReviews) break;

			// Fetch all review information
			$theAuthor = $element->find('.reviewauthor', 0)->first_child()->plaintext;

			$theDate = $element->find('.reviewdate', 0)->plaintext;

			$theQuality = $element->find('.ratingscontainer', 0)->find('.review-rating', 0)->find('.review-stars', 0)->find('span', 0)->style;
			$theQuality = filter_var($theQuality, FILTER_SANITIZE_NUMBER_INT);

			$theDelivery = $element->find('.ratingscontainer', 0)->find('.review-rating', 1)->find('.review-stars', 0)->find('span', 0)->style;
			$theDelivery = filter_var($theDelivery, FILTER_SANITIZE_NUMBER_INT);

			$theBody = $element->find('.reviewbody', 0)->plaintext;

			// Append this review to reviewList
			$newReview = new ThuisbezorgdReview($theAuthor, $theDate, $theQuality, $theDelivery, $theBody);

			// Negative review check
			// If enabled, result must score atleast 60 average to be shown
			if($hideNegatives) {
				if(thuisbezorgd_beoordelingen_review_avg($newReview) < 60) continue;
			}

			array_push($reviewList, $newReview);
		}

		return thuisbezorgd_beoordelingen_reviewsToHTML($reviewList);
	}
	else {
		echo 'Stel een geldige Thuisbezorgd URL in via de instellingen.';
	}
}

/* Get average score of a review */
function thuisbezorgd_beoordelingen_review_avg($reviewOb) {
	$revQuality = $reviewOb->quality;
	$revDelivery = $reviewOb->delivery;

	return ($revQuality + $revDelivery) / 2;
}

/* Convert reviewList array to HTML result */
function thuisbezorgd_beoordelingen_reviewsToHTML($arr) {

		// Enqueue necessary stylesheets for the reviews
		wp_enqueue_style('thuisbezorgd_beoordelingen_reviews');
		wp_enqueue_style('thuisbezorgd_beoordelingen_stars');

		?>
		<!-- Thuisbezorgd header -->
		<div class="thuisbezorgd-beoordelingen-header">
			<img src="<?= plugins_url('img/thuisbezorgd_header.png', __FILE__ ) ?>">
		</div>

		<!-- Display review count -->
		<span class="thuisbezorgd-beoordelingen-reviewcount"><?php 
			$reviewCount = count($arr);
			$reviewPlural = 'beoordelingen';
			if($reviewCount == 1) $reviewPlural = 'beoordeling';

			echo $reviewCount . ' recente ' . $reviewPlural . ' gevonden.';
		?></span>

		<!-- Reviews -->
		<?php
			// Loop through reviews and echo them
			foreach($arr as $review) {

				?>
				<div class="thuisbezorgd-beoordelingen-review">
					<!-- Meta info for review -->
					<div class="thuisbezorgd-beoordelingen-review-meta">
						<!-- Author and date -->
						<b class="thuisbezorgd-beoordelingen-review-meta-author">door <?= $review->author ?></b> <em class="thuisbezorgd-beoordelingen-review-meta-date"><?= $review->date ?></em>
					
						<!-- Ratings -->
						<div class="thuisbezorgd-beoordelingen-meta-ratings">
							<!-- Quality -->
							<div class="thuisbezorgd-beoordelingen-meta-rating">
								<span class="thuisbezorgd-beoordelingen-meta-rating-title">
									Kwaliteit
								</span>
								<div class="star-ratings-sprite">
									<span style="width:<?= $review->quality-1 ?>%" class="star-ratings-sprite-rating"></span>
								</div>
							</div>

							<!-- Delivery -->
							<div class="thuisbezorgd-beoordelingen-meta-rating">
								<span class="thuisbezorgd-beoordelingen-meta-rating-title">
									Bezorging
								</span>
								<div class="star-ratings-sprite">
									<span style="width:<?= $review->delivery-1 ?>%" class="star-ratings-sprite-rating"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="thuisbezorgd-beoordelingen-review-body">
						<?= strlen($review->body) > 1 ? $review->body : 'Geen opmerking.' ?>
					</div>
				</div>
				<?php
			}
		?>

		<!-- Read more reviews -->
		<?php if(count($arr)) { ?>
			<a class="thuisbezorgd-beoordelingen-review-readmore" href="<?= get_option('thuisbezorgd-beoordelingen-settings')['thuisbezorgd-beoordelingen-url'] ?>" target="_blank">Klik hier om meer beoordelingen te bekijken op Thuisbezorgd.nl</a>
		<?php } ?>

	<?php
}

/* Review class */
class ThuisbezorgdReview {
	public $author; // Author name: John L
	public $date; // Date: Vandaag om 18:50
	public $body; // Content of the review
	public $quality; // Quality rating in percentage
	public $delivery; // Delivery rating in percentage

	public function __construct($author, $date, $quality, $delivery, $body) {
		$this->author = $author;
	    $this->date = $date;
	    $this->quality = $quality;
	    $this->delivery = $delivery;
	    $this->body = $body;
	}
}

/* Enqueue reviews CSS */
function thuisbezorgd_beoordelingen_enqueue_reviewscss() {
	wp_register_style('thuisbezorgd_beoordelingen_reviews', plugins_url('css/reviews_1.css', __FILE__ ));
}
add_action('wp_enqueue_scripts', 'thuisbezorgd_beoordelingen_enqueue_reviewscss'); 

/* Enqueue reviews star rating CSS */
function thuisbezorgd_beoordelingen_enqueue_starcss() {
	wp_register_style('thuisbezorgd_beoordelingen_stars', plugins_url('css/reviews_stars.css', __FILE__ ));
}
add_action('wp_enqueue_scripts', 'thuisbezorgd_beoordelingen_enqueue_starcss'); 

/* Enqueue admin stylesheets on the settings page */
function thuisbezorgd_beoordelingen_enqueue_admincss($hook) {
	if($hook == 'thuisbezorgd-beoordelingen/settings/settings.php') {
		wp_register_style('thuisbezorgd_beoordelingen_admin', plugins_url('css/settings.css', __FILE__ ));
		wp_enqueue_style('thuisbezorgd_beoordelingen_admin');
	}
}
add_action('admin_enqueue_scripts', 'thuisbezorgd_beoordelingen_enqueue_admincss' );