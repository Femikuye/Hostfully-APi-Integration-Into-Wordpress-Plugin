<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php if (is_singular() && pings_open(get_queried_object())) { ?>
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <?php } ?>

    <?php wp_head(); ?>

    <!-- MailerLite Universal -->
    <script>
        (function(w, d, e, u, f, l, n) {
            w[f] = w[f] || function() {
                    (w[f].q = w[f].q || [])
                    .push(arguments);
                }, l = d.createElement(e), l.async = 1, l.src = u,
                n = d.getElementsByTagName(e)[0], n.parentNode.insertBefore(l, n);
        })
        (window, document, 'script', 'https://assets.mailerlite.com/js/universal.js', 'ml');
        ml('account', '473804');
    </script>
    <!-- End MailerLite Universal -->
    <script type="text/javascript">
        window.Trengo = window.Trengo || {};
        window.Trengo.key = 'zd6CGZxUumHJDO06AU2M';
        (function(d, script, t) {
            script = d.createElement('script');
            script.type = 'text/javascript';
            script.async = true;
            script.src = 'https://cdn.widget.trengo.eu/embed.js';
            d.getElementsByTagName('head')[0].appendChild(script);
        }(document));
    </script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-373561016"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'AW-373561016');
    </script>
</head>

<body <?php body_class(); ?> itemscope itemtype="https://schema.org/WebPage">
    <?php
    // Hook to include additional content after body tag open
    do_action('hendon_action_after_body_tag_open');
    ?>
    <div id="qodef-page-wrapper" class="<?php echo esc_attr(hendon_get_page_wrapper_classes()); ?>">
        <?php
        // Hook to include page header template
        do_action('hendon_action_page_header_template');

        // Hook to include left fixed area
        // do_action('hendon_action_left_fixed_area'); 
        ?>
        <div id="qodef-page-outer">
            <?php
            // Include title template
            // get_template_part('title');

            // Hook to include additional content before page inner content
            do_action('hendon_action_before_page_inner'); ?>
            <div id="qodef-page-inner" class="<?php echo esc_attr(hendon_get_page_inner_classes()); ?>">