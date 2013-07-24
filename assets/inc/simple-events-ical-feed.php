<?php
function se_events_ical() {

// - start collecting output -
ob_start();

// - file header -
header('Content-type: text/calendar');
header('Content-Disposition: attachment; filename="ical.ics"');

// - content header -
?>
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//<?php the_title(); ?>//NONSGML Events //EN
X-WR-CALNAME:<?php the_title(); _e(' - Events','themeforce'); ?>
X-ORIGINAL-URL:<?php echo the_permalink(); ?>
X-WR-CALDESC:<?php the_title(); _e(' - Events','themeforce'); ?>
CALSCALE:GREGORIAN

<?php

$compare = array(
    array(
        'key'     => '_se_event_start_datetime',
        'value'   => time(),
        'compare' => '>=',
        'type'    => 'NUMERIC'
    )
);

$args = array(
    'posts_per_page' => $count,
    'post_type'      => 'se_events',
    'sort'           => 'post_title',
    'order'          => 'ASC',
    'orderby'        => 'meta_value',
    'meta_key'       => '_se_event_start_datetime',
    'meta_query'     => $compare
);

$se_events = get_posts( $args );

// - loop -
if ( $se_events ) {

    global $post;

    foreach ( $se_events as $se_event ) {

        // - custom variables -
        $meta = get_post_custom( $se_event->ID );

        $sd = $meta["_se_event_start_datetime"][0];
        $ed = $meta["_se_event_end_datetime"][0];
        $description = isset($meta["_se_event_description"]) ? $meta["_se_event_description"][0] : 'No event description given.';

        // - grab gmt for start -
        $gmts = date('Y-m-d H:i:s', $sd);
        $gmts = get_gmt_from_date($gmts); // this function requires Y-m-d H:i:s, hence the back & forth.
        $gmts = strtotime($gmts);

        // - grab gmt for end -
        $gmte = date('Y-m-d H:i:s', $ed);
        $gmte = get_gmt_from_date($gmte); // this function requires Y-m-d H:i:s, hence the back & forth.
        $gmte = strtotime($gmte);

        // - Set to UTC ICAL FORMAT -
        $stime = date('Ymd\THis\Z', $gmts);
        $etime = date('Ymd\THis\Z', $gmte);

        // - item output -
        ?>
        BEGIN:VEVENT
        DTSTART:<?php echo $stime; ?>
        DTEND:<?php echo $etime; ?>
        SUMMARY:<?php echo $se_event->post_title; ?>
        DESCRIPTION:<?php echo $description; ?>
        END:VEVENT
        <?php
    }

}

?>
END:VCALENDAR
<?php
// - full output -
$se_events_ical = ob_get_contents();
ob_end_clean();
echo $se_events_ical;
}

function add_se_events_ical_feed() {
    // - add it to WP RSS feeds -
    add_feed('se-events-ical', 'se_events_ical');
}

add_action('init','add_se_events_ical_feed');
?>