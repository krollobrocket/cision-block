<?php

namespace CisionBlock\Plugin\Widget;

abstract class Widget extends \WP_Widget
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(
            $this->name,
            $this->title,
            array(
                'description' => $this->description,
            )
        );

        $this->register();
    }

    /**
     * Register the widget.
     */
    private function register()
    {
        $class = get_called_class();
        add_action('widgets_init', function () use ($class) {
            register_widget($class);
        });
    }

    /**
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        /**
         * Add arguments to widget.
         *
         * @var string $before_widget
         * @var string $before_title
         * @var string $after_title
         * @var string $after_widget
         */
        extract($args);

        echo $before_widget;

        // Display title if not empty.
        if (!empty($instance['title'])) {
            echo $before_title . apply_filters('widgets_title', $instance['title']) . $after_title;
        }

        // Render the widget content.
        $this->render($instance);

        // After widget.
        echo $after_widget;
    }
}
