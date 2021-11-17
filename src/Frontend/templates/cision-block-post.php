<?php

/**
 * Template used to display a single feed item from Cision.
 *
 * @var stdClass $CisionItem
 *   A feed item. A reference of all available fields can be found here: https://websolutions.ne.cision.com/documents/P2_Feed.pdf
 *
 * @package Cision Block
 * @since 2.0.0
 * @version 2.5.0
 */

get_header();
?>
    <div class="wrap">
        <section id="primary" class="content-area">
            <main id="main" class="site-main">
                <article class="entry cision-block-post">
                    <header class="entry-header">
                        <?php echo '<h1 class="entry-title">' . $CisionItem->Title . '</h1>'; ?>
                    </header>

                    <div class="entry-content">
                        <?php echo $CisionItem->HtmlBody; ?>
                    </div><!-- .entry-content -->
                </article>
            </main><!-- #main -->
        </section><!-- #primary -->
    </div><!-- .wrap -->
<?php
get_footer();
