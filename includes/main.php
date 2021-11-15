<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephen
 * Date: 2021-10-20
 * Time: 1:58 PM
 */

namespace ucf_people_directory_external_rss_news\main;

use DateTime;
use DateTimeZone;
use \ucf_people_directory_external_rss_news\simple_html_dom\simple_html_dom;
use \ucf_people_directory_external_rss_news\acf_pro_fields;



add_filter('single-person-news', __NAMESPACE__ . '\\list_person_external_news', 3, 2 );

// add external news, if the editor enabled it for this user
function list_person_external_news( $html_array, \WP_Post $post) {

	$pull_external_news = get_post_meta($post->ID, acf_pro_fields\enable_external, true );

	if ($pull_external_news) {
		$external_news_html = get_rss_feed($post->ID);
		array_push( $html_array, $external_news_html );
    }

	return $html_array;
}


/**
 * Gets the feed for a specific doctor.
 * If the editor specified a full url, uses that.
 * If the editor just specified a slug, assume it's pulling from COM.
 * @param $post_id
 *
 * @return mixed|string
 */
function get_acf_feed_url($post_id) {
    $use_com_news = get_post_meta($post_id, acf_pro_fields\group_container . '_' . acf_pro_fields\group_use_com, true);

    if ($use_com_news) {
        // default true. pull in news from COM rss feed
        $person_tag = get_post_meta($post_id, acf_pro_fields\group_container . '_' . acf_pro_fields\group_com_tag, true);
	    //$news_feed_url = "https://med.ucf.edu/feed/?post_type=news&tag={$person_tag}";
	    $news_feed_url = "https://med.ucf.edu/feed/?post_type=news&post_associated_people={$person_tag}"; // post_associated_people is defined in the college theme. child theme then adds rss parameter

    } else {
        // editor defined a specific RSS feed to pull articles from
        $news_feed_url = get_post_meta($post_id, acf_pro_fields\group_container . '_' . acf_pro_fields\group_external_rss, true);
    }

	return $news_feed_url;

}

/**
 * Fetches the news feed and returns the HTML used to print it out
 * @param $post_id
 *
 * @return false|string
 * @throws \Exception
 */
function get_rss_feed($post_id) {
	$numberArticles = get_post_meta($post_id, acf_pro_fields\group_container . '_' . acf_pro_fields\group_max_articles, true);

	// now grab any ucf health articles
	add_filter( 'wp_feed_cache_transient_lifetime', __NAMESPACE__ . '\\feed_cache_lifetime_in_seconds' ); // refresh every 10 minutes
	$feed = \fetch_feed(get_acf_feed_url($post_id));
	remove_filter( 'wp_feed_cache_transient_lifetime' , __NAMESPACE__ . '\\feed_cache_lifetime_in_seconds' );

	if ( ! is_wp_error( $feed)) {
		$news_posts = [];

		$maxitems   = $feed->get_item_quantity( $numberArticles );
		$feed_items = $feed->get_items( 0, $maxitems );

		foreach ( $feed_items as $item ) {

			/* @var \SimplePie_Item $item */

			/* get thumbnail */
			$htmlDOM = new simple_html_dom();
			$htmlDOM->load( $item->get_content() );

			$image     = $htmlDOM->find( 'img', 0 );
			if ($image) {
				$image_url = $image->src;
			} else {
				$image_url = 'https://ucfhealth.com/wp-content/themes/ucf-health-theme/images/logos/ucf-building.jpg'; // default stock image if image not set
            }
			// remove images for description
			$image->outertext = '';
			$htmlDOM->save();

			$content_minus_image = wp_trim_words( $htmlDOM, '25', '...' ); // these functions are defined in functions.php

			$UTC         = new DateTimeZone( "UTC" );
			$timezoneEST = new DateTimeZone( "America/New_York" );
			$date = new DateTime( $item->get_date(), $UTC );
			$date->setTimezone( $timezoneEST );

			array_push( $news_posts, array(
				'image'     => $image_url,
				'permalink' => $item->get_link(),
				'title'     => $item->get_title(),
				'piece'     => $content_minus_image,
				'date'      => $date->format( 'F d, Y' ),
				'class'     => 'class="prev-img"',
				'target'    => 'target="_blank"'
			) );
		}
		$article_count = 0;
		foreach ($news_posts as $post) {
			$article_count++;
		}
		ob_start();

		// if there are any news articles, print them out with a header
		if ($news_posts) {
			$external_news_html = get_person_external_markup($news_posts);
			?>
			<div class="row" >
				<div class="col-lg" >
					<h2 class="person-subheading mt-5" >In The External News</h2 >
					<?=$external_news_html?>
				</div >
			</div >
		<?php
		}
		return ob_get_clean();
	}
}

/**
 * Number of seconds to cache the feed
 * @return float|int
 */
function feed_cache_lifetime_in_seconds() {
	return 60 * 10; // 10 minutes
}

/**
 * Returns a styled unordered list of posts associated with a person. For use
 * in single-person.php
 *
 * @param $posts array | array of Post objects
 *
 * @return string | publication list HTML
 **@author Jo Dickson
 * @since 1.0.0
 */
function get_person_external_markup( $news_posts ) {
	ob_start();
	if ( $news_posts ):
		?>
		<ul class="list-unstyled" >
			<?php foreach ( $news_posts as $external_post ): ?>
				<li class="mb-md-4" >
					<h3 class="h5" >
						<a href="<?php echo $external_post['permalink']; ?>" >
							<?php echo wptexturize( $external_post['title'] ); ?>
						</a >
					</h3 >
					<div >
						<?php echo $external_post['piece']; ?>
					</div >
				</li >
			<?php endforeach; ?>
		</ul >
	<?php
	endif;

	return ob_get_clean();
}