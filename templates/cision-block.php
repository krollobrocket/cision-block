<?php

/**
 * Template used to display a feed from cision.
 *
 * Available variables:
 * - $id: String id of block.
 * - $readmore: Read more button text.
 * - $cision_feed: An array of all feed items.
 * - $pager: Markup for the pager.
 * - $wrapper_attributes: Array of attributes for wrapper element.
 * - $prefix: String prefix to add to start of wrapper.
 * - $suffix: String suffix to add at end of wrapper.
 * - $attributes: Array of attributes for article wrapper element.
 * - $options: Array of options.
 *
 * @package Cision Block
 * @since 1.0
 * @version 1.5.0
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
    <img src="<?php echo esc_url($item->Images[0]->DownloadUrl); ?>" alt="<?php echo esc_html($item->Images[0]->Description); ?>" />
    </span>
    <?php endif; ?>
    <?php print wp_trim_words(esc_html($item->Intro ? $item->Intro : $item->Body)); ?>
    </p>
    <?php if (isset($item->CisionWireUrl, $readmore)) : ?>
    <a href="<?php echo esc_url($item->CisionWireUrl); ?>" target="_blank"><?php _e($readmore, CISION_BLOCK_TEXTDOMAIN); ?></a>
    <?php endif; ?>
  </article>
<?php endforeach; ?>
<?php endif; ?>
<?php echo $pager; ?>
<?php echo $suffix; ?>
</section>
