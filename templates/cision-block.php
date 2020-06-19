<?php

/**
 * Template used to display a feed from Cision.
 *
 * @var string $id
 *   String id of block.
 * @var string $readmore
 *   Read more button text.
 * @var array $cision_feed
 *   An array of all feed items.
 * @var string $pager
 *   Markup for pager.
 * @var array $wrapper_attributes
 *   Array of attributes for wrapper element.
 * @var string $prefix
 *   String prefix to add to start of wrapper.
 * @var string $suffix
 *   String suffix to add to end of wrapper.
 * @var array $attributes
 *   Array of attributes for article wrapper element.
 * @var array $options
 *   Array of options.
 *
 * @package Cision Block
 * @since 1.0
 * @version 2.0.0
 */

?>

<section<?php echo $wrapper_attributes; ?>>
<?php echo $prefix; ?>
<?php if (count($cision_feed)) : ?>
    <?php foreach ($cision_feed as $item) : ?>
    <article<?php echo $attributes; ?>>
        <h2><?php echo esc_html($item->Title); ?></h2>
        <time><?php echo date($options['date_format'], $item->PublishDate); ?></time>
        <p>
        <?php if (isset($item->Images[0])) : ?>
        <span class="cision-feed-item-media">
        <img
                src="<?php echo esc_url($item->Images[0]->DownloadUrl); ?>"
                alt="<?php echo esc_html($item->Images[0]->Description); ?>"
        />
        </span>
        <?php endif; ?>
        <?php print wp_trim_words(esc_html($item->Intro ? $item->Intro : $item->Body)); ?>
        </p>
        <?php if (isset($item->CisionWireUrl, $readmore)) : ?>
        <a
                href="<?php echo esc_url($item->CisionWireUrl); ?>"
                target="<?php echo $item->LinkTarget; ?>"
        ><?php _e($readmore, CISION_BLOCK_TEXTDOMAIN); ?></a>
        <?php endif; ?>
    </article>
    <?php endforeach; ?>
<?php endif; ?>
<?php echo $pager; ?>
<?php echo $suffix; ?>
</section>
